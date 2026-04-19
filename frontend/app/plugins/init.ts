/**
 * Init Plugin
 *
 * Инициализирует приложение при старте:
 * 1. Telegram SDK (ready, expand)
 * 2. Auto-login (если не авторизован)
 */

export default defineNuxtPlugin(async () => {
	const { ready, expand, isInTelegram } = useTg()
	const { login, isAuthenticated } = useAuth()

	// Init Telegram SDK (if in TMA)
	if (isInTelegram.value) {
		ready()
		expand()
	}

	// Auto-login if not authenticated
	if (!isAuthenticated.value) {
		try {
			await login()
		} catch (error) {
			console.error('[init] Auto-login failed:', error)
			// Don't block app, user can retry manually
		}
	}
})
