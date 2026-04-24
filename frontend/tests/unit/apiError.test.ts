import { describe, expect, it } from 'vitest'

import { getApiErrorMessage } from '../../app/utils/api-error'

describe('api error helper', () => {
	it('prefers backend message and falls back safely', () => {
		expect(getApiErrorMessage({ data: { message: 'Trip is not open' } }, 'Fallback')).toBe('Trip is not open')
		expect(getApiErrorMessage({ message: 'Client fail' }, 'Fallback')).toBe('Client fail')
		expect(getApiErrorMessage(null, 'Fallback')).toBe('Fallback')
	})
})
