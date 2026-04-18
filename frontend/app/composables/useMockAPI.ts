/**
 * useMockAPI - мок-сервис для разработки фронтенда
 *
 * Имитирует работу реального API:
 * - Задержки сети (300-800ms)
 * - Ошибки (10% шанс для тестирования)
 * - Сохранение состояния в localStorage
 * - Типы данных совпадают с api.ts
 *
 * Для переключения на реальный API: USE_MOCK_API = false в api.config.ts
 */

import { MOCK_CONFIG } from '~/config/api.config'
import type { User, TaxiOrder } from '~/types/api'

const getMockState = () => {
	if (typeof localStorage === 'undefined') return null
	const stored = localStorage.getItem('mock-api-state')
	if (stored) {
		return JSON.parse(stored)
	}
	return null
}

const saveMockState = (state: any) => {
	if (typeof localStorage !== 'undefined') {
		localStorage.setItem('mock-api-state', JSON.stringify(state))
	}
}

const initialState = {
	currentUser: {
		id: 1,
		telegram_id: 123456,
		username: 'test_user',
		first_name: 'Тестовый',
		role: 'passenger' as 'passenger' | 'driver',
		rating: 4.8,
		completed_orders: 12,
		is_available: false,
		created_at: new Date().toISOString(),
		updated_at: new Date().toISOString()
	} as User,
	orders: [
		{
			id: 101,
			passenger_id: 2,
			driver_id: null,
			from_address: 'ул. Ленина, 15',
			to_address: 'Аэропорт Якутск',
			price: 350,
			status: 'open',
			passenger: {
				id: 2,
				telegram_id: 2,
				username: null,
				first_name: 'Анна',
				role: 'passenger',
				rating: 4.5,
				completed_orders: 8,
				is_available: false,
				created_at: '',
				updated_at: ''
			},
			driver: null,
			created_at: new Date().toISOString(),
			updated_at: new Date().toISOString()
		},
		{
			id: 102,
			passenger_id: 3,
			driver_id: null,
			from_address: 'ТЦ "Туймаада"',
			to_address: 'мкр. Старый город',
			price: 200,
			status: 'open',
			passenger: {
				id: 3,
				telegram_id: 3,
				username: null,
				first_name: 'Михаил',
				role: 'passenger',
				rating: 4.2,
				completed_orders: 3,
				is_available: false,
				created_at: '',
				updated_at: ''
			},
			driver: null,
			created_at: new Date().toISOString(),
			updated_at: new Date().toISOString()
		}
	] as TaxiOrder[],
	myActiveOrder: null as TaxiOrder | null
}

const useMockState = () => {
	const state = useState('mock-api-state', () => ({
		...initialState
	}))

	if (import.meta.client) {
		const stored = getMockState()
		if (stored) {
			Object.assign(state.value, stored)
		}

		watch(
			state,
			(newValue) => {
				saveMockState(newValue)
			},
			{ deep: true }
		)
	}

	return state
}

const delay = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms))
const randomDelay = () => delay(MOCK_CONFIG.baseDelay + Math.random() * MOCK_CONFIG.maxExtraDelay)
const shouldFail = () => Math.random() < MOCK_CONFIG.errorRate

export const useMockAPI = () => {
	const mockState = useMockState()

	// ===== AUTH API =====
	const login = async () => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка авторизации')

		return {
			token: 'mock-jwt-token',
			user: mockState.value.currentUser
		}
	}

	// ===== USER API =====
	const getUser = async () => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка получения данных пользователя')

		return { data: mockState.value.currentUser }
	}

	const switchRole = async (role: 'passenger' | 'driver') => {
		await randomDelay()
		mockState.value.currentUser.role = role
		if (role === 'passenger') {
			mockState.value.currentUser.is_available = false
		}
		if (import.meta.dev) console.log(`[MockAPI] Роль изменена на: ${role}`)
		return { user: mockState.value.currentUser }
	}

	const setAvailability = async (available: boolean) => {
		await randomDelay()
		mockState.value.currentUser.is_available = available
		if (import.meta.dev) console.log(`[MockAPI] Статус: ${available ? 'На линии' : 'Оффлайн'}`)
		return { success: true }
	}

	// ===== ORDERS API =====
	const getOrders = async () => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка загрузки заказов')

		const availableOrders = mockState.value.orders.filter((o: TaxiOrder) => o.status === 'open')
		return { data: availableOrders }
	}

	const getMyOrder = async () => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка получения активного заказа')

		return { data: mockState.value.myActiveOrder }
	}

	const getOrder = async (id: number) => {
		await randomDelay()

		if (mockState.value.myActiveOrder?.id === id) {
			return { data: mockState.value.myActiveOrder }
		}

		const order = mockState.value.orders.find((o: TaxiOrder) => o.id === id)
		if (!order) throw new Error('Заказ не найден')

		return { data: order }
	}

	const createOrder = async (data: { from_address: string; to_address: string; price: number }) => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка создания заказа')
		if (data.price < 100 || data.price > 5000) throw new Error('Цена должна быть от 100 до 5000 ₽')
		if (mockState.value.myActiveOrder) throw new Error('У вас уже есть активный заказ')

		const newOrder: TaxiOrder = {
			id: Date.now(),
			passenger_id: mockState.value.currentUser.id,
			driver_id: null,
			from_address: data.from_address,
			to_address: data.to_address,
			price: data.price,
			status: 'open',
			passenger: mockState.value.currentUser,
			driver: null,
			created_at: new Date().toISOString(),
			updated_at: new Date().toISOString()
		}

		mockState.value.orders.push(newOrder)
		mockState.value.myActiveOrder = newOrder

		if (import.meta.dev) console.log('[MockAPI] Заказ создан! Ищем водителя...')

		setTimeout(() => {
			if (mockState.value.myActiveOrder?.id === newOrder.id && mockState.value.myActiveOrder.status === 'open') {
				const mockDriver: User = {
					id: 50,
					telegram_id: 555,
					username: 'driver_mock',
					first_name: 'Водитель ' + Math.floor(Math.random() * 100),
					role: 'driver',
					rating: +(4 + Math.random()).toFixed(1),
					completed_orders: Math.floor(50 + Math.random() * 200),
					is_available: true,
					created_at: '',
					updated_at: ''
				}
				mockState.value.myActiveOrder.status = 'accepted'
				mockState.value.myActiveOrder.driver_id = mockDriver.id
				mockState.value.myActiveOrder.driver = mockDriver
				mockState.value.myActiveOrder.updated_at = new Date().toISOString()

				const orderInList = mockState.value.orders.find((o: TaxiOrder) => o.id === newOrder.id)
				if (orderInList) {
					orderInList.status = 'accepted'
					orderInList.driver_id = mockDriver.id
					orderInList.driver = mockDriver
					orderInList.updated_at = new Date().toISOString()
				}
				if (import.meta.dev) console.log('[MockAPI] Водитель найден!')
			}
		}, MOCK_CONFIG.autoAcceptDelay)

		return { data: newOrder }
	}

	const acceptOrder = async (orderId: number) => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка принятия заказа')
		if (mockState.value.currentUser.role !== 'driver') throw new Error('Только водитель может принять заказ')

		const order = mockState.value.orders.find((o: TaxiOrder) => o.id === orderId)
		if (!order) throw new Error('Заказ не найден')
		if (order.status !== 'open') throw new Error('Заказ уже занят')
		if (mockState.value.myActiveOrder) throw new Error('У вас уже есть активный заказ')

		order.status = 'accepted'
		order.driver_id = mockState.value.currentUser.id
		order.driver = mockState.value.currentUser
		order.updated_at = new Date().toISOString()

		mockState.value.myActiveOrder = { ...order }

		if (import.meta.dev) console.log('[MockAPI] Заказ принят! Едьте к пассажиру')
		return { data: order }
	}

	const cancelOrder = async (orderId: number) => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка отмены заказа')

		const orderIndex = mockState.value.orders.findIndex((o: TaxiOrder) => o.id === orderId)
		if (orderIndex > -1 && mockState.value.orders[orderIndex]) {
			mockState.value.orders[orderIndex]!.status = 'cancelled'
			mockState.value.orders[orderIndex]!.updated_at = new Date().toISOString()
		}

		if (mockState.value.myActiveOrder?.id === orderId) {
			mockState.value.myActiveOrder.status = 'cancelled'
			mockState.value.myActiveOrder.updated_at = new Date().toISOString()
			setTimeout(() => {
				mockState.value.myActiveOrder = null
			}, 2000)
		}

		if (import.meta.dev) console.log('[MockAPI] Заказ отменен')
		return { data: { success: true } }
	}

	const markArrived = async (orderId: number) => {
		await randomDelay()

		if (mockState.value.myActiveOrder?.id === orderId) {
			mockState.value.myActiveOrder.status = 'arrived'
			mockState.value.myActiveOrder.updated_at = new Date().toISOString()
		}

		const order = mockState.value.orders.find((o: TaxiOrder) => o.id === orderId)
		if (order) {
			order.status = 'arrived'
			order.updated_at = new Date().toISOString()
		}

		if (import.meta.dev) console.log('[MockAPI] Вы на месте! Ожидайте пассажира')
		return { data: { success: true } }
	}

	const startTrip = async (orderId: number) => {
		await randomDelay()

		if (mockState.value.myActiveOrder?.id === orderId) {
			mockState.value.myActiveOrder.status = 'in_progress'
			mockState.value.myActiveOrder.updated_at = new Date().toISOString()
		}

		const order = mockState.value.orders.find((o: TaxiOrder) => o.id === orderId)
		if (order) {
			order.status = 'in_progress'
			order.updated_at = new Date().toISOString()
		}

		if (import.meta.dev) console.log('[MockAPI] Поездка началась!')
		return { data: { success: true } }
	}

	const completeTrip = async (orderId: number) => {
		await randomDelay()

		if (mockState.value.myActiveOrder?.id === orderId) {
			mockState.value.myActiveOrder.status = 'completed'
			mockState.value.myActiveOrder.updated_at = new Date().toISOString()
		}

		const order = mockState.value.orders.find((o: TaxiOrder) => o.id === orderId)
		if (order) {
			order.status = 'completed'
			order.updated_at = new Date().toISOString()
		}

		mockState.value.currentUser.completed_orders = (mockState.value.currentUser.completed_orders || 0) + 1

		setTimeout(() => {
			mockState.value.myActiveOrder = null
		}, 2000)

		if (import.meta.dev) console.log('[MockAPI] Поездка завершена!')
		return { data: { success: true } }
	}

	// ===== Универсальный метод (для совместимости с useAPI) =====
	const request = async <T>(endpoint: string, options?: { method?: string; body?: any }): Promise<T> => {
		const { method = 'GET', body } = options || {}

		if (import.meta.dev) console.log(`[MockAPI] ${method} ${endpoint}`, body)

		if (endpoint === '/auth/telegram' || endpoint.includes('/auth')) {
			const result = await login()
			return result as T
		}

		if (endpoint === '/user/me') {
			return getUser() as Promise<T>
		}

		if (endpoint === '/user/switch-role') {
			return switchRole(body?.role) as Promise<T>
		}

		if (endpoint === '/user/availability') {
			return setAvailability(body?.available) as Promise<T>
		}

		if (endpoint === '/ayan/orders/open') {
			return getOrders() as Promise<T>
		}

		if (endpoint === '/ayan/orders/my') {
			return getMyOrder() as Promise<T>
		}

		if (endpoint === '/ayan/orders') {
			if (method === 'POST') {
				return createOrder(body) as Promise<T>
			}
			return getOrders() as Promise<T>
		}

		// /ayan/orders/active
		if (endpoint === '/ayan/orders/active') {
			return getMyOrder() as Promise<T>
		}

		// Specific action routes — check BEFORE generic {id} route
		const cancelMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/cancel$/)
		if (cancelMatch?.[1]) {
			return cancelOrder(parseInt(cancelMatch[1])) as Promise<T>
		}

		const acceptMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/accept$/)
		if (acceptMatch?.[1]) {
			return acceptOrder(parseInt(acceptMatch[1])) as Promise<T>
		}

		const arriveMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/arrive$/)
		if (arriveMatch?.[1]) {
			return markArrived(parseInt(arriveMatch[1])) as Promise<T>
		}

		const startMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/start$/)
		if (startMatch?.[1]) {
			return startTrip(parseInt(startMatch[1])) as Promise<T>
		}

		const completeMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/complete$/)
		if (completeMatch?.[1]) {
			return completeTrip(parseInt(completeMatch[1])) as Promise<T>
		}

		// /ayan/orders/:id (generic — MUST be last among /ayan/orders/ routes)
		const orderMatch = endpoint.match(/\/ayan\/orders\/(\d+)$/)
		if (orderMatch?.[1]) {
			return getOrder(parseInt(orderMatch[1])) as Promise<T>
		}

		throw new Error(`[MockAPI] Неизвестный endpoint: ${endpoint}`)
	}

	const get = <T>(endpoint: string, _params?: Record<string, string>) => request<T>(endpoint, { method: 'GET' })
	const post = <T>(endpoint: string, body?: any) => request<T>(endpoint, { method: 'POST', body })
	const put = <T>(endpoint: string, body?: any) => request<T>(endpoint, { method: 'PUT', body })
	const del = <T>(endpoint: string) => request<T>(endpoint, { method: 'DELETE' })

	return {
		request,
		get,
		post,
		put,
		del,
		login,
		getUser,
		switchRole,
		setAvailability,
		getOrders,
		getMyOrder,
		getOrder,
		createOrder,
		acceptOrder,
		cancelOrder,
		markArrived,
		startTrip,
		completeTrip,
		state: mockState
	}
}
