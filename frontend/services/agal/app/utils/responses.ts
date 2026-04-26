import type { AgalResponse } from '../types/agal'

export function findTargetResponse(
	responses: AgalResponse[],
	target: { routeId?: number; requestId?: number }
): AgalResponse | null {
	return (
		responses.find((response) => {
			if (target.routeId) return response.route_id === target.routeId
			if (target.requestId) return response.request_id === target.requestId
			return false
		}) ?? null
	)
}

export function getResponseTargetPath(response: AgalResponse): string | null {
	if (response.route_id) return `/agal/route/${response.route_id}`
	if (response.request_id) return `/agal/request/${response.request_id}`
	return null
}
