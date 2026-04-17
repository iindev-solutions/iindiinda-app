/**
 * useAuth - авторизация через Telegram
 * Работает с backend через POST /api/auth/telegram
 */
export const useAuth = () => {
	const { initData, user: tgUser } = useTg()
	const api = useAPI()

	const token = useState<string | null>('auth-token', () => null)
	const user = useState<AuthUser | null>('auth-user', () => null)
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
		} finally {
			isLoading.value = false
		}
	}

	const logout = () => {
		token.value = null
		user.value = null
	}

	const switchRole = async (role: 'passenger' | 'driver') => {
		const response = await api.post<{ user: AuthUser }>('/user/switch-role', { role })
		user.value = response.user
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

// --- Типы ---
export interface AuthUser {
	id: number
	telegram_id: number
	username: string | null
	first_name: string
	role: 'passenger' | 'driver'
	created_at: string
	updated_at: string
}

export interface AuthResponse {
	token: string
	user: AuthUser
}
