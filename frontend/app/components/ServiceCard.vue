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
		<div class="service-card__top">
			<div class="service-card__icon">
				<UIcon :name="service.icon" />
			</div>
			<span class="service-card__label">{{ service.name }}</span>
		</div>

		<div class="service-card__content">
			<h3 class="service-card__name">{{ service.description }}</h3>
			<p class="service-card__description">{{ service.name }}</p>
		</div>

		<div class="service-card__footer">
			<span v-if="service.stats" class="service-card__stats">{{ service.stats }}</span>
			<span class="service-card__arrow">
				<UIcon name="i-carbon-arrow-up-right" />
			</span>
		</div>
	</NuxtLink>
</template>

<style scoped>
.service-card {
	display: flex;
	flex-direction: column;
	gap: 18px;
	padding: 18px;
	text-decoration: none;
}

.service-card__top {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
}

.service-card__icon {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 52px;
	height: 52px;
	border-radius: 18px;
	background: rgb(94 218 198 / 0.12);
	border: 1px solid rgb(94 218 198 / 0.18);
	font-size: 24px;
	color: rgb(var(--color-cyan-300));
}

.service-card__label {
	display: inline-flex;
	align-items: center;
	padding: 8px 12px;
	border-radius: 999px;
	background: rgb(255 255 255 / 0.04);
	border: 1px solid rgb(255 255 255 / 0.05);
	font-size: 11px;
	font-weight: 700;
	letter-spacing: 0.12em;
	text-transform: uppercase;
	color: rgb(125 141 149 / 0.9);
}

.service-card__content {
	display: flex;
	flex-direction: column;
	gap: 6px;
}

.service-card__name {
	font-size: 18px;
	font-weight: 600;
	line-height: 1.3;
	letter-spacing: -0.02em;
	color: var(--text-primary);
	margin: 0;
}

.service-card__description {
	font-size: 13px;
	line-height: 1.55;
	color: var(--text-secondary);
	margin: 0;
}

.service-card__footer {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
}

.service-card__stats {
	font-size: 12px;
	color: rgb(var(--color-cyan-400));
}

.service-card__arrow {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 34px;
	height: 34px;
	border-radius: 999px;
	background: rgb(255 255 255 / 0.04);
	color: var(--text-secondary);
	font-size: 16px;
}
</style>
