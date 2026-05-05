<script setup lang="ts">
const services = [
	{
		id: 'ayan',
		name: 'AYAN',
		icon: 'i-carbon-car',
		description: 'Попутки',
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

const currentRoute = useRoute()
const currentService = computed(() => {
	const path = currentRoute.path
	for (const service of services) {
		if (path.startsWith(service.route)) {
			return service
		}
	}
	return null
})
</script>

<template>
	<header class="app-header">
		<div class="app-header__top">
			<div class="app-header__logo">
				<span class="app-header__logo-icon">iind</span>
			</div>
			<nav v-if="currentService" class="app-header__nav">
				<span class="app-header__nav-service">
					<UIcon :name="currentService.icon" />
					{{ currentService.name }}
				</span>
			</nav>
		</div>
		<div v-if="currentService" class="app-header__title">
			<h1>{{ currentService.name }} — {{ currentService.description }}</h1>
		</div>
	</header>
</template>

<style scoped>
.app-header {
	background: var(--color-gray-900);
	border-bottom: 1px solid var(--color-gray-800);
	padding: 12px 16px;
	position: sticky;
	top: 0;
	z-index: 50;
}

.app-header__top {
	display: flex;
	align-items: center;
	justify-content: space-between;
}

.app-header__logo {
	display: flex;
	align-items: center;
	gap: 8px;
}

.app-header__logo-icon {
	font-size: 18px;
	font-weight: 700;
	color: var(--color-cyan-400);
}

.app-header__nav {
	display: flex;
	align-items: center;
}

.app-header__nav-service {
	display: flex;
	align-items: center;
	gap: 6px;
	font-size: 14px;
	font-weight: 600;
	color: var(--color-gray-100);
	background: var(--color-gray-800);
	padding: 4px 12px;
	border-radius: 16px;
}

.app-header__title {
	margin-top: 8px;
}

.app-header__title h1 {
	font-size: 14px;
	font-weight: 500;
	color: var(--color-gray-400);
}
</style>
