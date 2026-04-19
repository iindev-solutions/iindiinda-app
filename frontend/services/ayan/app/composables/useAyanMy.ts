import type { AyanTrip, AyanRequest, AyanResponse } from '../types/ayan'
import { generateMockTrips, generateMockRequests, generateMockResponses, ayanMockDelay } from '../config/ayanMock'
import { USE_MOCK_API } from '~/config/api.config'

export const useAyanMy = () => {
	const api = useAPI()

	const fetchMyTrips = async (): Promise<AyanTrip[]> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return generateMockTrips(3)
		}
		const res = await api.get<{ success: boolean; data: AyanTrip[] }>('/ayan/my/trips')
		return res.data ?? []
	}

	const fetchMyRequests = async (): Promise<AyanRequest[]> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return generateMockRequests(2)
		}
		const res = await api.get<{ success: boolean; data: AyanRequest[] }>('/ayan/my/requests')
		return res.data ?? []
	}

	const fetchMyResponses = async (): Promise<AyanResponse[]> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return generateMockResponses(4)
		}
		const res = await api.get<{ success: boolean; data: AyanResponse[] }>('/ayan/my/responses')
		return res.data ?? []
	}

	return { fetchMyTrips, fetchMyRequests, fetchMyResponses }
}
