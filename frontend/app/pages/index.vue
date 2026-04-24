<script setup lang="ts">
const { t } = useI18n()
const { hapticFeedback } = useTg()

const services = [
	{
		id: 'ayan',
		name: 'AYAN',
		icon: 'i-carbon-car',
		description: t('ayan.desc'),
		route: '/ayan'
	},
	{
		id: 'uus',
		name: 'UUS',
		icon: 'i-carbon-tool-kit',
		description: 'Услуги',
		route: '/uus'
	},
	{
		id: 'tal',
		name: 'TAL',
		icon: 'i-carbon-calendar',
		description: 'Запись',
		route: '/tal'
	},
	{
		id: 'agal',
		name: 'AGAL',
		icon: 'i-carbon-box',
		description: 'Посылки',
		route: '/agal'
	}
]

function handleServiceClick(service: { route: string }) {
	hapticFeedback('impact')
	return navigateTo(service.route)
}
</script>

<template>
	<div class="index-page">
		<div class="index-page__header">
			<app-title />
			<p class="index-page__subtitle">Городские сервисы</p>
		</div>

		<div class="index-page__services">
			<ServiceCard
				v-for="service in services"
				:key="service.id"
				:service="service"
				@click="handleServiceClick(service)"
			/>
		</div>

		<div class="index-page__partner">
			<div class="index-page__partner-icon">
				<UIcon name="i-carbon-user" />
			</div>
			<div class="index-page__partner-content">
				<h3>Стать партнером</h3>
				<p>Для бизнеса</p>
			</div>
			<UIcon name="i-carbon-chevron-right" class="index-page__partner-arrow" />
		</div>

		<div class="mt-6">
			<AppLegalLinks />
		</div>
	</div>
</template>

<style scoped>
.index-page {
	min-height: 100vh;
	padding: 32px 16px;
	display: flex;
	flex-direction: column;
}

.index-page__header {
	text-align: center;
	margin-bottom: 40px;
}

.index-page__subtitle {
	font-size: 14px;
	color: rgb(var(--color-gray-400));
	margin: 8px 0 0;
}

.index-page__services {
	display: flex;
	flex-direction: column;
	gap: 12px;
	flex: 1;
}

.index-page__partner {
	display: flex;
	align-items: center;
	gap: 16px;
	padding: 16px;
	background: rgb(var(--color-gray-800));
	border-radius: 12px;
	border: 1px solid rgb(var(--color-gray-700));
	margin-top: auto;
	cursor: pointer;
}

.index-page__partner-icon {
	width: 48px;
	height: 48px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgb(var(--color-gray-700));
	border-radius: 12px;
	font-size: 24px;
	color: rgb(var(--color-gray-400));
}

.index-page__partner-content {
	flex: 1;
}

.index-page__partner-content h3 {
	font-size: 16px;
	font-weight: 600;
	color: rgb(var(--color-gray-100));
	margin: 0;
}

.index-page__partner-content p {
	font-size: 14px;
	color: rgb(var(--color-gray-500));
	margin: 4px 0 0;
}

.index-page__partner-arrow {
	font-size: 20px;
	color: rgb(var(--color-gray-500));
}
</style>
