import { getTelegramWebApp, waitForTelegramInitData, waitForTelegramWebApp } from '~/utils/telegram'

type TelegramState = {
	webApp: TelegramWebApp | null
	initData: string
	user: TelegramWebAppUser | null
}

let telegramInitDataSync: Promise<void> | null = null

export const useTg = () => {
	const state = useState<TelegramState>('telegram-state', () => ({
		webApp: null,
		initData: '',
		user: null
	}))

	const syncTelegramState = () => {
		const webApp = import.meta.client ? getTelegramWebApp() : null

		state.value = {
			webApp,
			initData: webApp?.initData || '',
			user: webApp?.initDataUnsafe?.user || null
		}

		return state.value
	}

	const ensureTelegramState = () => {
		const currentState = syncTelegramState()

		if (!import.meta.client || telegramInitDataSync || (currentState.webApp && currentState.initData)) {
			return
		}

		telegramInitDataSync = waitForTelegramWebApp()
			.then(() => {
				syncTelegramState()

				if (!state.value.webApp || state.value.initData) {
					return
				}

				return waitForTelegramInitData().then(() => {
					syncTelegramState()
				})
			})
			.finally(() => {
				telegramInitDataSync = null
			})
	}

	ensureTelegramState()

	const webApp = computed(() => state.value.webApp)
	const isInTelegram = computed(() => !!state.value.webApp)
	const user = computed(() => state.value.user)
	const initData = computed(() => state.value.initData)

	const version = computed(() => webApp.value?.version || '6.0')

	const themeParams = computed(
		() =>
			webApp.value?.themeParams || {
				bg_color: '#0a0c0e',
				text_color: '#eff3f5',
				hint_color: '#8da3ad',
				button_color: '#5edac6',
				button_text_color: '#0a0c0e'
			}
	)

	const isReady = computed(() => !!webApp.value)

	const supportsVersion = (minVersion: string) => {
		const currentVersion = syncTelegramState().webApp?.version || '6.0'
		const a = currentVersion.split('.').map(Number)
		const b = minVersion.split('.').map(Number)
		for (let i = 0; i < Math.max(a.length, b.length); i++) {
			const diff = (a[i] || 0) - (b[i] || 0)
			if (diff !== 0) return diff > 0
		}
		return true
	}

	const ready = () => {
		syncTelegramState().webApp?.ready()
	}

	const expand = () => {
		syncTelegramState().webApp?.expand()
	}

	const close = () => {
		syncTelegramState().webApp?.close()
	}

	let _backButtonCallback: (() => void) | null = null

	const showBackButton = () => {
		if (!supportsVersion('6.1')) return
		try {
			syncTelegramState().webApp?.BackButton?.show()
		} catch {
			// ignore
		}
	}

	const hideBackButton = () => {
		if (!supportsVersion('6.1')) return
		try {
			const currentWebApp = syncTelegramState().webApp
			if (_backButtonCallback) {
				currentWebApp?.BackButton?.offClick(_backButtonCallback)
				_backButtonCallback = null
			}
			currentWebApp?.BackButton?.hide()
		} catch {
			// ignore
		}
	}

	const onBackButtonClicked = (callback: () => void) => {
		if (!supportsVersion('6.1')) return
		try {
			const currentWebApp = syncTelegramState().webApp
			if (_backButtonCallback) {
				currentWebApp?.BackButton?.offClick(_backButtonCallback)
			}
			_backButtonCallback = callback
			currentWebApp?.BackButton?.onClick(callback)
		} catch {
			// ignore
		}
	}

	const showMainButton = (text: string, onClick: () => void) => {
		const currentWebApp = syncTelegramState().webApp
		if (!currentWebApp) return
		try {
			if (currentWebApp.MainButton) {
				currentWebApp.MainButton.text = text
				currentWebApp.MainButton.onClick(onClick)
				currentWebApp.MainButton.show()
			}
		} catch {
			// ignore
		}
	}

	const hideMainButton = () => {
		const currentWebApp = syncTelegramState().webApp
		if (!currentWebApp) return
		try {
			currentWebApp.MainButton?.hide()
		} catch {
			// ignore
		}
	}

	const hapticFeedback = (type: 'impact' | 'notification' | 'selection' = 'impact') => {
		if (!supportsVersion('6.1')) return
		try {
			const currentWebApp = syncTelegramState().webApp
			if (currentWebApp?.HapticFeedback) {
				if (type === 'impact') currentWebApp.HapticFeedback.impactOccurred('medium')
				else if (type === 'notification') currentWebApp.HapticFeedback.notificationOccurred('success')
				else currentWebApp.HapticFeedback.selectionChanged()
			}
		} catch {
			// ignore
		}
	}

	return {
		isInTelegram,
		webApp,
		user,
		initData,
		version,
		themeParams,
		isReady,
		supportsVersion,
		ready,
		expand,
		close,
		showBackButton,
		hideBackButton,
		onBackButtonClicked,
		showMainButton,
		hideMainButton,
		hapticFeedback
	}
}
