<script setup lang="ts">
interface Service {
	id: string
	name: string
	icon: string
	description: string
	stats?: string
}

defineProps<{
	service: Service
}>()
</script>

<template>
	<NuxtLink :to="`/${service.id}`" class="service-card">
		<div class="service-card__icon">
			<UIcon :name="service.icon" />
		</div>
		<div class="service-card__content">
			<h3 class="service-card__name">{{ service.name }}</h3>
			<p class="service-card__description">{{ service.description }}</p>
			<span v-if="service.stats" class="service-card__stats">{{ service.stats }}</span>
		</div>
		<div class="service-card__arrow">
			<UIcon name="i-carbon-chevron-right" />
		</div>
	</NuxtLink>
</template>

<style scoped>
.service-card {
	display: flex;
	align-items: center;
	gap: 16px;
	padding: 16px;
	background: rgb(var(--color-gray-800));
	border-radius: 12px;
	border: 1px solid rgb(var(--color-gray-700));
	transition: all 0.2s ease;
	cursor: pointer;
	text-decoration: none;
}

.service-card:hover {
	background: rgb(var(--color-gray-700));
	border-color: rgb(var(--color-cyan-500));
}

.service-card__icon {
	width: 48px;
	height: 48px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgb(var(--color-cyan-500));
	border-radius: 12px;
	font-size: 24px;
	color: rgb(var(--color-gray-900));
	flex-shrink: 0;
}

.service-card__content {
	flex: 1;
	min-width: 0;
}

.service-card__name {
	font-size: 16px;
	font-weight: 600;
	color: rgb(var(--color-gray-100));
	margin: 0;
}

.service-card__description {
	font-size: 14px;
	color: rgb(var(--color-gray-400));
	margin: 4px 0 0;
}

.service-card__stats {
	font-size: 12px;
	color: rgb(var(--color-cyan-400));
	margin-top: 4px;
	display: block;
}

.service-card__arrow {
	color: rgb(var(--color-gray-500));
	font-size: 20px;
	flex-shrink: 0;
}
</style>
