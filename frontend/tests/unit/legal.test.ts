import { describe, expect, it } from 'vitest'

import { getAyanLegalLinks } from '../../app/utils/legal'

describe('legal links', () => {
	it('returns the three AYAN legal routes', () => {
		expect(getAyanLegalLinks()).toEqual([
			{ key: 'terms', to: '/legal/ayan-terms' },
			{ key: 'privacy', to: '/legal/privacy' },
			{ key: 'safety', to: '/legal/ayan-safety' }
		])
	})
})
