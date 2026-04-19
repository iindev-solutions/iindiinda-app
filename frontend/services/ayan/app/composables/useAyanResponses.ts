import type { AyanResponse, AyanResponseCreate } from '../types/ayan'
import { generateMockResponses, createMockResponse, ayanMockDelay } from '../config/ayanMock'
import { USE_MOCK_API } from '~/config/api.config'

export const useAyanResponses = () => {
	const api = useAPI()

	const fetchTripResponses = async (tripId: number): Promise<AyanResponse[]> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return generateMockResponses(2)
		}
		const res = await api.get<{ success: boolean; data: AyanResponse[] }>(`/ayan/trips/${tripId}/responses`)
		return res.data ?? []
	}

	const fetchRequestResponses = async (requestId: number): Promise<AyanResponse[]> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return generateMockResponses(2)
		}
		const res = await api.get<{ success: boolean; data: AyanResponse[] }>(`/ayan/requests/${requestId}/responses`)
		return res.data ?? []
	}

	const createTripResponse = async (tripId: number, data?: AyanResponseCreate): Promise<AyanResponse> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return createMockResponse(data?.message)
		}
		const res = await api.post<{ success: boolean; data: AyanResponse }>(
			`/ayan/trips/${tripId}/responses`,
			data as Record<string, unknown>
		)
		return res.data!
	}

	const createRequestResponse = async (requestId: number, data?: AyanResponseCreate): Promise<AyanResponse> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return createMockResponse(data?.message)
		}
		const res = await api.post<{ success: boolean; data: AyanResponse }>(
			`/ayan/requests/${requestId}/responses`,
			data as Record<string, unknown>
		)
		return res.data!
	}

	const cancelResponse = async (id: number): Promise<void> => {
		if (USE_MOCK_API) {
			await ayanMockDelay()
			return
		}
		await api.del(`/ayan/responses/${id}`)
	}

	return { fetchTripResponses, fetchRequestResponses, createTripResponse, createRequestResponse, cancelResponse }
}
