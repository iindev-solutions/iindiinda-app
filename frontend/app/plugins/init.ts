/**
 * Init Plugin
 *
 * Инициализирует приложение при старте:
 * 1. Telegram SDK (ready, expand)
 * 2. Auto-login (если не авторизован)
 */

export default defineNuxtPlugin(async () => {
	const { ready, expand, webApp, initData } = useTg()
	const { login, loginWithInitData, isAuthenticated, authStatus } = useAuth()

	watch(
		webApp,
		(currentWebApp) => {
			if (!currentWebApp) return
			ready()
			expand()
		},
		{ immediate: true }
	)

	watch(initData, async (currentInitData) => {
		if (!currentInitData || isAuthenticated.value || authStatus.value === 'loading') return

		try {
			await loginWithInitData(currentInitData)
		} catch (error) {
			console.error('[init] Delayed Telegram login failed:', error)
		}
	})

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
