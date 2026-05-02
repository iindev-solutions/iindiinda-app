import type { UusFilters, UusTask, UusTaskCreate, UusTaskUpdate } from '../types/uus'

export const useUusTasks = () => {
	const api = useAPI()

	const fetchTasks = async (filters?: UusFilters): Promise<UusTask[]> => {
		const params: Record<string, string> = {}
		if (filters?.category) params.category = filters.category
		if (filters?.location) params.location = filters.location
		if (filters?.urgency) params.urgency = filters.urgency
		if (filters?.desired_when) params.desired_when = filters.desired_when
		if (filters?.date) params.date = filters.date

		const res = await api.get<{ success: boolean; data: UusTask[] }>('/uus/tasks', params)
		return res.data ?? []
	}

	const fetchTask = async (id: number): Promise<UusTask> => {
		const res = await api.get<{ success: boolean; data: UusTask }>(`/uus/tasks/${id}`)
		return res.data!
	}

	const createTask = async (data: UusTaskCreate): Promise<UusTask> => {
		const res = await api.post<{ success: boolean; data: UusTask }>('/uus/tasks', data as unknown as Record<string, unknown>)
		return res.data!
	}

	const updateTask = async (id: number, data: UusTaskUpdate): Promise<UusTask> => {
		const res = await api.patch<{ success: boolean; data: UusTask }>(`/uus/tasks/${id}`, data as unknown as Record<string, unknown>)
		return res.data!
	}

	return { fetchTasks, fetchTask, createTask, updateTask }
}
