/**
 * useTg - обёртка Telegram WebApp SDK
 * Предоставляет доступ к Telegram Mini App API
 */
export const useTg = () => {
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
		if (webApp.value?.BackButton) {
			webApp.value.BackButton.show()
		}
	}

	const hideBackButton = () => {
		if (webApp.value?.BackButton) {
			webApp.value.BackButton.hide()
		}
	}

	const onBackButtonClicked = (callback: () => void) => {
		if (webApp.value?.BackButton) {
			webApp.value.BackButton.onClick(callback)
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
		if (webApp.value?.HapticFeedback) {
			if (type === 'impact') webApp.value.HapticFeedback.impactOccurred('medium')
			else if (type === 'notification') webApp.value.HapticFeedback.notificationOccurred('success')
			else webApp.value.HapticFeedback.selectionChanged()
		}
	}

	return {
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
