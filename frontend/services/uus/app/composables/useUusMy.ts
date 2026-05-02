import type { UusResponse, UusTask } from '../types/uus'

export const useUusMy = () => {
	const api = useAPI()

	const fetchMyTasks = async (): Promise<UusTask[]> => {
		const res = await api.get<{ success: boolean; data: UusTask[] }>('/uus/my/tasks')
		return res.data ?? []
	}

	const fetchMyResponses = async (): Promise<UusResponse[]> => {
		const res = await api.get<{ success: boolean; data: UusResponse[] }>('/uus/my/responses')
		return res.data ?? []
	}

	return { fetchMyTasks, fetchMyResponses }
}
