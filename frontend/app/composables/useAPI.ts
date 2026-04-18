/**
 * useAPI - HTTP клиент с Telegram initData
 * Автоматически прокидывает авторизацию через Telegram WebApp
 */
export const useAPI = () => {
	const config = useRuntimeConfig()
	const { initData } = useTg()

	const baseURL = computed(() => (config.public.apiBase as string) || '/api')

	const headers = computed(() => {
		const h: Record<string, string> = {
			'Content-Type': 'application/json',
			Accept: 'application/json'
		}

		if (initData.value) {
			h['X-Telegram-Init-Data'] = initData.value
		}

		const token = useState<string | null>('auth-token', () => null)
		if (token.value) {
			h.Authorization = `Bearer ${token.value}`
		}

		return h
	})

	const request = async <T>(
		endpoint: string,
		options: {
			method?: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH'
			body?: Record<string, unknown>
			params?: Record<string, string>
		} = {}
	): Promise<T> => {
		const { method = 'GET', body, params } = options

		let url = `${baseURL.value}${endpoint.startsWith('/') ? '' : '/'}${endpoint}`
		if (params) {
			const searchParams = new URLSearchParams(params)
			url += `?${searchParams.toString()}`
		}

		const response = await $fetch<T>(url, {
			method,
			headers: headers.value,
			body: method !== 'GET' ? body : undefined
		})

		return response
	}

	const get = <T>(endpoint: string, params?: Record<string, string>) =>
		request<T>(endpoint, { method: 'GET', params })

	const post = <T>(endpoint: string, body?: Record<string, unknown>) => request<T>(endpoint, { method: 'POST', body })

	const put = <T>(endpoint: string, body?: Record<string, unknown>) => request<T>(endpoint, { method: 'PUT', body })

	const del = <T>(endpoint: string) => request<T>(endpoint, { method: 'DELETE' })

	return { request, get, post, put, del, baseURL, headers }
}
