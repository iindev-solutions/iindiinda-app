<script setup lang="ts">
const { t } = useI18n()

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
</script>

<template>
	<div class="app-page app-page--home">
		<section class="index-page__hero app-panel">
			<div class="index-page__hero-body">
				<p class="app-kicker">iindiinda</p>
				<AppTitle />
				<p class="index-page__subtitle">{{ t('home.subtitle') }}</p>
				<p class="app-copy app-copy--hero">
					{{ t('home.caption') }}
				</p>
				<div class="app-badge-row">
					<span v-for="service in services" :key="service.id" class="app-chip">
						<UIcon :name="service.icon" />
						{{ service.name }}
					</span>
				</div>
			</div>
		</section>

		<section class="app-section-stack">
			<ServiceCard v-for="service in services" :key="service.id" :service="service" />
		</section>

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
	margin-bottom: 16px;
	padding: 20px;
	border-radius: 28px;
	background:
		linear-gradient(135deg, rgb(94 218 198 / 0.1), transparent 40%),
		linear-gradient(180deg, rgb(23 28 33 / 0.98), rgb(18 22 26 / 0.94));
}

.index-page__hero-body {
	display: flex;
	flex-direction: column;
	gap: 14px;
}

.index-page__subtitle {
	font-size: 18px;
	font-weight: 600;
	line-height: 1.3;
	letter-spacing: -0.02em;
	color: var(--text-primary);
	margin: 0;
}

.index-page__legal {
	margin-top: 16px;
	border-radius: 24px;
	border-color: rgb(154 166 178 / 0.12);
	background: rgb(18 22 26 / 0.8);
}
</style>
