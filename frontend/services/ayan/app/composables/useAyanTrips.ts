import type { AyanTrip, AyanTripCreate, AyanTripUpdate, AyanFilters } from '../types/ayan'
import { generateMockTrips, findMockTrip, createMockTrip, ayanMockDelay } from '../config/ayanMock'
import { USE_MOCK_API } from '~/config/api.config'

export const useAyanTrips = () => {
	const api = useAPI()

	const fetchTrips = async (filters?: AyanFilters): Promise<AyanTrip[]> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return generateMockTrips(10, filters)
		}
		const params: Record<string, string> = {}
		if (filters?.from) params.from = filters.from
		if (filters?.to) params.to = filters.to
		if (filters?.date) params.date = filters.date
		const res = await api.get<{ success: boolean; data: AyanTrip[] }>('/ayan/trips', params)
		return res.data ?? []
	}

	const fetchTrip = async (id: number): Promise<AyanTrip> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			const trip = findMockTrip(id)
			if (trip) return trip
			throw new Error('Trip not found')
		}
		const res = await api.get<{ success: boolean; data: AyanTrip }>(`/ayan/trips/${id}`)
		return res.data!
	}

	const createTrip = async (data: AyanTripCreate): Promise<AyanTrip> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return createMockTrip(data)
		}
		const res = await api.post<{ success: boolean; data: AyanTrip }>(
			'/ayan/trips',
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	const updateTrip = async (id: number, data: AyanTripUpdate): Promise<AyanTrip> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			const existing = findMockTrip(id)
			if (!existing) throw new Error('Trip not found')
			return { ...existing, ...data, comment: data.comment ?? existing.comment }
		}
		const res = await api.patch<{ success: boolean; data: AyanTrip }>(
			`/ayan/trips/${id}`,
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	return { fetchTrips, fetchTrip, createTrip, updateTrip }
}
