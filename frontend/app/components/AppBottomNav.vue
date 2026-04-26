<script setup lang="ts">
const route = useRoute()
const { hapticFeedback } = useTg()

const navItems = [
	{ label: 'iind', icon: 'i-lucide-home', route: '/' },
	{ label: 'AYAN', icon: 'i-lucide-car', route: '/ayan' },
	{ label: 'UUS', icon: 'i-lucide-wrench', route: '/uus' },
	{ label: 'TAL', icon: 'i-lucide-calendar', route: '/tal' },
	{ label: 'AGAL', icon: 'i-lucide-box', route: '/agal' }
]

const pendingRoute = ref<string | null>(null)

function isRouteActive(path: string, target: string) {
	if (target === '/') return path === '/'
	return path === target || path.startsWith(`${target}/`)
}

const activeRoute = computed(() => {
	const currentPath = route.path
	const visualPath = pendingRoute.value ?? currentPath
	const match = navItems.find((item) => isRouteActive(visualPath, item.route))
	return match?.route || '/'
})

async function handleNavigate(path: string) {
	if (isRouteActive(route.path, path)) return

	pendingRoute.value = path
	hapticFeedback('impact')

	try {
		await navigateTo(path)
	} finally {
		pendingRoute.value = null
	}
}
</script>

<template>
	<nav class="bottom-nav">
		<div class="bottom-nav__dock">
			<NuxtLink
				v-for="item in navItems"
				:key="item.route"
				:to="item.route"
				class="bottom-nav__item"
				:class="{ 'bottom-nav__item--active': activeRoute === item.route }"
				:aria-current="activeRoute === item.route ? 'page' : undefined"
				@click="handleNavigate(item.route)"
			>
				<UIcon :name="item.icon" class="bottom-nav__icon" />
				<span class="bottom-nav__label">{{ item.label }}</span>
			</NuxtLink>
		</div>
	</nav>
</template>

<style>
.bottom-nav {
	position: fixed;
	left: 0;
	right: 0;
	bottom: 0;
	z-index: 50;
	padding: 0 14px calc(10px + env(safe-area-inset-bottom, 0px));
}

.bottom-nav__dock {
	max-width: 560px;
	margin: 0 auto;
	display: grid;
	grid-template-columns: repeat(5, minmax(0, 1fr));
	gap: 6px;
	padding: 8px;
	border: 1px solid rgb(154 166 178 / 0.12);
	border-radius: 24px;
	background: rgb(12 16 19 / 0.88);
	backdrop-filter: blur(18px);
	box-shadow: 0 18px 48px rgb(0 0 0 / 0.28);
}

.bottom-nav__item {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	gap: 4px;
	min-height: 54px;
	padding: 8px 4px;
	border-radius: 18px;
	color: rgb(125 141 149 / 0.84);
	text-decoration: none;
	transition:
		background-color 150ms ease-out,
		color 150ms ease-out,
		transform 150ms ease-out;
}

.bottom-nav__item--active {
	background: linear-gradient(180deg, rgb(94 218 198 / 0.16), rgb(94 218 198 / 0.1));
	color: rgb(var(--color-cyan-300));
}

.bottom-nav__item:active {
	transform: translateY(1px);
}

.bottom-nav__icon {
	font-size: 17px;
}

.bottom-nav__label {
	font-size: 10px;
	font-weight: 600;
	line-height: 1;
	letter-spacing: 0.02em;
}
</style>
