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

function handleNavigate(route: string) {
	hapticFeedback('impact')
	navigateTo(route)
}
</script>

<template>
	<nav
		class="fixed inset-x-0 bottom-0 z-50 border-t border-gray-800 bg-[#0a0c0e]/95 backdrop-blur-sm"
		style="padding-bottom: env(safe-area-inset-bottom, 0px)"
	>
		<div class="mx-auto flex max-w-[480px] items-center justify-around px-2 py-2">
			<button
				v-for="item in navItems"
				:key="item.route"
				class="flex flex-col items-center gap-0.5 rounded-lg px-3 py-1.5 transition-colors"
				:class="activeRoute === item.route ? 'text-cyan-400' : 'text-gray-500 hover:text-gray-300'"
				@click="handleNavigate(item.route)"
			>
				<UIcon :name="item.icon" class="text-[20px]" />
				<span class="text-[10px] font-medium">{{ item.label }}</span>
			</button>
		</div>
	</nav>
</template>
