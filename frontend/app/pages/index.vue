<script setup lang="ts">
const { t } = useI18n()
const { hapticFeedback } = useTg()

const services = [
	{
		id: 'ayan',
		name: t('services.ayan.name'),
		icon: 'i-carbon-car',
		description: t('services.ayan.desc'),
		route: '/ayan'
	},
	{
		id: 'uus',
		name: t('services.uus.name'),
		icon: 'i-carbon-tool-kit',
		description: t('services.uus.desc'),
		route: '/uus'
	},
	{
		id: 'tal',
		name: t('services.tal.name'),
		icon: 'i-carbon-calendar',
		description: t('services.tal.desc'),
		route: '/tal'
	},
	{
		id: 'agal',
		name: t('services.agal.name'),
		icon: 'i-carbon-box',
		description: t('services.agal.desc'),
		route: '/agal'
	}
]

async function openRoadmap() {
	hapticFeedback('impact')
	await navigateTo('/roadmap')
}
</script>

<template>
	<div class="app-page app-page--home">
		<section class="index-page__hero app-panel">
			<div class="index-page__hero-body">
				<AppTitle />
				<p class="index-page__subtitle">{{ t('home.subtitle') }}</p>
				<p class="app-copy app-copy--hero">{{ t('home.caption') }}</p>
			</div>
		</section>

		<section class="app-section-stack">
			<ServiceCard v-for="service in services" :key="service.id" :service="service" />
		</section>

		<UCard class="index-page__roadmap" variant="outline">
			<div class="space-y-4">
				<div class="space-y-2">
					<p class="app-kicker">{{ t('home.roadmap.badge') }}</p>
					<h2 class="text-sm font-semibold text-cyan-50">{{ t('home.roadmap.title') }}</h2>
					<p class="app-copy">{{ t('home.roadmap.description') }}</p>
				</div>
				<UButton
					size="sm"
					variant="ghost"
					color="primary"
					trailing-icon="i-lucide-arrow-right"
					@click="openRoadmap"
				>
					{{ t('home.roadmap.cta') }}
				</UButton>
			</div>
		</UCard>

		<UCard class="index-page__legal" variant="outline">
			<div class="space-y-4">
				<div class="space-y-2">
					<p class="app-kicker">{{ t('legal.badge') }}</p>
					<h2 class="text-sm font-semibold text-cyan-50">{{ t('legal.footer.title') }}</h2>
					<p class="app-copy">{{ t('legal.footer.description') }}</p>
				</div>
				<AppLegalLinks :show-intro="false" />
			</div>
		</UCard>
	</div>
</template>

<style scoped>
.index-page__hero {
	margin-bottom: 14px;
	padding: 18px;
}

.index-page__hero-body {
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.index-page__subtitle {
	font-size: 16px;
	font-weight: 600;
	line-height: 1.35;
	letter-spacing: -0.02em;
	color: var(--text-primary);
	margin: 0;
}

.index-page__roadmap,
.index-page__legal {
	margin-top: 14px;
	border-radius: 20px;
	border-color: var(--border-color);
	background: var(--bg-level-1);
}
</style>
