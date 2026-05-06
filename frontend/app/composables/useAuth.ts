import type { User, AuthResponse, Role } from '~/types/api'

import { USE_MOCK_API } from '~/config/api.config'
import { canUseDevInitData } from '~/utils/auth'
import { getTelegramWebApp, waitForTelegramInitData } from '~/utils/telegram'

export const useAuth = () => {
	const { initData, user: tgUser } = useTg()
	const api = useAPI()
	const config = useRuntimeConfig()

	const token = useState<string | null>('auth-token', () => null)
	const user = useState<User | null>('auth-user', () => null)
	const authStatus = useState<'idle' | 'loading' | 'authenticated' | 'failed'>('auth-status', () =>
		token.value ? 'authenticated' : 'idle'
	)
	const authError = useState<string | null>('auth-error', () => null)
	const isAuthenticated = computed(() => !!token.value && !!user.value)
	const isLoading = computed(() => authStatus.value === 'loading')

	// TMA mode: auto-login with initData
	const loginWithInitData = async (overrideInitData?: string) => {
		const payload = overrideInitData || initData.value

		if (!payload) throw new Error('No initData')

		authStatus.value = 'loading'
		authError.value = null
		try {
			const response = USE_MOCK_API
				? await api.post<AuthResponse>('/auth/telegram', { init_data: payload })
				: await (() => {
						const formData = new URLSearchParams()
						formData.set('init_data', payload)

						return $fetch<AuthResponse>(`${api.baseURL.value}/auth/telegram`, {
							method: 'POST',
							headers: {
								...api.headers.value,
								'Content-Type': 'application/x-www-form-urlencoded'
							},
							body: formData.toString()
						})
					})()
			token.value = response.token
			user.value = response.user
			authStatus.value = 'authenticated'
		} catch (error) {
			authStatus.value = 'failed'
			authError.value = error instanceof Error ? error.message : 'Auth failed'
			console.error('[useAuth] Login with initData failed:', error)
			throw error
		}
	}

	// Browser mode: OAuth redirect
	const loginWithOAuth = () => {
		const botId = config.public.telegramBotId as string
		if (!botId) {
			console.error('[useAuth] NUXT_PUBLIC_TELEGRAM_BOT_ID not configured')
			throw new Error('Telegram Bot ID not configured')
		}

		const origin = window.location.origin
		const redirectUrl = `${origin}/auth/callback`

		window.location.href = `https://oauth.telegram.org/auth?bot_id=${botId}&origin=${origin}&request_access=write&return_to=${redirectUrl}`
	}

	// OAuth callback handler
	const handleOAuthCallback = async (hash: string) => {
		authStatus.value = 'loading'
		authError.value = null
		try {
			const response = await api.post<AuthResponse>('/auth/telegram/oauth', { hash })
			token.value = response.token
			user.value = response.user
			authStatus.value = 'authenticated'
			await navigateTo('/')
		} catch (error) {
			authStatus.value = 'failed'
			authError.value = error instanceof Error ? error.message : 'Auth failed'
			console.error('[useAuth] OAuth callback failed:', error)
			throw error
		}
	}

	// Unified login (auto-detect mode)
	const login = async () => {
		const telegramWebApp = getTelegramWebApp()

		if (telegramWebApp) {
			authStatus.value = 'loading'
			authError.value = null

			const telegramInitData = telegramWebApp.initData || (await waitForTelegramInitData())

			if (!telegramInitData) {
				authStatus.value = 'failed'
				authError.value = 'Telegram initData is unavailable'
				throw new Error('Telegram initData is unavailable')
			}

			await loginWithInitData(telegramInitData)
			return
		}

		const devInitData = config.public.devInitData as string
		if (canUseDevInitData(window.location.hostname, devInitData, import.meta.dev)) {
			console.warn('[useAuth] Using dev initData fallback for browser testing')
			await loginWithInitData(devInitData)
			return
		}

		// Browser mode stays unauthenticated until real OAuth / verification flow is implemented.
		authStatus.value = 'idle'
		authError.value = null
		console.warn('[useAuth] Browser login is disabled until Telegram OAuth is wired end-to-end')
	}

	const logout = () => {
		token.value = null
		user.value = null
		authStatus.value = 'idle'
		authError.value = null
	}

	const switchRole = async (role: Role) => {
		try {
			const response = await api.post<{ user: User }>('/user/switch-role', { role })
			user.value = response.user
		} catch (error) {
			console.error('[useAuth] Switch role failed:', error)
			throw error
		}
	}

	return {
		token: readonly(token),
		user: readonly(user),
		tgUser,
		authStatus: readonly(authStatus),
		authError: readonly(authError),
		isAuthenticated,
		isLoading: readonly(isLoading),
		login,
		loginWithInitData,
		loginWithOAuth,
		handleOAuthCallback,
		logout,
		switchRole
	}
}
