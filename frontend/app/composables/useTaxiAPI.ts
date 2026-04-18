import { USE_MOCK_API } from '~/config/api.config'

export const useTaxiAPI = () => {
	const api = USE_MOCK_API ? useMockAPI() : useAPI()

	if (USE_MOCK_API && import.meta.dev) {
		console.log('[useTaxiAPI] Режим МОК API - данные сохраняются в localStorage')
	}

	return api
}
