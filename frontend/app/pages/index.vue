<script setup lang="ts">
const router = useRouter()

const services = [
	{
		name: 'Бардыбыт',
		desc: 'Такси',
		to: '/ayan',
		icon: 'i-lucide-car',
		active: true
	},
	{
		name: 'Уус',
		desc: 'Мастера',
		to: '/uus',
		icon: 'i-lucide-wrench',
		active: false
	},
	{
		name: 'Аҕал',
		desc: 'Доставка',
		to: '/agal',
		icon: 'i-lucide-plane',
		active: false
	},
	{
		name: 'Тал',
		desc: 'Запись',
		to: '/tal',
		icon: 'i-lucide-calendar',
		active: false
	}
]

function navigate(to: string, active: boolean) {
	if (active) {
		router.push(to)
	}
}
</script>

<template>
	<div class="flex min-h-screen flex-col px-5 py-8">
		<!-- Header -->
		<div class="mb-10">
			<h1 class="mb-1 text-3xl font-semibold text-white">iind.app</h1>
			<p class="text-sm text-gray-400">Городские сервисы</p>
		</div>

		<!-- Services Grid -->
		<div class="mb-auto grid grid-cols-2 gap-3">
			<button
				v-for="service in services"
				:key="service.name"
				class="flex flex-col items-center gap-3 rounded-2xl p-5 transition-transform active:scale-95"
				:class="
					service.active
						? 'bg-cyan-500/20 border border-cyan-500/30'
						: 'bg-gray-800/50 border border-gray-700/50 opacity-60'
				"
				@click="navigate(service.to, service.active)"
			>
				<UIcon
					:name="service.icon"
					class="h-8 w-8"
					:class="service.active ? 'text-cyan-400' : 'text-gray-500'"
				/>
				<div class="text-center">
					<div class="font-medium text-white">{{ service.name }}</div>
					<div class="text-xs text-gray-400">{{ service.desc }}</div>
				</div>
			</button>
		</div>

		<!-- Partner CTA -->
		<button
			class="mt-8 flex items-center gap-3 rounded-xl bg-gray-800/30 p-4 text-left transition-colors active:bg-gray-800/50"
		>
			<UIcon name="i-lucide-users" class="h-6 w-6 text-gray-400" />
			<div class="flex-1">
				<div class="text-sm font-medium text-white">Стать партнером</div>
				<div class="text-xs text-gray-500">Для бизнеса</div>
			</div>
			<UIcon name="i-lucide-chevron-right" class="h-5 w-5 text-gray-500" />
		</button>
	</div>
</template>
