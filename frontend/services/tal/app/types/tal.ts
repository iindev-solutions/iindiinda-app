export type TalCategory = 'beauty' | 'home' | 'repair'
export type TalAvailabilityStatus = 'now' | 'later' | 'tomorrow' | 'busy'
export type TalMasterStatus = 'open' | 'matched' | 'completed' | 'cancelled'
export type TalBookingStatus = 'pending' | 'accepted' | 'rejected'

export interface TalUserSummary {
	id: number
	name: string
	username: string | null
}

export interface TalMaster {
	id: number
	master: TalUserSummary
	category: TalCategory
	service_label: string
	description: string
	location: string
	availability_status: TalAvailabilityStatus
	available_note: string | null
	price_from: number | null
	status: TalMasterStatus
	created_at: string
}

export interface TalBooking {
	id: number
	tal_master_id: number
	tal_master: TalMaster | null
	user: TalUserSummary
	message: string | null
	desired_time: string | null
	status: TalBookingStatus
	created_at: string
}

export interface TalFilters {
	category?: string
	availability_status?: string
	location?: string
}

export interface TalMasterCreate {
	category: TalCategory
	service_label: string
	description: string
	location: string
	availability_status: TalAvailabilityStatus
	available_note?: string | null
	price_from?: number | null
}

export interface TalMasterUpdate extends Partial<TalMasterCreate> {
	status?: TalMasterStatus
}

export interface TalBookingCreate {
	message?: string | null
	desired_time?: string | null
}
