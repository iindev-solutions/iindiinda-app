import type { User, AuthResponse } from '~/types/api'

// ==========================================
// Mock Users — realistic Yakutsk region names
// ==========================================

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

// ==========================================
// AYAN Order Types
// ==========================================

export interface AyaniOrder {
	id: number
	from_address: string
	to_address: string
	price: number
	date: string
	time: string
	seats: number
	status: 'open' | 'accepted' | 'completed' | 'cancelled'
	driver_id: number
	passenger_id: number | null
	created_at: string
	updated_at: string
	driver?: User
	passenger?: User
}

// ==========================================
// Realistic Routes — Yakutsk region
// ==========================================

const CITY_ROUTES = [
	{ from: 'Марха', to: 'Порт', price: 200, seats: 3 },
	{ from: 'Якутск', to: 'Намцы', price: 350, seats: 2 },
	{ from: 'Сайсары', to: 'Республиканская больница', price: 150, seats: 4 },
	{ from: 'Промышленный', to: 'Автовокзал', price: 180, seats: 3 }
]

const INTERCITY_ROUTES = [
	{ from: 'Якутск', to: 'Намцы', price: 400, seats: 3 },
	{ from: 'Якутск', to: 'Мегино', price: 300, seats: 2 },
	{ from: 'Якутск', to: 'Хандыга', price: 1200, seats: 3 },
	{ from: 'Якутск', to: 'Вилюйск', price: 1800, seats: 4 },
	{ from: 'Якутск', to: 'Мирный', price: 2500, seats: 3 },
	{ from: 'Намцы', to: 'Якутск', price: 350, seats: 2 },
	{ from: 'Якутск', to: 'Олекминск', price: 2000, seats: 3 }
]

const TIMES = ['06:00', '07:30', '08:00', '09:15', '10:30', '12:00', '14:00', '16:30', '18:00', '19:30']

function randomDelay(min = 100, max = 400): number {
	return Math.floor(Math.random() * (max - min + 1)) + min
}

function randomDate(daysAhead = 0): string {
	const date = new Date()
	date.setDate(date.getDate() + daysAhead)
	return date.toISOString().split('T')[0] ?? ''
}

function randomTime(): string {
	return TIMES[Math.floor(Math.random() * TIMES.length)] ?? '08:00'
}

// ==========================================
// Mock AYAN Orders
// ==========================================

export function generateMockOrders(count = 10): AyaniOrder[] {
	const orders: AyaniOrder[] = []
	const drivers = MOCK_USERS.filter((u) => u.role === 'driver')

	for (let i = 0; i < count; i++) {
		const isIntercity = Math.random() > 0.6
		const routes = isIntercity ? INTERCITY_ROUTES : CITY_ROUTES
		const routeIdx = Math.floor(Math.random() * routes.length)
		const route = routes[routeIdx]
		const driverIdx = Math.floor(Math.random() * drivers.length)
		const driver = drivers[driverIdx]
		const hoursAgo = Math.floor(Math.random() * 48)

		if (!route || !driver) {
			continue
		}

		orders.push({
			id: 100 + i,
			from_address: route.from,
			to_address: route.to,
			price: route.price + Math.floor(Math.random() * 50),
			date: randomDate(Math.floor(Math.random() * 3)),
			time: randomTime(),
			seats: Math.max(1, route.seats - Math.floor(Math.random() * 2)),
			status: 'open',
			driver_id: driver.id,
			passenger_id: null,
			created_at: new Date(Date.now() - hoursAgo * 3600000).toISOString(),
			updated_at: new Date(Date.now() - hoursAgo * 3600000).toISOString(),
			driver
		})
	}

	return orders.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
}

// ==========================================
// Mock Auth Response
// ==========================================

export function getMockAuthResponse(): AuthResponse {
	const user = MOCK_USERS[Math.floor(Math.random() * 2)]!
	return {
		token: `mock_token_${Date.now()}`,
		user
	}
}

// ==========================================
// Mock User (current)
// ==========================================

export function getMockCurrentUser(): User {
	return MOCK_USERS[0]!
}

// ==========================================
// Mock API Responses with delay simulation
// ==========================================

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
