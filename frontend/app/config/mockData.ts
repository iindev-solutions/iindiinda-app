import type { User, AuthResponse } from '~/types/api'

export const MOCK_USERS: User[] = [
	{
		id: 1,
		telegram_id: 123456789,
		username: 'vasya_driver',
		first_name: 'Вася',
		role: 'driver',
		rating: 4.8,
		completed_orders: 45,
		is_available: true,
		created_at: '2025-01-15T10:00:00Z',
		updated_at: '2026-04-18T06:00:00Z'
	},
	{
		id: 2,
		telegram_id: 987654321,
		username: 'petya_kuznetsov',
		first_name: 'Пётр',
		role: 'driver',
		rating: 4.5,
		completed_orders: 32,
		is_available: true,
		created_at: '2025-03-20T14:30:00Z',
		updated_at: '2026-04-18T08:00:00Z'
	},
	{
		id: 3,
		telegram_id: 555666777,
		username: 'ivan_nikolaev',
		first_name: 'Иван',
		role: 'driver',
		rating: 4.9,
		completed_orders: 78,
		is_available: true,
		created_at: '2024-11-05T09:15:00Z',
		updated_at: '2026-04-17T22:00:00Z'
	},
	{
		id: 4,
		telegram_id: 111222333,
		username: null,
		first_name: 'Мария',
		role: 'passenger',
		rating: 5.0,
		completed_orders: 12,
		is_available: false,
		created_at: '2025-06-10T16:45:00Z',
		updated_at: '2026-04-18T07:30:00Z'
	},
	{
		id: 5,
		telegram_id: 444555666,
		username: 'anna_smirnova',
		first_name: 'Анна',
		role: 'passenger',
		rating: 4.7,
		completed_orders: 8,
		is_available: false,
		created_at: '2025-09-01T11:20:00Z',
		updated_at: '2026-04-16T15:00:00Z'
	}
]

function randomDelay(min = 100, max = 400): number {
	return Math.floor(Math.random() * (max - min + 1)) + min
}

export function getMockAuthResponse(): AuthResponse {
	const user = MOCK_USERS[Math.floor(Math.random() * 2)]!
	return {
		token: `mock_token_${Date.now()}`,
		user
	}
}

export function getMockCurrentUser(): User {
	return MOCK_USERS[0]!
}

export const mockApiResponses = {
	async get<T>(data: T, delay?: number): Promise<T> {
		await new Promise((resolve) => setTimeout(resolve, delay || randomDelay()))
		return data
	},

	async post<T>(data: T, delay?: number): Promise<T> {
		await new Promise((resolve) => setTimeout(resolve, delay || randomDelay()))
		return data
	},

	async error(message: string, status = 400): Promise<{ message: string; status: number }> {
		await new Promise((resolve) => setTimeout(resolve, randomDelay()))
		const error = new Error(message) as any
		error.status = status
		throw error
	}
}
