/**
 * Global Error Handler Plugin
 *
 * Перехватывает все Vue ошибки и показывает их пользователю.
 */

export default defineNuxtPlugin((nuxtApp) => {
	const { showError } = useGlobalError()

	nuxtApp.hook('vue:error', (error: unknown, instance, info) => {
		console.error('[error-handler] Vue error:', error, info)
		const message = error instanceof Error ? error.message : 'Произошла ошибка'
		showError(message)
	})

	// Handle unhandled promise rejections
	if (import.meta.client) {
		window.addEventListener('unhandledrejection', (event) => {
			console.error('[error-handler] Unhandled rejection:', event.reason)
			const message = event.reason?.message || 'Произошла ошибка'
			showError(message)
		})
	}
})
