export interface TimeSlot {
	id: string
	time: string
	available: boolean
	date: string
}

export interface Master {
	id: string
	name: string
	avatar?: string
	specialization: string
	rating: number
	reviewCount: number
}

export interface Service {
	id: string
	name: string
	duration: number
	price: number
}

export interface Booking {
	serviceId: string
	masterId: string
	timeSlotId: string
	date: string
	time: string
	status: 'pending' | 'confirmed' | 'cancelled'
}

export interface TalState {
	selectedService: Service | null
	selectedMaster: Master | null
	selectedDate: string | null
	selectedTimeSlot: TimeSlot | null
	availableSlots: TimeSlot[]
	masters: Master[]
	services: Service[]
	currentBooking: Booking | null
	isLoading: boolean
}

export const useTalStore = () => {
	const state = useState<TalState>('tal-store', () => ({
		selectedService: null,
		selectedMaster: null,
		selectedDate: null,
		selectedTimeSlot: null,
		availableSlots: [],
		masters: [],
		services: [],
		currentBooking: null,
		isLoading: false
	}))

	const selectService = (service: Service) => {
		state.value.selectedService = service
		state.value.selectedMaster = null
		state.value.selectedTimeSlot = null
	}

	const selectMaster = (master: Master) => {
		state.value.selectedMaster = master
		state.value.selectedTimeSlot = null
	}

	const selectDate = (date: string) => {
		state.value.selectedDate = date
		state.value.selectedTimeSlot = null
	}

	const selectTimeSlot = (slot: TimeSlot) => {
		state.value.selectedTimeSlot = slot
	}

	const resetSelection = () => {
		state.value.selectedService = null
		state.value.selectedMaster = null
		state.value.selectedDate = null
		state.value.selectedTimeSlot = null
		state.value.currentBooking = null
	}

	const setLoading = (loading: boolean) => {
		state.value.isLoading = loading
	}

	return {
		state: readonly(state),
		selectService,
		selectMaster,
		selectDate,
		selectTimeSlot,
		resetSelection,
		setLoading
	}
}
