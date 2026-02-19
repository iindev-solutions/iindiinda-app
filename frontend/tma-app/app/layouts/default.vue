<script setup lang="ts">
const { isLoading } = useLoadingIndicator()
// const { themeParams, safeAreaInsets } = useTg()

// const layoutStyles = computed(() => ({
// 	backgroundColor: themeParams.value.bg_color || '#ffffff',
// 	color: themeParams.value.text_color || '#000000',
// 	paddingTop: `${safeAreaInsets.value.top}px`,
// 	paddingBottom: `${safeAreaInsets.value.bottom}px`,
// 	minHeight: '100vh'
// }))
</script>

<template>
	<div class="tma-layout">
		eto header
		<pre>{{ isLoading }}</pre>
		<slot />
	</div>
</template>

<style>
body {
	font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	-webkit-font-smoothing: antialiased;
}

.tma-layout {
	transition: background-color 0.2s ease;
}
</style>
