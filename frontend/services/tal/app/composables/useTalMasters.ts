import type { TalFilters, TalMaster, TalMasterCreate, TalMasterUpdate } from '../types/tal'

export const useTalMasters = () => {
	const api = useAPI()

	const fetchMasters = async (filters?: TalFilters): Promise<TalMaster[]> => {
		const params: Record<string, string> = {}
		if (filters?.category) params.category = filters.category
		if (filters?.availability_status) params.availability_status = filters.availability_status
		if (filters?.location) params.location = filters.location

		const res = await api.get<{ success: boolean; data: TalMaster[] }>('/tal/masters', params)
		return res.data ?? []
	}

	const fetchMaster = async (id: number): Promise<TalMaster> => {
		const res = await api.get<{ success: boolean; data: TalMaster }>(`/tal/masters/${id}`)
		return res.data!
	}

	const createMaster = async (data: TalMasterCreate): Promise<TalMaster> => {
		const res = await api.post<{ success: boolean; data: TalMaster }>(
			'/tal/masters',
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	const updateMaster = async (id: number, data: TalMasterUpdate): Promise<TalMaster> => {
		const res = await api.patch<{ success: boolean; data: TalMaster }>(
			`/tal/masters/${id}`,
			data as unknown as Record<string, unknown>
		)
		return res.data!
	}

	return { fetchMasters, fetchMaster, createMaster, updateMaster }
}
