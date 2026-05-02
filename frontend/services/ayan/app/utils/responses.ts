import type { AyanResponse } from '../types/ayan'

export function findTargetResponse(
	responses: AyanResponse[],
	target: { tripId?: number; requestId?: number }
): AyanResponse | null {
	return (
		responses.find((response) => {
			if (target.tripId) return response.trip_id === target.tripId
			if (target.requestId) return response.request_id === target.requestId
			return false
		}) ?? null
	)
}

export function getResponseTargetPath(response: AyanResponse): string | null {
	if (response.trip_id) return `/ayan/trip/${response.trip_id}`
	if (response.request_id) return `/ayan/request/${response.request_id}`
	return null
}
