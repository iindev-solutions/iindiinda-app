import type { TalBooking, TalBookingCreate, TalBookingStatus } from '../types/tal'

export const useTalBookings = () => {
	const api = useAPI()

	const fetchMasterBookings = async (masterId: number): Promise<TalBooking[]> => {
		const res = await api.get<{ success: boolean; data: TalBooking[] }>(`/tal/masters/${masterId}/bookings`)
		return res.data ?? []
	}

	const createMasterBooking = async (masterId: number, data?: TalBookingCreate): Promise<TalBooking> => {
		const res = await api.post<{ success: boolean; data: TalBooking }>(
			`/tal/masters/${masterId}/bookings`,
			(data ?? {}) as Record<string, unknown>
		)
		return res.data!
	}

	const updateBookingStatus = async (id: number, status: TalBookingStatus): Promise<TalBooking> => {
		const res = await api.patch<{ success: boolean; data: TalBooking }>(`/tal/bookings/${id}`, { status })
		return res.data!
	}

	const cancelBooking = async (id: number): Promise<void> => {
		await api.del(`/tal/bookings/${id}`)
	}

	return { fetchMasterBookings, createMasterBooking, updateBookingStatus, cancelBooking }
}
