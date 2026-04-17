<script setup lang="ts">
const route = useRoute()

const isHome = computed(() => route.path === '/')
</script>

<template>
	<div class="tma-layout">
		<header v-if="!isHome" class="tma-header">
			<NuxtLink to="/" class="tma-header-back">
				<svg
					xmlns="http://www.w3.org/2000/svg"
					width="20"
					height="20"
					viewBox="0 0 24 24"
					fill="none"
					stroke="currentColor"
					stroke-width="2"
					stroke-linecap="round"
					stroke-linejoin="round"
				>
					<path d="m15 18-6-6 6-6" />
				</svg>
				<span class="text-sm font-medium">iind.app</span>
			</NuxtLink>
		</header>
		<main>
			<slot />
		</main>
	</div>
</template>

<style>
.tma-layout {
	min-height: 100vh;
	transition: background-color 0.2s ease;
}

.tma-header {
	position: sticky;
	top: 0;
	z-index: 50;
	display: flex;
	align-items: center;
	height: 48px;
	padding: 0 16px;
	background-color: var(--color-background);
	border-bottom: 0.5px solid var(--color-cyan-900);
	backdrop-filter: blur(12px);
}

.tma-header-back {
	display: flex;
	align-items: center;
	gap: 4px;
	color: var(--color-cyan-400);
	text-decoration: none;
	transition: opacity 0.15s ease;
}

.tma-header-back:hover {
	opacity: 0.8;
}
</style>
