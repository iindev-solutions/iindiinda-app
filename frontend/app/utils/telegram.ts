type WaitForTelegramInitDataOptions = {
	timeoutMs?: number
	intervalMs?: number
}

export function getTelegramWebApp(): TelegramWebApp | null {
	if (typeof window === 'undefined') return null
	return window.Telegram?.WebApp ?? null
}

export async function waitForTelegramWebApp(options: WaitForTelegramInitDataOptions = {}): Promise<TelegramWebApp | null> {
	const { timeoutMs = 10000, intervalMs = 50 } = options
	const startedAt = Date.now()

	while (Date.now() - startedAt <= timeoutMs) {
		const webApp = getTelegramWebApp()
		if (webApp) return webApp

		await new Promise((resolve) => setTimeout(resolve, intervalMs))
	}

	return getTelegramWebApp()
}

export async function waitForTelegramInitData(options: WaitForTelegramInitDataOptions = {}): Promise<string> {
	const { timeoutMs = 10000, intervalMs = 50 } = options
	const startedAt = Date.now()

	while (Date.now() - startedAt <= timeoutMs) {
		const initData = getTelegramWebApp()?.initData ?? ''
		if (initData) return initData

		await new Promise((resolve) => setTimeout(resolve, intervalMs))
	}

	return getTelegramWebApp()?.initData ?? ''
}
