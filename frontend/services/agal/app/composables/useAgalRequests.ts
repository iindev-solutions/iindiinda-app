import type { AgalFilters, AgalRequest, AgalRequestCreate, AgalRequestUpdate } from '../types/agal'

export const useAgalRequests = () => {
	const api = useAPI()

	const fetchRequests = async (filters?: AgalFilters): Promise<AgalRequest[]> => {
		const params: Record<string, string> = {}
		if (filters?.from) params.from = filters.from
		if (filters?.to) params.to = filters.to
		if (filters?.date) params.date = filters.date

		const res = await api.get<{ success: boolean; data: AgalRequest[] }>('/agal/requests', params)
		return res.data ?? []
	}

	const fetchRequest = async (id: number): Promise<AgalRequest> => {
		const res = await api.get<{ success: boolean; data: AgalRequest }>(`/agal/requests/${id}`)
		return res.data!
	}

	const createRequest = async (data: AgalRequestCreate): Promise<AgalRequest> => {
		const res = await api.post<{ success: boolean; data: AgalRequest }>(
			'/agal/requests',
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	const updateRequest = async (id: number, data: AgalRequestUpdate): Promise<AgalRequest> => {
		const res = await api.patch<{ success: boolean; data: AgalRequest }>(
			`/agal/requests/${id}`,
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	return { fetchRequests, fetchRequest, createRequest, updateRequest }
}
