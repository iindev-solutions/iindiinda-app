/**
 * useTg - обёртка Telegram WebApp SDK
 * Предоставляет доступ к Telegram Mini App API
 *
 * Версии API:
 *   v6.0: MainButton, basic API
 *   v6.1: BackButton, HapticFeedback
 *   v6.2: SettingsButton, Accelerometer, DeviceOrientation
 *   v6.4: BiometryManager
 *   v6.7: SecondaryButton
 *
 * SDK бросает ошибку при ДОСТУПЕ к свойствам, которых нет в текущей версии,
 * поэтому проверяем version ДО обращения к HapticFeedback/BackButton/etc.
 */

function compareVersions(v1: string, v2: string): number {
	const a = v1.split('.').map(Number)
	const b = v2.split('.').map(Number)
	for (let i = 0; i < Math.max(a.length, b.length); i++) {
		const diff = (a[i] || 0) - (b[i] || 0)
		if (diff !== 0) return diff > 0 ? 1 : -1
	}
	return 0
}

export const useTg = () => {
	const isInTelegram = computed(() => {
		if (typeof window === 'undefined') return false
		return !!window.Telegram?.WebApp?.initData
	})

	const webApp = computed(() => {
		if (import.meta.client && window.Telegram?.WebApp) {
			return window.Telegram.WebApp
		}
		return null
	})

	const user = computed(() => webApp.value?.initDataUnsafe?.user || null)

	const initData = computed(() => webApp.value?.initData || '')

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

	const supportsVersion = (minVersion: string) => compareVersions(version.value, minVersion) >= 0

	const ready = () => {
		webApp.value?.ready()
	}

	const expand = () => {
		webApp.value?.expand()
	}

	const close = () => {
		webApp.value?.close()
	}

	const showBackButton = () => {
		if (!supportsVersion('6.1')) return
		try {
			webApp.value?.BackButton?.show()
		} catch {
			// ignore
		}
	}

	const hideBackButton = () => {
		if (!supportsVersion('6.1')) return
		try {
			webApp.value?.BackButton?.hide()
		} catch {
			// ignore
		}
	}

	const onBackButtonClicked = (callback: () => void) => {
		if (!supportsVersion('6.1')) return
		try {
			webApp.value?.BackButton?.onClick(callback)
		} catch {
			// ignore
		}
	}

	const showMainButton = (text: string, onClick: () => void) => {
		if (!webApp.value) return
		try {
			if (webApp.value.MainButton) {
				webApp.value.MainButton.text = text
				webApp.value.MainButton.onClick(onClick)
				webApp.value.MainButton.show()
			}
		} catch {
			// ignore
		}
	}

	const hideMainButton = () => {
		if (!webApp.value) return
		try {
			webApp.value.MainButton?.hide()
		} catch {
			// ignore
		}
	}

	const hapticFeedback = (type: 'impact' | 'notification' | 'selection' = 'impact') => {
		if (!supportsVersion('6.1')) return
		try {
			if (webApp.value?.HapticFeedback) {
				if (type === 'impact') webApp.value.HapticFeedback.impactOccurred('medium')
				else if (type === 'notification') webApp.value.HapticFeedback.notificationOccurred('success')
				else webApp.value.HapticFeedback.selectionChanged()
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
