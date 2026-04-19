/**
 * Auth Middleware
 *
 * Защищает роуты от неавторизованных пользователей.
 * Редиректит на главную если пользователь не авторизован.
 *
 * Usage:
 * <script setup>
 * definePageMeta({
 *   middleware: 'auth'
 * })
 * </script>
 */

export default defineNuxtRouteMiddleware((_to, _from) => {
	const { isAuthenticated } = useAuth()

	if (!isAuthenticated.value) {
		return navigateTo('/')
	}
})
