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
	const visualPath = pendingRoute.value ?? route.path
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
			<button
				v-for="item in navItems"
				:key="item.route"
				type="button"
				class="bottom-nav__item"
				:class="{ 'bottom-nav__item--active': activeRoute === item.route }"
				@click="handleNavigate(item.route)"
			>
				<span class="bottom-nav__icon-wrap">
					<UIcon :name="item.icon" class="bottom-nav__icon" />
				</span>
				<span class="bottom-nav__label">{{ item.label }}</span>
			</button>
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
	gap: 4px;
	padding: 6px;
	border: 1px solid var(--border-color);
	border-radius: 18px;
	background: rgb(11 15 18 / 0.96);
	box-shadow: 0 8px 24px rgb(0 0 0 / 0.22);
}

.bottom-nav__item {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	gap: 4px;
	min-height: 52px;
	padding: 6px 4px;
	border-radius: 14px;
	color: var(--text-muted);
	transition:
		background-color 150ms ease-out,
		color 150ms ease-out;
}

.bottom-nav__item--active {
	background: rgb(94 218 198 / 0.08);
	color: var(--color-cyan-400);
}

.bottom-nav__icon-wrap {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	width: 26px;
	height: 26px;
	border-radius: 999px;
}

.bottom-nav__item--active .bottom-nav__icon-wrap {
	background: rgb(94 218 198 / 0.12);
}

.bottom-nav__icon {
	font-size: 16px;
}

.bottom-nav__label {
	font-size: 10px;
	font-weight: 600;
	line-height: 1;
	letter-spacing: 0.02em;
}
</style>
