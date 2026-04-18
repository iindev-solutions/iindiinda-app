/**
 * iind Backend API Types — Foundation
 *
 * Базовые типы, используемые ВСЕМИ сервисами.
 * Сервис-специфичные типы находятся в service layers:
 * - services/ayan/app/types/
 * - services/uus/app/types/
 * - services/tal/app/types/
 * - services/agal/app/types/
 */

// ==========================================
// Общие / Base
// ==========================================

export interface ApiResponse<T> {
	success: boolean
	data?: T
	message?: string
	errors?: Record<string, string[]>
}

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
	role: Role
	rating?: number
	completed_orders?: number
	is_available?: boolean
	created_at: string
	updated_at: string
}

export type Role = 'passenger' | 'driver' | 'carrier' | 'master' | 'sender'

export interface AuthResponse {
	token: string
	user: User
}

// ==========================================
// Service-specific types
// See: services/{service}/app/types/
// ==========================================
