/**
 * Error Handling Composable
 *
 * Управляет глобальными и локальными ошибками.
 * Автоматически скрывает ошибку через 5 секунд.
 */

export const useGlobalError = () => {
	const error = useState<string | null>('global-error', () => null)
	const errorTimeout = ref<NodeJS.Timeout | null>(null)

	const showError = (message: string, duration = 5000) => {
		error.value = message

		// Clear previous timeout
		if (errorTimeout.value) {
			clearTimeout(errorTimeout.value)
		}

		// Auto-hide after duration
		if (duration > 0) {
			errorTimeout.value = setTimeout(() => {
				error.value = null
			}, duration)
		}
	}

	const clearError = () => {
		error.value = null
		if (errorTimeout.value) {
			clearTimeout(errorTimeout.value)
			errorTimeout.value = null
		}
	}

	return {
		error: readonly(error),
		showError,
		clearError
	}
}
