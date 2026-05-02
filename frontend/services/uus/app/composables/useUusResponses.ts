import type { UusResponse, UusResponseCreate, UusResponseStatus } from '../types/uus'

export const useUusResponses = () => {
	const api = useAPI()

	const fetchTaskResponses = async (taskId: number): Promise<UusResponse[]> => {
		const res = await api.get<{ success: boolean; data: UusResponse[] }>(`/uus/tasks/${taskId}/responses`)
		return res.data ?? []
	}

	const createTaskResponse = async (taskId: number, data?: UusResponseCreate): Promise<UusResponse> => {
		const res = await api.post<{ success: boolean; data: UusResponse }>(
			`/uus/tasks/${taskId}/responses`,
			(data ?? {}) as Record<string, unknown>
		)
		return res.data!
	}

	const updateResponseStatus = async (id: number, status: UusResponseStatus): Promise<UusResponse> => {
		const res = await api.patch<{ success: boolean; data: UusResponse }>(`/uus/responses/${id}`, { status })
		return res.data!
	}

	const cancelResponse = async (id: number): Promise<void> => {
		await api.del(`/uus/responses/${id}`)
	}

	return { fetchTaskResponses, createTaskResponse, updateResponseStatus, cancelResponse }
}
