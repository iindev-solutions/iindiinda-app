import type { AgalRequest, AgalResponse, AgalRoute } from '../types/agal'

export const useAgalMy = () => {
	const api = useAPI()

	const fetchMyRoutes = async (): Promise<AgalRoute[]> => {
		const res = await api.get<{ success: boolean; data: AgalRoute[] }>('/agal/my/routes')
		return res.data ?? []
	}

	const fetchMyRequests = async (): Promise<AgalRequest[]> => {
		const res = await api.get<{ success: boolean; data: AgalRequest[] }>('/agal/my/requests')
		return res.data ?? []
	}

	const fetchMyResponses = async (): Promise<AgalResponse[]> => {
		const res = await api.get<{ success: boolean; data: AgalResponse[] }>('/agal/my/responses')
		return res.data ?? []
	}

	return { fetchMyRoutes, fetchMyRequests, fetchMyResponses }
}
