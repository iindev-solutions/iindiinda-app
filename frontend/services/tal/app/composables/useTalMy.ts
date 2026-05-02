import type { TalBooking, TalMaster } from '../types/tal'

export const useTalMy = () => {
	const api = useAPI()

	const fetchMyMasters = async (): Promise<TalMaster[]> => {
		const res = await api.get<{ success: boolean; data: TalMaster[] }>('/tal/my/masters')
		return res.data ?? []
	}

	const fetchMyBookings = async (): Promise<TalBooking[]> => {
		const res = await api.get<{ success: boolean; data: TalBooking[] }>('/tal/my/bookings')
		return res.data ?? []
	}

	return { fetchMyMasters, fetchMyBookings }
}
