import { describe, expect, it } from 'vitest'

import { shouldShowTelegramBackButton, shouldShowUiBackButton } from '../../app/utils/back-button'

describe('back button helpers', () => {
	it('shows ui button when forced even inside telegram', () => {
		expect(shouldShowUiBackButton(true, true)).toBe(true)
		expect(shouldShowUiBackButton(false, false)).toBe(true)
		expect(shouldShowUiBackButton(true, false)).toBe(false)
	})

	it('keeps telegram native back button inside telegram regardless of forceUi', () => {
		expect(shouldShowTelegramBackButton(true)).toBe(true)
		expect(shouldShowTelegramBackButton(false)).toBe(false)
	})
})
