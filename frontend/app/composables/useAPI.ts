import { USE_MOCK_API } from '~/config/api.config'
import { mockApiResponses, getMockAuthResponse, getMockCurrentUser } from '~/config/mockData'

export const useAPI = () => {
	const config = useRuntimeConfig()
	const { initData } = useTg()
	const token = useState<string | null>('auth-token', () => null)

	const baseURL = computed(() => (config.public.apiBase as string) || '/api')

	const headers = computed(() => {
		const h: Record<string, string> = {
			Accept: 'application/json'
		}

		if (initData.value) {
			h['X-Telegram-Init-Data'] = initData.value
		}

		if (token.value) {
			h['Authorization'] = `Bearer ${token.value}`
		}

		return h
	})

	// ==========================================
	// Real API Request
	// ==========================================

	const realRequest = async <T>(
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

		const reqHeaders: Record<string, string> = { ...headers.value }
		if (method !== 'GET' && body) {
			reqHeaders['Content-Type'] = 'application/json'
		}

		const response = await $fetch<T>(url, {
			method,
			headers: reqHeaders,
			body: method !== 'GET' ? body : undefined
		})

		return response
	}

	// ==========================================
	// Mock API Request
	// ==========================================

	const mockRequest = async <T>(
		endpoint: string,
		options: {
			method?: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH'
			body?: Record<string, unknown>
			params?: Record<string, string>
		} = {}
	): Promise<T> => {
		// Simulate network delay
		await new Promise((resolve) => setTimeout(resolve, 200 + Math.random() * 300))

		// Auth endpoints
		if (endpoint === '/auth/telegram') {
			return mockApiResponses.post(getMockAuthResponse()) as Promise<T>
		}

		// User endpoints
		if (endpoint === '/user') {
			return mockApiResponses.get(getMockCurrentUser()) as Promise<T>
		}

		if (endpoint === '/user/switch-role') {
			const currentUser = getMockCurrentUser()
			const newRole = (options.body?.role as string) || currentUser.role
			return mockApiResponses.post({
				user: { ...currentUser, role: newRole }
			}) as Promise<T>
		}

		// AYAN endpoints are handled by AYAN layer composables (useAyanTrips, useAyanRequests, etc.)
		// This mock handler only covers auth and user endpoints

		// Default: return empty array or null
		if (options.method === 'GET') {
			return [] as unknown as Promise<T>
		}
		return {} as unknown as Promise<T>
	}

	// ==========================================
	// Unified Request
	// ==========================================

	const request = async <T>(
		endpoint: string,
		options: {
			method?: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH'
			body?: Record<string, unknown>
			params?: Record<string, string>
		} = {}
	): Promise<T> => {
		if (USE_MOCK_API) {
			return mockRequest<T>(endpoint, options)
		}
		return realRequest<T>(endpoint, options)
	}

	const get = <T>(endpoint: string, params?: Record<string, string>) =>
		request<T>(endpoint, { method: 'GET', params })

	const post = <T>(endpoint: string, body?: Record<string, unknown>) => request<T>(endpoint, { method: 'POST', body })

	const put = <T>(endpoint: string, body?: Record<string, unknown>) => request<T>(endpoint, { method: 'PUT', body })

	const patch = <T>(endpoint: string, body?: Record<string, unknown>) =>
		request<T>(endpoint, { method: 'PATCH', body })

	const del = <T>(endpoint: string) => request<T>(endpoint, { method: 'DELETE' })

	return { request, get, post, put, patch, del, baseURL, headers, token }
}
