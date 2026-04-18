<script setup lang="ts">
// Page metadata
definePageMeta({
	layout: 'default'
})

// Composables
const { t } = useI18n()
const { hapticFeedback } = useTg()

// State
const showConfetti = ref(true)

// Navigation handlers
function goToHome() {
	hapticFeedback('impact')
	navigateTo('/ayan')
}

function goToCreateOrder() {
	hapticFeedback('impact')
	navigateTo('/ayan/create')
}

// Hide confetti after animation
onMounted(() => {
	hapticFeedback('notification')
	setTimeout(() => {
		showConfetti.value = false
	}, 3000)
})
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<!-- Success Animation -->
			<div class="flex flex-col items-center justify-center py-12">
				<!-- Animated Check Circle -->
				<div class="relative mb-8">
					<div v-if="showConfetti" class="absolute inset-0 animate-ping rounded-full bg-cyan-500/30"></div>
					<div
						class="flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-cyan-400 to-cyan-600"
					>
						<UIcon name="i-lucide-check" class="h-12 w-12 text-white" />
					</div>
				</div>

				<!-- Title -->
				<h1 class="mb-2 text-center text-2xl font-medium text-white">
					{{ t('ayan.order.status.completed.title') }}
				</h1>
				<p class="mb-8 text-center text-gray-400">
					{{ t('ayan.order.status.completed.description') }}
				</p>

				<!-- Rating Card -->
				<UCard class="mb-6 w-full rounded-2xl border-gray-800 bg-level-1">
					<div class="text-center">
						<div class="mb-3 text-sm text-gray-400">Как прошла поездка?</div>
						<div class="flex justify-center gap-2">
							<UButton v-for="i in 5" :key="i" variant="ghost" color="gray" size="lg" class="p-2">
								<UIcon name="i-lucide-star" class="h-8 w-8 text-yellow-400" />
							</UButton>
						</div>
					</div>
				</UCard>

				<!-- Action Buttons -->
				<div class="w-full space-y-3">
					<UButton block size="lg" color="primary" @click="goToCreateOrder">
						<UIcon name="i-lucide-plus" class="mr-2 h-5 w-5" />
						Новая поездка
					</UButton>

					<UButton block size="lg" variant="outline" color="gray" @click="goToHome">
						<UIcon name="i-lucide-home" class="mr-2 h-5 w-5" />
						На главную
					</UButton>
				</div>
			</div>

			<!-- Trip Stats -->
			<UCard class="mt-8 rounded-2xl border-gray-800 bg-level-1">
				<div class="space-y-4">
					<h3 class="text-center font-medium text-white">Детали поездки</h3>
					<div class="grid grid-cols-2 gap-4">
						<div class="text-center">
							<div class="mb-1 text-xs text-gray-500">Дата</div>
							<div class="text-sm text-white">{{ new Date().toLocaleDateString('ru-RU') }}</div>
						</div>
						<div class="text-center">
							<div class="mb-1 text-xs text-gray-500">Время</div>
							<div class="text-sm text-white">
								{{ new Date().toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' }) }}
							</div>
						</div>
					</div>
				</div>
			</UCard>
		</div>
	</div>
</template>
