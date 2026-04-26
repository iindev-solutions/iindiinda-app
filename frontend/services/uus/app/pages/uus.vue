<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { t } = useI18n()

const aboutExamples = computed(() => [
	{
		title: t('serviceAbout.uus.examples.cleaning.title'),
		description: t('serviceAbout.uus.examples.cleaning.description')
	},
	{
		title: t('serviceAbout.uus.examples.repair.title'),
		description: t('serviceAbout.uus.examples.repair.description')
	}
])

const cards = computed(() => [
	{
		icon: 'i-lucide-clipboard-list',
		title: t('servicePages.uus.cards.task.title'),
		description: t('servicePages.uus.cards.task.description')
	},
	{
		icon: 'i-lucide-messages-square',
		title: t('servicePages.uus.cards.responses.title'),
		description: t('servicePages.uus.cards.responses.description')
	},
	{
		icon: 'i-lucide-handshake',
		title: t('servicePages.uus.cards.agreement.title'),
		description: t('servicePages.uus.cards.agreement.description')
	}
])
</script>

<template>
	<div class="app-page">
		<AppHero
			:eyebrow="t('servicePages.uus.badge')"
			:title="t('servicePages.uus.title')"
			:description="t('servicePages.uus.intro')"
			icon="i-lucide-wrench"
		>
			<AppServiceAbout
				:label="t('serviceAbout.label')"
				:description="t('serviceAbout.uus.description')"
				:examples-title="t('serviceAbout.examplesTitle')"
				:examples="aboutExamples"
			/>
		</AppHero>

		<div class="app-section-stack">
			<div v-for="card in cards" :key="card.title" class="app-panel app-panel--soft service-page-card">
				<div class="service-page-card__icon">
					<UIcon :name="card.icon" />
				</div>
				<div>
					<h2 class="service-page-card__title">{{ card.title }}</h2>
					<p class="service-page-card__description">{{ card.description }}</p>
				</div>
			</div>

			<div class="app-panel app-panel--soft service-page-cta">
				<UButton block size="lg" color="primary" disabled>{{ t('servicePages.uus.cta') }}</UButton>
				<p class="service-page-cta__status">{{ t('servicePages.uus.status') }}</p>
			</div>
		</div>
	</div>
</template>

<style scoped>
.service-page-card {
	display: flex;
	gap: 14px;
	padding: 18px;
}

.service-page-card__icon {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 44px;
	height: 44px;
	border-radius: 16px;
	background: rgb(94 218 198 / 0.1);
	border: 1px solid rgb(94 218 198 / 0.14);
	color: rgb(var(--color-cyan-300));
	font-size: 20px;
	flex-shrink: 0;
}

.service-page-card__title {
	font-size: 16px;
	font-weight: 600;
	line-height: 1.35;
	color: var(--text-primary);
	margin: 0;
}

.service-page-card__description {
	font-size: 13px;
	line-height: 1.6;
	color: var(--text-secondary);
	margin: 6px 0 0;
}

.service-page-cta {
	padding: 18px;
}

.service-page-cta__status {
	margin-top: 10px;
	text-align: center;
	font-size: 12px;
	color: var(--text-muted);
}
</style>
