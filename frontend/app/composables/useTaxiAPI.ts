/**
 * useTaxiAPI - универсальный API для такси
 *
 * Автоматически использует мок или реальный API в зависимости от конфигурации.
 * Не нужно менять код в компонентах при переключении!
 */
import { USE_MOCK_API } from '~/config/api.config'

export const useTaxiAPI = () => {
	const realAPI = useAPI()
	const mockAPI = useMockAPI()

	// Выбираем API в зависимости от конфигурации
	const api = USE_MOCK_API ? mockAPI : realAPI

	// Показываем уведомление при использовании мока
	if (USE_MOCK_API && import.meta.dev) {
		console.log('🔶 [useTaxiAPI] Режим МОК API - данные сохраняются в localStorage')
	}

	return {
		// Все методы из useAPI
		get: api.get,
		post: api.post,
		put: api.put,
		del: api.del,
		request: api.request,

		// Дополнительные методы для удобства
		...(USE_MOCK_API
			? {
					// В мок режиме доступны прямые методы
					createOrder: mockAPI.createOrder,
					acceptOrder: mockAPI.acceptOrder,
					cancelOrder: mockAPI.cancelOrder,
					markArrived: mockAPI.markArrived,
					startTrip: mockAPI.startTrip,
					completeTrip: mockAPI.completeTrip,
					getOrders: mockAPI.getOrders,
					getMyOrder: mockAPI.getMyOrder,
					switchRole: mockAPI.switchRole,
					setAvailability: mockAPI.setAvailability,
					state: mockAPI.state // Доступ к состоянию для отладки
				}
			: {})
	}
}
