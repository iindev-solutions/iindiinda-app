/**
 * iind Backend API Types
 * Описание моделей и ответов для всех сервисов
 */

// ==========================================
// Общие
// ==========================================

export interface ApiError {
	message: string
	errors?: Record<string, string[]>
}

export interface PaginatedResponse<T> {
	data: T[]
	meta: {
		current_page: number
		last_page: number
		per_page: number
		total: number
	}
}

// ==========================================
// Auth & User
// ==========================================

export interface User {
	id: number
	telegram_id: number
	username: string | null
	first_name: string
	role: 'passenger' | 'driver'
	created_at: string
	updated_at: string
}

export interface AuthResponse {
	token: string
	user: User
}

// ==========================================
// AYAN (Бардыбыт / Такси)
// ==========================================

export type TaxiOrderStatus = 'open' | 'accepted' | 'completed' | 'cancelled'

export interface TaxiOrder {
	id: number
	passenger_id: number
	driver_id: number | null
	from_address: string
	to_address: string
	/** Цена в рублях. Валидация: min 100, max 5000 */
	price: number
	status: TaxiOrderStatus
	passenger?: User
	driver?: User
	created_at: string
	updated_at: string
}

export interface CreateTaxiOrderRequest {
	from_address: string
	to_address: string
	/** min: 100, max: 5000 */
	price: number
}

/**
 * Статусы заказов:
 * open -> accepted -> completed
 * open -> cancelled
 * accepted -> cancelled (только пассажир)
 */

// ==========================================
// TAL (Бронирование)
// ==========================================

export interface TalService {
	id: string
	name: string
	/** Длительность в минутах */
	duration: number
	/** Цена в рублях */
	price: number
}

export interface TalMaster {
	id: string
	name: string
	avatar?: string
	specialization: string
	rating: number
	review_count: number
}

export interface TalTimeSlot {
	id: string
	time: string
	available: boolean
	date: string
}

export type BookingStatus = 'pending' | 'confirmed' | 'cancelled'

export interface TalBooking {
	id: string
	service_id: string
	master_id: string
	time_slot_id: string
	date: string
	time: string
	status: BookingStatus
}

export interface CreateBookingRequest {
	service_id: string
	master_id: string
	time_slot_id: string
	date: string
}

// ==========================================
// UUS (Мастера)
// ==========================================

export type TaskStatus = 'open' | 'in_progress' | 'completed' | 'cancelled'

export interface UusTask {
	id: number
	user_id: number
	title: string
	description: string
	category: string
	budget: number | null
	status: TaskStatus
	responses_count: number
	created_at: string
}

export interface CreateTaskRequest {
	title: string
	description: string
	category: string
	budget?: number
}

export interface UusResponse {
	id: number
	task_id: number
	master_id: number
	message: string
	price: number
	master?: User
	created_at: string
}

// ==========================================
// AGAL (Доставка)
// ==========================================

export type ParcelStatus = 'looking_for_carrier' | 'in_transit' | 'delivered' | 'cancelled'

export interface AgalParcel {
	id: number
	sender_id: number
	carrier_id: number | null
	from_city: string
	to_city: string
	weight_kg: number
	description: string
	status: ParcelStatus
	sender?: User
	carrier?: User
	created_at: string
}

export interface CreateParcelRequest {
	from_city: string
	to_city: string
	weight_kg: number
	description: string
}
