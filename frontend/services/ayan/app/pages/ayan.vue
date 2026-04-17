<script setup lang="ts">
definePageMeta({
	layout: 'default'
})

const { hapticFeedback } = useTg()
const router = useRouter()

function goToCreateOrder() {
	hapticFeedback('impact')
	router.push('/ayan/create')
}

function goToDriverMode() {
	hapticFeedback('impact')
	router.push('/ayan/driver')
}

function goToMyOrder() {
	hapticFeedback('impact')
	router.push('/ayan/my-order')
}
</script>

<template>
	<div class="flex min-h-screen flex-col px-5 py-8">
		<!-- Header -->
		<div class="mb-8">
			<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">Такси</div>
			<h1 class="mb-2 text-2xl font-semibold text-white">Бардыбыт</h1>
			<p class="text-sm text-gray-400">Ты ставишь цену</p>
		</div>

		<!-- Main Actions -->
		<div class="mb-auto space-y-3">
			<button
				class="flex w-full items-center gap-4 rounded-2xl bg-cyan-500/20 p-5 text-left transition-transform active:scale-95"
				@click="goToCreateOrder"
			>
				<div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-500/30">
					<UIcon name="i-lucide-plus" class="h-6 w-6 text-cyan-400" />
				</div>
				<div class="flex-1">
					<div class="font-medium text-white">Заказать поездку</div>
					<div class="text-xs text-gray-400">Создать новый заказ</div>
				</div>
				<UIcon name="i-lucide-chevron-right" class="h-5 w-5 text-gray-500" />
			</button>

			<button
				class="flex w-full items-center gap-4 rounded-2xl bg-gray-800/50 p-5 text-left transition-transform active:scale-95"
				@click="goToMyOrder"
			>
				<div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-700">
					<UIcon name="i-lucide-map-pin" class="h-6 w-6 text-gray-400" />
				</div>
				<div class="flex-1">
					<div class="font-medium text-white">Мой заказ</div>
					<div class="text-xs text-gray-400">Отследить поездку</div>
				</div>
				<UIcon name="i-lucide-chevron-right" class="h-5 w-5 text-gray-500" />
			</button>

			<button
				class="flex w-full items-center gap-4 rounded-2xl bg-gray-800/50 p-5 text-left transition-transform active:scale-95"
				@click="goToDriverMode"
			>
				<div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-700">
					<UIcon name="i-lucide-car" class="h-6 w-6 text-gray-400" />
				</div>
				<div class="flex-1">
					<div class="font-medium text-white">Режим водителя</div>
					<div class="text-xs text-gray-400">Принимать заказы</div>
				</div>
				<UIcon name="i-lucide-chevron-right" class="h-5 w-5 text-gray-500" />
			</button>
		</div>

		<!-- How it works - minimal -->
		<div class="mt-8 rounded-2xl bg-gray-800/30 p-5">
			<div class="mb-3 text-xs uppercase tracking-wider text-gray-500">Как это работает</div>
			<div class="space-y-2 text-sm text-gray-400">
				<div class="flex gap-2">
					<span class="text-cyan-400">1.</span>
					<span>Создаёшь заказ с ценой</span>
				</div>
				<div class="flex gap-2">
					<span class="text-cyan-400">2.</span>
					<span>Водитель принимает</span>
				</div>
				<div class="flex gap-2">
					<span class="text-cyan-400">3.</span>
					<span>Едете по честной цене</span>
				</div>
			</div>
		</div>
	</div>
</template>
