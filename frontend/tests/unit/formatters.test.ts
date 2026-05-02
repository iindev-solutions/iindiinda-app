import { describe, expect, it } from 'vitest'

import { formatPrice, isPastAyanDateTime } from '../../app/utils/formatters'

describe('formatters', () => {
	it('renders zero price as free', () => {
		expect(formatPrice(0, '₽', 'Free')).toBe('Free')
	})

	it('detects past AYAN date and time', () => {
		expect(isPastAyanDateTime('2000-01-01', '09:00')).toBe(true)
		expect(isPastAyanDateTime('2999-01-01', '09:00')).toBe(false)
	})

	it('treats past date without time as past', () => {
		expect(isPastAyanDateTime('2000-01-01')).toBe(true)
	})
})
