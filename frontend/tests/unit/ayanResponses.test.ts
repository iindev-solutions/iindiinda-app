import { describe, expect, it } from 'vitest'

import type { AyanResponse } from '../../services/ayan/app/types/ayan'
import { findTargetResponse, getResponseTargetPath } from '../../services/ayan/app/utils/responses'

const responses: AyanResponse[] = [
	{
		id: 1,
		trip_id: 44,
		request_id: null,
		user: { id: 2, name: 'Alex', username: 'alex' },
		message: 'pending trip',
		status: 'pending',
		created_at: '2026-04-24T00:00:00Z'
	},
	{
		id: 2,
		trip_id: null,
		request_id: 55,
		user: { id: 2, name: 'Alex', username: 'alex' },
		message: 'rejected request',
		status: 'rejected',
		created_at: '2026-04-24T00:00:00Z'
	}
]

describe('ayan response helpers', () => {
	it('finds current target response by trip or request id', () => {
		expect(findTargetResponse(responses, { tripId: 44 })).toMatchObject({ id: 1, status: 'pending' })
		expect(findTargetResponse(responses, { requestId: 55 })).toMatchObject({ id: 2, status: 'rejected' })
		expect(findTargetResponse(responses, { tripId: 99 })).toBeNull()
	})

	it('builds target navigation path from response target', () => {
		expect(getResponseTargetPath(responses[0]!)).toBe('/ayan/trip/44')
		expect(getResponseTargetPath(responses[1]!)).toBe('/ayan/request/55')
	})
})
