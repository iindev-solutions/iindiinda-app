/**
 * useMockAPI - мок-сервис для разработки фронтенда
 *
 * Полностью имитирует работу реального API:
 * - Задержки сети (300-800ms)
 * - Ошибки (10% шанс для тестирования)
 * - Сохранение состояния в localStorage
 * - Те же интерфейсы и типы данных
 *
 * Для переключения на реальный API: замените useMockAPI на useAPI в компонентах
 */

import { MOCK_CONFIG } from '~/config/api.config'

// Простой reactive state через localStorage
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

// Типы данных (дублируют API типы)
export interface MockUser {
	id: number
	telegramId: string
	name: string
	role: 'passenger' | 'driver'
	isAvailable: boolean
	stats: {
		completedOrders: number
		rating: number
	}
}

export interface MockOrder {
	id: number
	passengerId: number
	driverId?: number
	status: 'searching' | 'matched' | 'arrived' | 'on-trip' | 'completed' | 'cancelled'
	pickup: string
	destination: string
	price: number
	driver?: {
		name: string
		rating: number
	}
	passenger?: {
		name: string
		phone: string
	}
	createdAt: string
}

// Начальное состояние
const initialState = {
	currentUser: {
		id: 1,
		telegramId: '123456',
		name: 'Тестовый Пользователь',
		role: 'passenger',
		isAvailable: false,
		stats: {
			completedOrders: 12,
			rating: 4.8
		}
	} as MockUser,
	orders: [
		{
			id: 101,
			passengerId: 2,
			status: 'searching',
			pickup: 'ул. Ленина, 15',
			destination: 'Аэропорт Якутск',
			price: 350,
			createdAt: new Date().toISOString()
		},
		{
			id: 102,
			passengerId: 3,
			status: 'searching',
			pickup: 'ТЦ "Туймаада"',
			destination: 'мкр. Старый город',
			price: 200,
			createdAt: new Date().toISOString()
		}
	] as MockOrder[],
	myActiveOrder: null as MockOrder | null
}

// Глобальное состояние для всего приложения
const useMockState = () => {
	const state = useState('mock-api-state', () => ({
		...initialState
	}))

	// Загружаем из localStorage при инициализации на клиенте
	if (import.meta.client) {
		const stored = getMockState()
		if (stored) {
			Object.assign(state.value, stored)
		}

		// Сохраняем в localStorage при изменениях
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

// Имитация задержки сети
const delay = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms))
const randomDelay = () => delay(MOCK_CONFIG.baseDelay + Math.random() * MOCK_CONFIG.maxExtraDelay)

// Имитация ошибки (для тестирования обработки ошибок)
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
			mockState.value.currentUser.isAvailable = false
		}
		if (import.meta.dev) console.log(`[MockAPI] Роль изменена на: ${role}`)
		return { success: true }
	}

	const setAvailability = async (available: boolean) => {
		await randomDelay()
		mockState.value.currentUser.isAvailable = available
		if (import.meta.dev) console.log(`[MockAPI] Статус: ${available ? 'На линии' : 'Оффлайн'}`)
		return { success: true }
	}

	// ===== ORDERS API =====
	const getOrders = async () => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка загрузки заказов')

		// Возвращаем только заказы в статусе searching
		const availableOrders = mockState.value.orders.filter((o: MockOrder) => o.status === 'searching')
		return { data: availableOrders }
	}

	const getMyOrder = async () => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка получения активного заказа')

		return { data: mockState.value.myActiveOrder }
	}

	const getOrder = async (id: number) => {
		await randomDelay()

		// Ищем в моих заказах
		if (mockState.value.myActiveOrder?.id === id) {
			return { data: mockState.value.myActiveOrder }
		}

		// Ищем в списке доступных
		const order = mockState.value.orders.find((o: MockOrder) => o.id === id)
		if (!order) throw new Error('Заказ не найден')

		return { data: order }
	}

	const createOrder = async (data: { pickup: string; destination: string; price: number }) => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка создания заказа')

		const newOrder: MockOrder = {
			id: Date.now(),
			passengerId: mockState.value.currentUser.id,
			status: 'searching',
			pickup: data.pickup,
			destination: data.destination,
			price: data.price,
			createdAt: new Date().toISOString()
		}

		mockState.value.orders.push(newOrder)
		mockState.value.myActiveOrder = newOrder

		if (import.meta.dev) console.log('[MockAPI] Заказ создан! Ищем водителя...')

		// Автоматически "найти" водителя через 5 секунд (для демо)
		setTimeout(() => {
			if (mockState.value.myActiveOrder?.id === newOrder.id) {
				mockState.value.myActiveOrder.status = 'matched'
				mockState.value.myActiveOrder.driver = {
					name: 'Водитель ' + Math.floor(Math.random() * 100),
					rating: +(4 + Math.random()).toFixed(1)
				}
				// Найти и обновить в общем списке
				const orderInList = mockState.value.orders.find((o: MockOrder) => o.id === newOrder.id)
				if (orderInList) {
					orderInList.status = 'matched'
					orderInList.driver = mockState.value.myActiveOrder.driver
				}
				if (import.meta.dev) console.log('[MockAPI] Водитель найден!')
			}
		}, 5000)

		return { data: newOrder }
	}

	const acceptOrder = async (orderId: number) => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка принятия заказа')

		const order = mockState.value.orders.find((o: MockOrder) => o.id === orderId)
		if (!order) throw new Error('Заказ не найден')

		order.status = 'matched'
		order.driverId = mockState.value.currentUser.id
		order.driver = {
			name: mockState.value.currentUser.name,
			rating: mockState.value.currentUser.stats.rating
		}
		order.passenger = {
			name: 'Пассажир ' + order.passengerId,
			phone: '+7 (999) 123-45-67'
		}

		if (import.meta.dev) console.log('[MockAPI] Заказ принят! Едьте к пассажиру')
		return { success: true }
	}

	const cancelOrder = async (orderId: number) => {
		await randomDelay()
		if (shouldFail()) throw new Error('Ошибка отмены заказа')

		const orderIndex = mockState.value.orders.findIndex((o: MockOrder) => o.id === orderId)
		if (orderIndex > -1 && mockState.value.orders[orderIndex]) {
			mockState.value.orders[orderIndex]!.status = 'cancelled'
		}

		if (mockState.value.myActiveOrder?.id === orderId) {
			mockState.value.myActiveOrder.status = 'cancelled'
			// Через 2 секунды очистить активный заказ
			setTimeout(() => {
				mockState.value.myActiveOrder = null
			}, 2000)
		}

		if (import.meta.dev) console.log('[MockAPI] Заказ отменен')
		return { success: true }
	}

	const markArrived = async (orderId: number) => {
		await randomDelay()

		if (mockState.value.myActiveOrder?.id === orderId) {
			mockState.value.myActiveOrder.status = 'arrived'
		}

		const order = mockState.value.orders.find((o: MockOrder) => o.id === orderId)
		if (order) order.status = 'arrived'

		if (import.meta.dev) console.log('[MockAPI] Вы на месте! Ожидайте пассажира')
		return { success: true }
	}

	const startTrip = async (orderId: number) => {
		await randomDelay()

		if (mockState.value.myActiveOrder?.id === orderId) {
			mockState.value.myActiveOrder.status = 'on-trip'
		}

		const order = mockState.value.orders.find((o: MockOrder) => o.id === orderId)
		if (order) order.status = 'on-trip'

		if (import.meta.dev) console.log('[MockAPI] Поездка началась!')
		return { success: true }
	}

	const completeTrip = async (orderId: number) => {
		await randomDelay()

		if (mockState.value.myActiveOrder?.id === orderId) {
			mockState.value.myActiveOrder.status = 'completed'
		}

		const order = mockState.value.orders.find((o: MockOrder) => o.id === orderId)
		if (order) order.status = 'completed'

		// Увеличить счетчик поездок
		mockState.value.currentUser.stats.completedOrders++

		// Очистить активный заказ через 2 секунды
		setTimeout(() => {
			mockState.value.myActiveOrder = null
		}, 2000)

		if (import.meta.dev) console.log('[MockAPI] Поездка завершена!')
		return { success: true }
	}

	// ===== Универсальный метод (для совместимости с useAPI) =====
	const request = async <T>(endpoint: string, options?: { method?: string; body?: any }): Promise<T> => {
		const { method = 'GET', body } = options || {}

		console.log(`[MockAPI] ${method} ${endpoint}`, body)

		// Маршрутизация запросов
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

		if (endpoint === '/ayan/orders') {
			if (method === 'POST') {
				return createOrder(body) as Promise<T>
			}
			return getOrders() as Promise<T>
		}

		if (endpoint === '/ayan/orders/me') {
			return getMyOrder() as Promise<T>
		}

		// /ayan/orders/:id
		const orderMatch = endpoint.match(/\/ayan\/orders\/(\d+)$/)
		if (orderMatch?.[1]) {
			const orderId = parseInt(orderMatch[1])
			return getOrder(orderId) as Promise<T>
		}

		// /ayan/orders/:id/cancel
		const cancelMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/cancel$/)
		if (cancelMatch?.[1]) {
			const orderId = parseInt(cancelMatch[1])
			return cancelOrder(orderId) as Promise<T>
		}

		// /ayan/orders/:id/accept
		const acceptMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/accept$/)
		if (acceptMatch?.[1]) {
			const orderId = parseInt(acceptMatch[1])
			return acceptOrder(orderId) as Promise<T>
		}

		// /ayan/orders/:id/arrive
		const arriveMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/arrive$/)
		if (arriveMatch?.[1]) {
			const orderId = parseInt(arriveMatch[1])
			return markArrived(orderId) as Promise<T>
		}

		// /ayan/orders/:id/start
		const startMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/start$/)
		if (startMatch?.[1]) {
			const orderId = parseInt(startMatch[1])
			return startTrip(orderId) as Promise<T>
		}

		// /ayan/orders/:id/complete
		const completeMatch = endpoint.match(/\/ayan\/orders\/(\d+)\/complete$/)
		if (completeMatch?.[1]) {
			const orderId = parseInt(completeMatch[1])
			return completeTrip(orderId) as Promise<T>
		}

		// /ayan/orders/active
		if (endpoint === '/ayan/orders/active') {
			return getMyOrder() as Promise<T>
		}

		throw new Error(`[MockAPI] Неизвестный endpoint: ${endpoint}`)
	}

	// Совместимость с useAPI интерфейсом
	const get = <T>(endpoint: string) => request<T>(endpoint, { method: 'GET' })
	const post = <T>(endpoint: string, body?: any) => request<T>(endpoint, { method: 'POST', body })
	const put = <T>(endpoint: string, body?: any) => request<T>(endpoint, { method: 'PUT', body })
	const del = <T>(endpoint: string) => request<T>(endpoint, { method: 'DELETE' })

	return {
		// Методы совместимые с useAPI
		request,
		get,
		post,
		put,
		del,

		// Прямые методы для удобства
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

		// Доступ к состоянию (для отладки)
		state: mockState
	}
}
