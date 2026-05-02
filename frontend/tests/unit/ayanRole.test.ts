import { describe, expect, it } from 'vitest'

import { getAyanCreateMode, isAyanPrimaryRole } from '../../services/ayan/app/utils/role'

describe('ayan role utils', () => {
	it('maps driver and passenger to create modes', () => {
		expect(getAyanCreateMode('driver')).toBe('trip')
		expect(getAyanCreateMode('passenger')).toBe('request')
	})

	it('rejects non-ayan roles', () => {
		expect(getAyanCreateMode('carrier')).toBeNull()
		expect(isAyanPrimaryRole('driver')).toBe(true)
		expect(isAyanPrimaryRole('master')).toBe(false)
	})
})
