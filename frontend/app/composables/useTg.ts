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

	const supportsVersion = (minVersion: string) => {
		const a = version.value.split('.').map(Number)
		const b = minVersion.split('.').map(Number)
		for (let i = 0; i < Math.max(a.length, b.length); i++) {
			const diff = (a[i] || 0) - (b[i] || 0)
			if (diff !== 0) return diff > 0
		}
		return true
	}

	const ready = () => {
		webApp.value?.ready()
	}

	const expand = () => {
		webApp.value?.expand()
	}

	const close = () => {
		webApp.value?.close()
	}

	let _backButtonCallback: (() => void) | null = null

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
			if (_backButtonCallback) {
				webApp.value?.BackButton?.offClick(_backButtonCallback)
				_backButtonCallback = null
			}
			webApp.value?.BackButton?.hide()
		} catch {
			// ignore
		}
	}

	const onBackButtonClicked = (callback: () => void) => {
		if (!supportsVersion('6.1')) return
		try {
			if (_backButtonCallback) {
				webApp.value?.BackButton?.offClick(_backButtonCallback)
			}
			_backButtonCallback = callback
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
