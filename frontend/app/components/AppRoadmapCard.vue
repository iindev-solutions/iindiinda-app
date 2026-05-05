<script setup lang="ts">
import type { PublicRoadmapItem } from '~/composables/usePublicRoadmap'

const props = withDefaults(
	defineProps<{
		label: string
		title: string
		description: string
		liveLabel: string
		buildingLabel: string
		plannedLabel: string
		live: PublicRoadmapItem[]
		building: PublicRoadmapItem[]
		planned: PublicRoadmapItem[]
		actionLabel?: string
		actionRoute?: string
		icon?: string
		compact?: boolean
		limitPerSection?: number
	}>(),
	{
		compact: false,
		limitPerSection: 0
	}
)

const { hapticFeedback } = useTg()

const sections = computed(() => {
	const limit = props.limitPerSection > 0 ? props.limitPerSection : Number.POSITIVE_INFINITY

	return [
		{
			key: 'live',
			label: props.liveLabel,
			items: props.live.slice(0, limit),
			total: props.live.length,
			hidden: Math.max(props.live.length - Math.min(props.live.length, limit), 0)
		},
		{
			key: 'building',
			label: props.buildingLabel,
			items: props.building.slice(0, limit),
			total: props.building.length,
			hidden: Math.max(props.building.length - Math.min(props.building.length, limit), 0)
		},
		{
			key: 'planned',
			label: props.plannedLabel,
			items: props.planned.slice(0, limit),
			total: props.planned.length,
			hidden: Math.max(props.planned.length - Math.min(props.planned.length, limit), 0)
		}
	]
})

async function handleAction() {
	if (!props.actionRoute) return

	hapticFeedback('impact')
	await navigateTo(props.actionRoute)
}
</script>

<template>
	<section class="app-roadmap-card app-panel app-panel--soft">
		<div class="app-roadmap-card__header">
			<div class="min-w-0 flex-1 space-y-2">
				<p class="app-kicker">{{ label }}</p>
				<h2 class="app-roadmap-card__title">{{ title }}</h2>
				<p class="app-copy">{{ description }}</p>
			</div>
			<div v-if="icon" class="app-roadmap-card__icon">
				<UIcon :name="icon" class="size-5" />
			</div>
		</div>

		<div class="app-roadmap-card__sections">
			<div v-for="section in sections" :key="section.key" class="app-roadmap-card__section">
				<div class="app-roadmap-card__section-head">
					<div
						class="app-roadmap-card__badge"
						:class="{
							'app-roadmap-card__badge--live': section.key === 'live',
							'app-roadmap-card__badge--building': section.key === 'building',
							'app-roadmap-card__badge--planned': section.key === 'planned'
						}"
					>
						{{ section.label }}
					</div>
					<UBadge size="xs" color="neutral" variant="soft">{{ section.total }}</UBadge>
				</div>

				<div class="app-roadmap-card__items">
					<div v-for="item in section.items" :key="item.title" class="app-roadmap-card__item">
						<div class="app-roadmap-card__dot" />
						<div>
							<p class="app-roadmap-card__item-title">{{ item.title }}</p>
							<p v-if="!compact" class="app-roadmap-card__item-copy">{{ item.description }}</p>
						</div>
					</div>
					<p v-if="compact && section.hidden" class="app-roadmap-card__more">+{{ section.hidden }}</p>
				</div>
			</div>
		</div>

		<div v-if="actionLabel && actionRoute" class="app-roadmap-card__footer">
			<UButton
				size="sm"
				variant="ghost"
				color="primary"
				trailing-icon="i-lucide-arrow-right"
				@click="handleAction"
			>
				{{ actionLabel }}
			</UButton>
		</div>
	</section>
</template>

<style scoped>
.app-roadmap-card {
	display: flex;
	flex-direction: column;
	gap: 16px;
	padding: 16px;
}

.app-roadmap-card__header {
	display: flex;
	align-items: flex-start;
	gap: 14px;
}

.app-roadmap-card__title {
	font-size: 15px;
	font-weight: 600;
	line-height: 1.35;
	color: var(--text-primary);
	margin: 0;
}

.app-roadmap-card__icon {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 40px;
	height: 40px;
	border-radius: 14px;
	background: rgb(94 218 198 / 0.08);
	border: 1px solid rgb(94 218 198 / 0.14);
	color: var(--color-cyan-300);
	flex-shrink: 0;
}

.app-roadmap-card__sections {
	display: flex;
	flex-direction: column;
	gap: 14px;
}

.app-roadmap-card__section {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.app-roadmap-card__section-head {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 10px;
}

.app-roadmap-card__badge {
	display: inline-flex;
	align-items: center;
	width: fit-content;
	padding: 4px 10px;
	border-radius: 999px;
	font-size: 11px;
	font-weight: 700;
	letter-spacing: 0.03em;
}

.app-roadmap-card__badge--live {
	background: rgb(94 218 198 / 0.12);
	color: var(--color-cyan-300);
}

.app-roadmap-card__badge--building {
	background: rgb(56 189 248 / 0.12);
	color: rgb(125 211 252);
}

.app-roadmap-card__badge--planned {
	background: rgb(148 163 184 / 0.16);
	color: rgb(203 213 225);
}

.app-roadmap-card__items {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.app-roadmap-card__item {
	display: flex;
	align-items: flex-start;
	gap: 10px;
}

.app-roadmap-card__dot {
	width: 8px;
	height: 8px;
	margin-top: 6px;
	border-radius: 999px;
	background: var(--color-cyan-400);
	flex-shrink: 0;
}

.app-roadmap-card__item-title {
	font-size: 13px;
	font-weight: 600;
	line-height: 1.4;
	color: var(--text-primary);
	margin: 0;
}

.app-roadmap-card__item-copy {
	font-size: 12px;
	line-height: 1.55;
	color: var(--text-secondary);
	margin: 4px 0 0;
}

.app-roadmap-card__more {
	font-size: 11px;
	font-weight: 600;
	line-height: 1.4;
	color: var(--text-muted);
	margin: 0 0 0 18px;
}

.app-roadmap-card__footer {
	display: flex;
	justify-content: flex-start;
}
</style>
