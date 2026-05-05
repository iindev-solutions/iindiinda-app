<script setup lang="ts">
const { t } = useI18n()
const { services } = usePublicRoadmap()
</script>

<template>
	<div class="app-page app-page--roadmap">
		<AppHero
			:eyebrow="t('roadmap.page.badge')"
			:title="t('roadmap.page.title')"
			:description="t('roadmap.page.description')"
			icon="i-lucide-map"
		/>

		<UCard class="roadmap-page__note" variant="outline">
			<p class="app-copy">{{ t('roadmap.page.note') }}</p>
		</UCard>

		<div class="roadmap-page__grid">
			<AppRoadmapCard
				v-for="service in services"
				:key="service.id"
				:label="service.badge"
				:title="service.name"
				:description="service.summary"
				:icon="service.icon"
				:live-label="t('roadmap.sections.live')"
				:building-label="t('roadmap.sections.building')"
				:planned-label="t('roadmap.sections.planned')"
				:live="service.live"
				:building="service.building"
				:planned="service.planned"
				compact
				:action-label="t('roadmap.openService')"
				:action-route="service.route"
			/>
		</div>
	</div>
</template>

<style scoped>
.roadmap-page__note {
	margin-bottom: 14px;
	border-radius: 20px;
	border-color: var(--border-color);
	background: var(--bg-level-1);
}

.roadmap-page__grid {
	display: flex;
	flex-direction: column;
	gap: 14px;
}
</style>
