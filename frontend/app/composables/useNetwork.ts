/**
 * Network Status Composable
 *
 * Отслеживает online/offline статус.
 * Полезно для показа уведомлений о потере соединения.
 */

export const useNetwork = () => {
	const isOnline = ref(true)

	if (import.meta.client) {
		isOnline.value = navigator.onLine

		const handleOnline = () => {
			isOnline.value = true
		}

		const handleOffline = () => {
			isOnline.value = false
		}

		window.addEventListener('online', handleOnline)
		window.addEventListener('offline', handleOffline)

		// Cleanup on unmount
		onUnmounted(() => {
			window.removeEventListener('online', handleOnline)
			window.removeEventListener('offline', handleOffline)
		})
	}

	return {
		isOnline: readonly(isOnline)
	}
}
