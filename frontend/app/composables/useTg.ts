/**
 * useTg - обёртка Telegram WebApp SDK
 * Предоставляет доступ к Telegram Mini App API
 */
export const useTg = () => {
	// Проверяем, находимся ли мы внутри Telegram WebApp
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
		try {
			webApp.value?.BackButton?.show()
		} catch {
			console.log('[useTg] BackButton not supported')
		}
	}

	const hideBackButton = () => {
		try {
			webApp.value?.BackButton?.hide()
		} catch {
			console.log('[useTg] BackButton not supported')
		}
	}

	const onBackButtonClicked = (callback: () => void) => {
		try {
			webApp.value?.BackButton?.onClick(callback)
		} catch {
			console.log('[useTg] BackButton not supported')
		}
	}

	const showMainButton = (text: string, onClick: () => void) => {
		if (webApp.value?.MainButton) {
			webApp.value.MainButton.text = text
			webApp.value.MainButton.onClick(onClick)
			webApp.value.MainButton.show()
		}
	}

	const hideMainButton = () => {
		if (webApp.value?.MainButton) {
			webApp.value.MainButton.hide()
		}
	}

	const hapticFeedback = (type: 'impact' | 'notification' | 'selection' = 'impact') => {
		try {
			if (webApp.value?.HapticFeedback?.impactOccurred) {
				if (type === 'impact') webApp.value.HapticFeedback.impactOccurred('medium')
				else if (type === 'notification') webApp.value.HapticFeedback.notificationOccurred('success')
				else webApp.value.HapticFeedback.selectionChanged()
			}
		} catch {
			// HapticFeedback не поддерживается в старых версиях WebApp
			console.log('[useTg] HapticFeedback not supported')
		}
	}

	return {
		isInTelegram, // true если работаем внутри Telegram
		webApp,
		user,
		initData,
		themeParams,
		isReady,
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
