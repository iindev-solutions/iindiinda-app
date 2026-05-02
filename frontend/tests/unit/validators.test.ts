import { describe, expect, it } from 'vitest'

import {
	isValidAddress,
	isValidPrice,
	isValidSeats,
	isValidTelegramUsername,
	isValidTime
} from '../../app/utils/validators'

describe('validators', () => {
	it('accepts valid AYAN form values', () => {
		expect(isValidAddress('Якутск')).toBe(true)
		expect(isValidTime('09:45')).toBe(true)
		expect(isValidSeats(3)).toBe(true)
		expect(isValidPrice(1500)).toBe(true)
		expect(isValidTelegramUsername('driver_01')).toBe(true)
	})

	it('rejects invalid AYAN form values', () => {
		expect(isValidAddress('  ')).toBe(false)
		expect(isValidTime('25:99')).toBe(false)
		expect(isValidSeats(0)).toBe(false)
		expect(isValidPrice(0)).toBe(false)
		expect(isValidTelegramUsername('@bad')).toBe(false)
	})
})
