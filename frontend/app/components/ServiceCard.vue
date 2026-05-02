<script setup lang="ts">
interface Service {
	id: string
	name: string
	icon: string
	description: string
	stats?: string
	route?: string
}

const props = defineProps<{
	service: Service
}>()

const { hapticFeedback } = useTg()

const href = computed(() => props.service.route || `/${props.service.id}`)

function handleClick() {
	hapticFeedback('impact')
}
</script>

<template>
	<NuxtLink :to="href" class="service-card app-panel app-panel--interactive" @click="handleClick">
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
	gap: 14px;
	padding: 16px;
	text-decoration: none;
}

.service-card__icon {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 44px;
	height: 44px;
	border-radius: 14px;
	background: rgb(94 218 198 / 0.1);
	border: 1px solid rgb(94 218 198 / 0.14);
	font-size: 20px;
	color: rgb(var(--color-cyan-300));
	flex-shrink: 0;
}

.service-card__content {
	min-width: 0;
	flex: 1;
}

.service-card__name {
	font-size: 15px;
	font-weight: 600;
	line-height: 1.35;
	color: var(--text-primary);
	margin: 0;
}

.service-card__description {
	font-size: 13px;
	line-height: 1.5;
	color: var(--text-secondary);
	margin: 4px 0 0;
}

.service-card__stats {
	display: block;
	font-size: 12px;
	color: rgb(var(--color-cyan-400));
	margin-top: 6px;
}

.service-card__arrow {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	color: var(--text-muted);
	font-size: 18px;
	flex-shrink: 0;
}
</style>
