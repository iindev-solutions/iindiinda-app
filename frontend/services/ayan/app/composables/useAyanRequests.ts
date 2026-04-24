import type { AyanRequest, AyanRequestCreate, AyanRequestUpdate, AyanFilters } from '../types/ayan'
import { generateMockRequests, findMockRequest, createMockRequest, ayanMockDelay } from '../config/ayanMock'
import { USE_MOCK_API } from '~/config/api.config'

export const useAyanRequests = () => {
	const api = useAPI()

	const fetchRequests = async (filters?: AyanFilters): Promise<AyanRequest[]> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return generateMockRequests(6, filters)
		}
		const params: Record<string, string> = {}
		if (filters?.from) params.from = filters.from
		if (filters?.to) params.to = filters.to
		if (filters?.date) params.date = filters.date
		const res = await api.get<{ success: boolean; data: AyanRequest[] }>('/ayan/requests', params)
		return res.data ?? []
	}

	const fetchRequest = async (id: number): Promise<AyanRequest> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			const request = findMockRequest(id)
			if (request) return request
			throw new Error('Request not found')
		}
		const res = await api.get<{ success: boolean; data: AyanRequest }>(`/ayan/requests/${id}`)
		return res.data!
	}

	const createRequest = async (data: AyanRequestCreate): Promise<AyanRequest> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return createMockRequest(data)
		}
		const res = await api.post<{ success: boolean; data: AyanRequest }>(
			'/ayan/requests',
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	const updateRequest = async (id: number, data: AyanRequestUpdate): Promise<AyanRequest> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			const existing = findMockRequest(id)
			if (!existing) throw new Error('Request not found')
			return {
				...existing,
				...data,
				description: data.description ?? existing.description,
				time: data.time ?? existing.time
			}
		}
		const res = await api.patch<{ success: boolean; data: AyanRequest }>(
			`/ayan/requests/${id}`,
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	return { fetchRequests, fetchRequest, createRequest, updateRequest }
}
