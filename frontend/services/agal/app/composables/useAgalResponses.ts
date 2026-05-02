import type { AgalResponse, AgalResponseCreate, AgalResponseStatus } from '../types/agal'

export const useAgalResponses = () => {
	const api = useAPI()

	const fetchRouteResponses = async (routeId: number): Promise<AgalResponse[]> => {
		const res = await api.get<{ success: boolean; data: AgalResponse[] }>(`/agal/routes/${routeId}/responses`)
		return res.data ?? []
	}

	const fetchRequestResponses = async (requestId: number): Promise<AgalResponse[]> => {
		const res = await api.get<{ success: boolean; data: AgalResponse[] }>(`/agal/requests/${requestId}/responses`)
		return res.data ?? []
	}

	const createRouteResponse = async (routeId: number, data?: AgalResponseCreate): Promise<AgalResponse> => {
		const res = await api.post<{ success: boolean; data: AgalResponse }>(
			`/agal/routes/${routeId}/responses`,
			(data ?? {}) as Record<string, unknown>
		)
		return res.data!
	}

	const createRequestResponse = async (requestId: number, data?: AgalResponseCreate): Promise<AgalResponse> => {
		const res = await api.post<{ success: boolean; data: AgalResponse }>(
			`/agal/requests/${requestId}/responses`,
			(data ?? {}) as Record<string, unknown>
		)
		return res.data!
	}

	const updateResponseStatus = async (id: number, status: AgalResponseStatus): Promise<AgalResponse> => {
		const res = await api.patch<{ success: boolean; data: AgalResponse }>(`/agal/responses/${id}`, { status })
		return res.data!
	}

	const cancelResponse = async (id: number): Promise<void> => {
		await api.del(`/agal/responses/${id}`)
	}

	return {
		fetchRouteResponses,
		fetchRequestResponses,
		createRouteResponse,
		createRequestResponse,
		updateResponseStatus,
		cancelResponse
	}
}
