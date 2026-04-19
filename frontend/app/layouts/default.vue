<script setup lang="ts">
const route = useRoute()
const isHome = computed(() => route.path === '/')
</script>

<template>
	<div class="tma-layout">
		<main :class="{ 'pb-16': !isHome }">
			<slot />
		</main>
		<AppBottomNav />
	</div>
</template>

<style>
.tma-layout {
	min-height: 100vh;
	background: rgb(var(--color-gray-950));
}
</style>
