<script setup lang="ts">
const route = useRoute()

const isHome = computed(() => route.path === '/')
</script>

<template>
	<div class="tma-layout">
		<AppHeader v-if="!isHome" />
		<main>
			<slot />
		</main>
	</div>
</template>

<style>
.tma-layout {
	min-height: 100vh;
	background: rgb(var(--color-gray-950));
}

.tma-layout main {
	padding-bottom: env(safe-area-inset-bottom, 16px);
}
</style>
