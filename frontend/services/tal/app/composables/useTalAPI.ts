import type { Service, Master, TimeSlot, Booking } from './useTalStore'

export const useTalAPI = () => {
	const fetchServices = async (): Promise<Service[]> => {
		return []
	}

	const fetchMasters = async (serviceId: string): Promise<Master[]> => {
		return []
	}

	const fetchAvailableSlots = async (masterId: string, date: string): Promise<TimeSlot[]> => {
		return []
	}

	const createBooking = async (booking: {
		serviceId: string
		masterId: string
		timeSlotId: string
		date: string
	}): Promise<Booking> => {
		return {
			...booking,
			time: '',
			status: 'pending'
		}
	}

	const cancelBooking = async (bookingId: string): Promise<void> => {
		return
	}

	return {
		fetchServices,
		fetchMasters,
		fetchAvailableSlots,
		createBooking,
		cancelBooking
	}
}
