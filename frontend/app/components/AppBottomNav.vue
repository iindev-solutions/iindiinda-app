<script setup lang="ts">
const route = useRoute()
const { hapticFeedback } = useTg()

const navItems = [
	{ label: 'Главная', icon: 'i-lucide-home', route: '/' },
	{ label: 'AYAN', icon: 'i-lucide-car', route: '/ayan' },
	{ label: 'UUS', icon: 'i-lucide-wrench', route: '/uus' },
	{ label: 'TAL', icon: 'i-lucide-calendar', route: '/tal' },
	{ label: 'AGAL', icon: 'i-lucide-box', route: '/agal' }
]

const activeRoute = computed(() => {
	const path = route.path
	if (path === '/') return '/'
	for (const item of navItems) {
		if (item.route !== '/' && path.startsWith(item.route)) return item.route
	}
	return '/'
})

function handleNavigate(path: string) {
	hapticFeedback('impact')
	navigateTo(path)
}
</script>

<template>
	<nav class="bottom-nav">
		<div class="mx-auto flex max-w-[480px] items-center justify-around px-2 py-1.5">
			<button
				v-for="item in navItems"
				:key="item.route"
				class="nav-item"
				:class="activeRoute === item.route ? 'text-cyan-400' : 'text-gray-500'"
				@click="handleNavigate(item.route)"
			>
				<UIcon :name="item.icon" class="text-lg" />
				<span class="text-[10px] font-medium leading-tight">{{ item.label }}</span>
			</button>
		</div>
	</nav>
</template>

<style>
.bottom-nav {
	position: fixed;
	inset-inline: 0;
	bottom: 0;
	z-index: 50;
	border-top: 0.5px solid var(--border-color);
	background: rgb(10 12 14 / 0.95);
	backdrop-filter: blur(12px);
	padding-bottom: env(safe-area-inset-bottom, 0px);
}
.nav-item {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 2px;
	padding: 6px 12px;
	border-radius: 12px;
	transition: color 150ms ease-out;
}
.nav-item:hover {
	color: var(--color-gray-300);
}
</style>
