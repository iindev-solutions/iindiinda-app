import { MOCK_USERS } from '~/config/mockData'
import type {
	AyanTrip,
	AyanTripDriver,
	AyanRequest,
	AyanRequestPassenger,
	AyanResponse,
	AyanResponseUser,
	AyanTripCreate,
	AyanRequestCreate,
	AyanFilters
} from '../types/ayan'

const CITY_ROUTES = [
	{ from: 'Марха', to: 'Порт' },
	{ from: 'Якутск', to: 'Намцы' },
	{ from: 'Сайсары', to: 'Республиканская больница' },
	{ from: 'Промышленный', to: 'Автовокзал' }
]

const INTERCITY_ROUTES = [
	{ from: 'Якутск', to: 'Намцы' },
	{ from: 'Якутск', to: 'Мегино' },
	{ from: 'Якутск', to: 'Хандыга' },
	{ from: 'Якутск', to: 'Вилюйск' },
	{ from: 'Якутск', to: 'Мирный' },
	{ from: 'Намцы', to: 'Якутск' },
	{ from: 'Якутск', to: 'Олекминск' }
]

const TIMES = ['06:00', '07:30', '08:00', '09:15', '10:30', '12:00', '14:00', '16:30', '18:00', '19:30']

function randomDate(daysAhead = 0): string {
	const date = new Date()
	date.setDate(date.getDate() + daysAhead)
	return date.toISOString().split('T')[0] ?? ''
}

function randomTime(): string {
	return TIMES[Math.floor(Math.random() * TIMES.length)] ?? '08:00'
}

function randomDelay(min = 100, max = 400): number {
	return Math.floor(Math.random() * (max - min + 1)) + min
}

const mockDrivers: AyanTripDriver[] = MOCK_USERS.filter((u) => u.role === 'driver').map((u) => ({
	id: u.id,
	name: u.first_name,
	username: u.username
}))

const mockPassengers: AyanRequestPassenger[] = MOCK_USERS.filter((u) => u.role === 'passenger').map((u) => ({
	id: u.id,
	name: u.first_name,
	username: u.username
}))

const mockResponseUsers: AyanResponseUser[] = MOCK_USERS.slice(0, 3).map((u) => ({
	id: u.id,
	name: u.first_name,
	username: u.username
}))

export function generateMockTrips(count = 10, filters?: AyanFilters): AyanTrip[] {
	const trips: AyanTrip[] = []

	for (let i = 0; i < count; i++) {
		const isIntercity = Math.random() > 0.6
		const routes = isIntercity ? INTERCITY_ROUTES : CITY_ROUTES
		const route = routes[Math.floor(Math.random() * routes.length)]
		const driver = mockDrivers[Math.floor(Math.random() * mockDrivers.length)]
		const hoursAgo = Math.floor(Math.random() * 48)

		if (!route || !driver) continue

		const from_address = route.from
		const to_address = route.to
		const date = randomDate(Math.floor(Math.random() * 3))

		if (filters?.from && from_address !== filters.from) continue
		if (filters?.to && to_address !== filters.to) continue
		if (filters?.date && date !== filters.date) continue

		trips.push({
			id: 100 + i,
			driver,
			from_address,
			to_address,
			date,
			time: randomTime(),
			seats: Math.max(1, Math.floor(Math.random() * 4) + 1),
			price: (isIntercity ? 800 : 150) + Math.floor(Math.random() * 500),
			comment: Math.random() > 0.7 ? 'Звоните заранее' : null,
			status: 'open',
			created_at: new Date(Date.now() - hoursAgo * 3600000).toISOString()
		})
	}

	return trips.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
}

export function generateMockRequests(count = 6, filters?: AyanFilters): AyanRequest[] {
	const requests: AyanRequest[] = []

	for (let i = 0; i < count; i++) {
		const isIntercity = Math.random() > 0.5
		const routes = isIntercity ? INTERCITY_ROUTES : CITY_ROUTES
		const route = routes[Math.floor(Math.random() * routes.length)]
		const passenger = mockPassengers[Math.floor(Math.random() * mockPassengers.length)]
		const hoursAgo = Math.floor(Math.random() * 48)

		if (!route || !passenger) continue

		const from_address = route.from
		const to_address = route.to
		const date = randomDate(Math.floor(Math.random() * 3))

		if (filters?.from && from_address !== filters.from) continue
		if (filters?.to && to_address !== filters.to) continue
		if (filters?.date && date !== filters.date) continue

		requests.push({
			id: 200 + i,
			passenger,
			from_address,
			to_address,
			date,
			time: Math.random() > 0.4 ? randomTime() : null,
			description: Math.random() > 0.6 ? 'Нужно доехать до обеда' : null,
			status: 'open',
			created_at: new Date(Date.now() - hoursAgo * 3600000).toISOString()
		})
	}

	return requests.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime())
}

export function generateMockResponses(count = 3): AyanResponse[] {
	const responses: AyanResponse[] = []

	for (let i = 0; i < count; i++) {
		const user = mockResponseUsers[Math.floor(Math.random() * mockResponseUsers.length)]
		if (!user) continue

		responses.push({
			id: 300 + i,
			user,
			message: Math.random() > 0.5 ? 'Могу подвезти, звоните' : null,
			created_at: new Date(Date.now() - Math.floor(Math.random() * 24) * 3600000).toISOString()
		})
	}

	return responses
}

export function getMockTripsStore(): AyanTrip[] {
	const store = useState<AyanTrip[]>('ayan-mock-trips', () => generateMockTrips(10))
	return store.value
}

export function findMockTrip(id: number): AyanTrip | null {
	return getMockTripsStore().find((t) => t.id === id) ?? null
}

export function getMockRequestsStore(): AyanRequest[] {
	const store = useState<AyanRequest[]>('ayan-mock-requests', () => generateMockRequests(6))
	return store.value
}

export function findMockRequest(id: number): AyanRequest | null {
	return getMockRequestsStore().find((r) => r.id === id) ?? null
}

export function createMockTrip(data: AyanTripCreate): AyanTrip {
	const driver = mockDrivers[0]!
	return {
		id: Math.floor(Math.random() * 10000) + 1000,
		driver,
		...data,
		comment: data.comment ?? null,
		status: 'open',
		created_at: new Date().toISOString()
	}
}

export function createMockRequest(data: AyanRequestCreate): AyanRequest {
	const passenger = mockPassengers[0]!
	return {
		id: Math.floor(Math.random() * 10000) + 2000,
		passenger,
		...data,
		time: data.time ?? null,
		description: data.description ?? null,
		status: 'open',
		created_at: new Date().toISOString()
	}
}

export function createMockResponse(message?: string | null): AyanResponse {
	const user = mockResponseUsers[0]!
	return {
		id: Math.floor(Math.random() * 10000) + 3000,
		user,
		message: message ?? null,
		created_at: new Date().toISOString()
	}
}

export const ayanMockDelay = () => new Promise<void>((resolve) => setTimeout(resolve, randomDelay()))
