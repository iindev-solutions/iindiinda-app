import type { User } from '~/types/api'

export interface AuthResponse {
	token: string
	user: User
}

export const useAuth = () => {
	const { initData, user: tgUser } = useTg()
	const api = useAPI()

	const token = useState<string | null>('auth-token', () => null)
	const user = useState<User | null>('auth-user', () => null)
	const isAuthenticated = computed(() => !!token.value)
	const isLoading = ref(false)

	const login = async () => {
		if (!initData.value) return

		isLoading.value = true
		try {
			const response = await api.post<AuthResponse>('/auth/telegram', {
				init_data: initData.value
			})
			token.value = response.token
			user.value = response.user
		} catch (error) {
			console.error('[useAuth] Login failed:', error)
			throw error
		} finally {
			isLoading.value = false
		}
	}

	const logout = () => {
		token.value = null
		user.value = null
	}

	const switchRole = async (role: 'passenger' | 'driver') => {
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
		logout,
		switchRole
	}
}
