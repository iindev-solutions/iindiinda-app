import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest'

import { getTelegramWebApp, waitForTelegramInitData, waitForTelegramWebApp } from '../../app/utils/telegram'

type MockTelegramWebApp = {
	initData: string
}

function setMockWindow(windowValue: {
	Telegram?: {
		WebApp?: TelegramWebApp
	}
}) {
	Object.defineProperty(globalThis, 'window', {
		value: windowValue,
		configurable: true,
		writable: true
	})
}

describe('telegram utils', () => {
	beforeEach(() => {
		setMockWindow({})
	})

	afterEach(() => {
		vi.useRealTimers()
		Reflect.deleteProperty(globalThis, 'window')
	})

	it('reads telegram web app even before initData is populated', () => {
		const webApp = { initData: '' } as MockTelegramWebApp as TelegramWebApp
		setMockWindow({ Telegram: { WebApp: webApp } })

		expect(getTelegramWebApp()).toBe(webApp)
	})

	it('waits for telegram web app object to appear after initial load', async () => {
		vi.useFakeTimers()
		const webApp = { initData: '' } as MockTelegramWebApp as TelegramWebApp

		const pendingWebApp = waitForTelegramWebApp({ timeoutMs: 200, intervalMs: 50 })

		setTimeout(() => {
			setMockWindow({ Telegram: { WebApp: webApp } })
		}, 100)

		await vi.advanceTimersByTimeAsync(100)

		await expect(pendingWebApp).resolves.toBe(webApp)
	})

	it('keeps waiting long enough for slower telegram web app bootstrap', async () => {
		vi.useFakeTimers()
		const webApp = { initData: '' } as MockTelegramWebApp as TelegramWebApp

		const pendingWebApp = waitForTelegramWebApp()

		setTimeout(() => {
			setMockWindow({ Telegram: { WebApp: webApp } })
		}, 2000)

		await vi.advanceTimersByTimeAsync(2000)

		await expect(pendingWebApp).resolves.toBe(webApp)
	})

	it('waits for initData to arrive on existing telegram web app', async () => {
		vi.useFakeTimers()
		const webApp = { initData: '' } as MockTelegramWebApp as TelegramWebApp
		setMockWindow({ Telegram: { WebApp: webApp } })

		const pendingInitData = waitForTelegramInitData({ timeoutMs: 200, intervalMs: 50 })

		setTimeout(() => {
			webApp.initData = 'signed-init-data'
		}, 100)

		await vi.advanceTimersByTimeAsync(100)

		await expect(pendingInitData).resolves.toBe('signed-init-data')
	})

	it('keeps waiting long enough for slower telegram initData bootstrap', async () => {
		vi.useFakeTimers()
		const webApp = { initData: '' } as MockTelegramWebApp as TelegramWebApp
		setMockWindow({ Telegram: { WebApp: webApp } })

		const pendingInitData = waitForTelegramInitData()

		setTimeout(() => {
			webApp.initData = 'slow-signed-init-data'
		}, 2000)

		await vi.advanceTimersByTimeAsync(2000)

		await expect(pendingInitData).resolves.toBe('slow-signed-init-data')
	})

	it('returns empty initData after timeout when telegram never provides it', async () => {
		vi.useFakeTimers()
		setMockWindow({ Telegram: { WebApp: { initData: '' } as MockTelegramWebApp as TelegramWebApp } })

		const pendingInitData = waitForTelegramInitData({ timeoutMs: 100, intervalMs: 50 })

		await vi.advanceTimersByTimeAsync(150)

		await expect(pendingInitData).resolves.toBe('')
	})
})
