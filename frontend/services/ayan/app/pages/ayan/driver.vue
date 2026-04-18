<script setup lang="ts">
import type { User } from '~/types/api'

definePageMeta({
	layout: 'default'
})

const { t } = useI18n()
const { hapticFeedback } = useTg()
const { get, post } = useTaxiAPI()
const toast = useToast()

const isAvailable = ref(false)
const isSwitching = ref(false)
const isLoading = ref(true)
const driverStats = ref({
	completed_orders: 0,
	rating: 5.0
})

async function fetchDriverStatus() {
	isLoading.value = true
	try {
		const response = await get<{ data: User }>('/user/me')
		const data = response.data
		if (data.role === 'driver') {
			isAvailable.value = data.is_available ?? false
			driverStats.value.completed_orders = data.completed_orders ?? 0
			driverStats.value.rating = data.rating ?? 5.0
		}
	} catch (error: any) {
		console.error('Failed to fetch driver status:', error)
		toast.add({ title: error?.message || t('ayan.driver.fetchError'), color: 'gray' })
	} finally {
		isLoading.value = false
	}
}

async function toggleAvailability() {
	isSwitching.value = true
	hapticFeedback('impact')

	try {
		await post('/user/switch-role', { role: 'driver' })
		const newStatus = !isAvailable.value
		await post('/user/availability', { is_available: newStatus })
		isAvailable.value = newStatus

		if (isAvailable.value) {
			navigateTo('/ayan/orders')
		}
	} catch (error: any) {
		console.error('Failed to toggle availability:', error)
		toast.add({ title: error?.message || t('ayan.driver.toggleError'), color: 'gray' })
	} finally {
		isSwitching.value = false
	}
}

onMounted(() => {
	fetchDriverStatus()
})
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<div v-if="isLoading" class="flex h-[60vh] items-center justify-center">
				<UIcon name="i-lucide-loader-circle" class="h-8 w-8 animate-spin text-cyan-400" />
			</div>

			<template v-else>
				<header class="mb-8 pt-2">
					<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
						{{ t('ayan.driver.header.subtitle') }}
					</div>
					<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">
						{{ t('ayan.driver.title') }}
					</h1>
					<p class="text-sm leading-relaxed text-gray-300">
						{{ t('ayan.driver.description') }}
					</p>
				</header>

				<UCard class="mb-6 rounded-2xl border-gray-800 bg-level-1">
					<div class="flex items-center justify-between">
						<div>
							<h3 class="font-medium text-white">
								{{ t('ayan.driver.availability.title') }}
							</h3>
							<p class="text-sm text-gray-400">
								{{
									isAvailable
										? t('ayan.driver.availability.online')
										: t('ayan.driver.availability.offline')
								}}
							</p>
						</div>
						<USwitch
							:model-value="isAvailable"
							:loading="isSwitching"
							@update:model-value="toggleAvailability"
						/>
					</div>
				</UCard>

				<div class="mb-6 grid grid-cols-2 gap-4">
					<UCard class="rounded-2xl border-gray-800 bg-level-1">
						<div class="text-center">
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.driver.stats.completed') }}
							</div>
							<div class="text-2xl font-semibold text-white">
								{{ driverStats.completed_orders }}
							</div>
						</div>
					</UCard>
					<UCard class="rounded-2xl border-gray-800 bg-level-1">
						<div class="text-center">
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.driver.stats.rating') }}
							</div>
							<div class="text-2xl font-semibold text-cyan-400">
								{{ driverStats.rating.toFixed(1) }}
							</div>
						</div>
					</UCard>
				</div>

				<UCard class="rounded-2xl border-cyan-500/20 bg-level-1">
					<div class="space-y-4">
						<h3 class="font-medium text-white">
							{{ t('ayan.driver.instructions.title') }}
						</h3>
						<div class="space-y-3 text-sm text-gray-400">
							<div class="flex items-start gap-3">
								<div
									class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-xs text-cyan-400"
								>
									1
								</div>
								<span>{{ t('ayan.driver.instructions.step1') }}</span>
							</div>
							<div class="flex items-start gap-3">
								<div
									class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-xs text-cyan-400"
								>
									2
								</div>
								<span>{{ t('ayan.driver.instructions.step2') }}</span>
							</div>
							<div class="flex items-start gap-3">
								<div
									class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-xs text-cyan-400"
								>
									3
								</div>
								<span>{{ t('ayan.driver.instructions.step3') }}</span>
							</div>
						</div>
					</div>
				</UCard>
			</template>
		</div>
	</div>
</template>
