import { describe, expect, it } from 'vitest'

import { canUseDevInitData, getAyanAccessState } from '../../app/utils/auth'

describe('auth utils', () => {
	it('allows dev init data only on dev or localhost', () => {
		expect(canUseDevInitData('localhost', 'test')).toBe(true)
		expect(canUseDevInitData('127.0.0.1', 'test')).toBe(true)
		expect(canUseDevInitData('iindiinda.duckdns.org', 'test')).toBe(false)
		expect(canUseDevInitData('iindiinda.duckdns.org', 'test', true)).toBe(true)
	})

	it('derives ayan access state from auth and telegram context', () => {
		expect(
			getAyanAccessState({ isAuthenticated: true, isLoading: false, isInTelegram: false, hasAuthError: false })
		).toBe('ready')
		expect(
			getAyanAccessState({ isAuthenticated: false, isLoading: true, isInTelegram: true, hasAuthError: false })
		).toBe('loading')
		expect(
			getAyanAccessState({ isAuthenticated: false, isLoading: false, isInTelegram: true, hasAuthError: true })
		).toBe('auth-error')
		expect(
			getAyanAccessState({ isAuthenticated: false, isLoading: false, isInTelegram: false, hasAuthError: false })
		).toBe('telegram-required')
	})
})
