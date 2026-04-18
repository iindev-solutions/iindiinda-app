<script setup lang="ts">
defineProps<{
	icon?: string
	title: string
	description?: string
}>()

const defaultIcon = 'i-carbon-search'
</script>

<template>
	<div class="empty-state">
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
	padding: 48px 24px;
	text-align: center;
}

.empty-state__icon {
	font-size: 48px;
	color: rgb(var(--color-gray-600));
	margin-bottom: 16px;
}

.empty-state__title {
	font-size: 16px;
	font-weight: 600;
	color: rgb(var(--color-gray-200));
	margin: 0;
}

.empty-state__description {
	font-size: 14px;
	color: rgb(var(--color-gray-400));
	margin: 8px 0 0;
}
</style>
