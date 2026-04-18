<script setup lang="ts">
definePageMeta({
	layout: 'default'
})

const { t } = useI18n()
const { hapticFeedback } = useTg()

function goToCreateOrder() {
	hapticFeedback('impact')
	navigateTo('/ayan/create')
}

function goToDriverMode() {
	hapticFeedback('impact')
	navigateTo('/ayan/driver')
}

function goToMyOrder() {
	hapticFeedback('impact')
	navigateTo('/ayan/my-order')
}
</script>

<template>
	<div class="flex min-h-screen flex-col px-5 py-8">
		<div class="mb-8">
			<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">{{ t('ayan.category') }}</div>
			<h1 class="mb-2 text-2xl font-semibold text-white">{{ t('ayan.name') }}</h1>
			<p class="text-sm text-gray-400">{{ t('ayan.desc') }}</p>
		</div>

		<div class="mb-auto space-y-3">
			<button
				class="flex w-full items-center gap-4 rounded-2xl bg-cyan-500/20 p-5 text-left transition-transform active:scale-95"
				@click="goToCreateOrder"
			>
				<div class="flex h-12 w-12 items-center justify-center rounded-xl bg-cyan-500/30">
					<UIcon name="i-lucide-plus" class="h-6 w-6 text-cyan-400" />
				</div>
				<div class="flex-1">
					<div class="font-medium text-white">{{ t('ayan.createOrder') }}</div>
					<div class="text-xs text-gray-400">{{ t('ayan.create.subtitle') }}</div>
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
					<div class="font-medium text-white">{{ t('ayan.myOrder') }}</div>
					<div class="text-xs text-gray-400">{{ t('ayan.myOrderDesc') }}</div>
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
					<div class="font-medium text-white">{{ t('ayan.driverMode') }}</div>
					<div class="text-xs text-gray-400">{{ t('ayan.driverModeDesc') }}</div>
				</div>
				<UIcon name="i-lucide-chevron-right" class="h-5 w-5 text-gray-500" />
			</button>
		</div>

		<div class="mt-8 rounded-2xl bg-gray-800/30 p-5">
			<div class="mb-3 text-xs uppercase tracking-wider text-gray-500">{{ t('ayan.howItWorks') }}</div>
			<div class="space-y-2 text-sm text-gray-400">
				<div class="flex gap-2">
					<span class="text-cyan-400">1.</span>
					<span>{{ t('ayan.step1') }}</span>
				</div>
				<div class="flex gap-2">
					<span class="text-cyan-400">2.</span>
					<span>{{ t('ayan.step2') }}</span>
				</div>
				<div class="flex gap-2">
					<span class="text-cyan-400">3.</span>
					<span>{{ t('ayan.step3') }}</span>
				</div>
			</div>
		</div>
	</div>
</template>
