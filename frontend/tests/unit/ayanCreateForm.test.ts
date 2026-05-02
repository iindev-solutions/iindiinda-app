import { describe, expect, it } from 'vitest'

import { parsePriceInput, sanitizePriceInput } from '../../services/ayan/app/utils/create-form'

describe('ayan create form helpers', () => {
	it('sanitizes price input to digits only', () => {
		expect(sanitizePriceInput('12a3 ₽')).toBe('123')
		expect(sanitizePriceInput('')).toBe('')
	})

	it('parses empty or invalid price input as zero', () => {
		expect(parsePriceInput('')).toBe(0)
		expect(parsePriceInput('000')).toBe(0)
		expect(parsePriceInput('1500')).toBe(1500)
	})
})
