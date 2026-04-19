<script setup lang="ts">
/**
 * OAuth Callback Page
 *
 * Обрабатывает redirect после Telegram OAuth авторизации.
 * Получает hash из query params и завершает авторизацию.
 */

const route = useRoute()
const { handleOAuthCallback } = useAuth()

onMounted(async () => {
	const hash = route.query.hash as string
	if (hash) {
		try {
			await handleOAuthCallback(hash)
		} catch (error) {
			console.error('[callback] OAuth failed:', error)
			// Redirect to home on error
			await navigateTo('/')
		}
	} else {
		// No hash, redirect to home
		await navigateTo('/')
	}
})
</script>

<template>
	<div class="flex items-center justify-center min-h-screen">
		<LoadingSpinner size="lg" />
	</div>
</template>
