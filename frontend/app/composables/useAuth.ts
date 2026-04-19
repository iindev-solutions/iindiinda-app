import type { User, AuthResponse, Role } from '~/types/api'

export const useAuth = () => {
	const { initData, isInTelegram, user: tgUser } = useTg()
	const api = useAPI()
	const config = useRuntimeConfig()

	const token = useState<string | null>('auth-token', () => null)
	const user = useState<User | null>('auth-user', () => null)
	const isAuthenticated = computed(() => !!token.value)
	const isLoading = ref(false)

	// TMA mode: auto-login with initData
	const loginWithInitData = async () => {
		if (!initData.value) throw new Error('No initData')

		isLoading.value = true
		try {
			const response = await api.post<AuthResponse>('/auth/telegram', {
				init_data: initData.value
			})
			token.value = response.token
			user.value = response.user
		} catch (error) {
			console.error('[useAuth] Login with initData failed:', error)
			throw error
		} finally {
			isLoading.value = false
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
		isLoading.value = true
		try {
			const response = await api.post<AuthResponse>('/auth/telegram/oauth', { hash })
			token.value = response.token
			user.value = response.user
			await navigateTo('/')
		} catch (error) {
			console.error('[useAuth] OAuth callback failed:', error)
			throw error
		} finally {
			isLoading.value = false
		}
	}

	// Unified login (auto-detect mode)
	const login = async () => {
		if (isInTelegram.value) {
			await loginWithInitData()
		} else {
			loginWithOAuth()
		}
	}

	const logout = () => {
		token.value = null
		user.value = null
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
