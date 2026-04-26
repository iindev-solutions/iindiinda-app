<script setup lang="ts">
defineProps<{
	icon?: string
	title: string
	description?: string
}>()

const defaultIcon = 'i-carbon-search'
</script>

<template>
	<div class="app-panel app-panel--soft empty-state">
		<div class="empty-state__icon">
			<UIcon :name="icon || defaultIcon" />
		</div>
		<h3 class="empty-state__title">{{ title }}</h3>
		<p v-if="description" class="empty-state__description">{{ description }}</p>
		<slot />
	</div>
</template>

<style scoped>
.empty-state {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	padding: 28px 20px;
	text-align: center;
}

.empty-state__icon {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 44px;
	height: 44px;
	border-radius: 14px;
	background: rgb(94 218 198 / 0.08);
	font-size: 24px;
	color: rgb(var(--color-cyan-300));
	margin-bottom: 12px;
}

.empty-state__title {
	font-size: 15px;
	font-weight: 600;
	line-height: 1.35;
	color: var(--text-primary);
	margin: 0;
}

.empty-state__description {
	max-width: 28ch;
	font-size: 13px;
	line-height: 1.6;
	color: var(--text-secondary);
	margin: 8px 0 0;
}
</style>
