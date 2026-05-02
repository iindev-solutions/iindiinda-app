import type { AgalFilters, AgalRoute, AgalRouteCreate, AgalRouteUpdate } from '../types/agal'

export const useAgalRoutes = () => {
	const api = useAPI()

	const fetchRoutes = async (filters?: AgalFilters): Promise<AgalRoute[]> => {
		const params: Record<string, string> = {}
		if (filters?.from) params.from = filters.from
		if (filters?.to) params.to = filters.to
		if (filters?.date) params.date = filters.date

		const res = await api.get<{ success: boolean; data: AgalRoute[] }>('/agal/routes', params)
		return res.data ?? []
	}

	const fetchRoute = async (id: number): Promise<AgalRoute> => {
		const res = await api.get<{ success: boolean; data: AgalRoute }>(`/agal/routes/${id}`)
		return res.data!
	}

	const createRoute = async (data: AgalRouteCreate): Promise<AgalRoute> => {
		const res = await api.post<{ success: boolean; data: AgalRoute }>(
			'/agal/routes',
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	const updateRoute = async (id: number, data: AgalRouteUpdate): Promise<AgalRoute> => {
		const res = await api.patch<{ success: boolean; data: AgalRoute }>(
			`/agal/routes/${id}`,
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	return { fetchRoutes, fetchRoute, createRoute, updateRoute }
}
