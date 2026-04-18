<script setup lang="ts">
// Page metadata
definePageMeta({
	layout: 'default'
})

// Composables
const { t } = useI18n()
const { hapticFeedback, showBackButton, hideBackButton, onBackButtonClicked } = useTg()
const { get, post } = useTaxiAPI()

// Types
interface DriverStatus {
	role: 'passenger' | 'driver'
	isAvailable: boolean
	stats: {
		completedOrders: number
		rating: number
	}
}

// State
const isAvailable = ref(false)
const isSwitching = ref(false)
const isLoading = ref(true)
const driverStats = ref({
	completedOrders: 0,
	rating: 5.0
})

// Fetch driver initial status
async function fetchDriverStatus() {
	isLoading.value = true
	try {
		const response = await get<{ data: DriverStatus }>('/user/me')
		const data = response.data

		// If user is driver and available, set toggle on
		if (data.role === 'driver') {
			isAvailable.value = data.isAvailable
			driverStats.value = data.stats
		}
	} catch (error) {
		console.error('Failed to fetch driver status:', error)
	} finally {
		isLoading.value = false
	}
}

// Switch availability
async function toggleAvailability() {
	isSwitching.value = true
	hapticFeedback('impact')

	try {
		// First switch role to driver if not already
		await post('/user/switch-role', { role: 'driver' })

		// Toggle availability
		const newStatus = !isAvailable.value
		await post('/user/availability', { available: newStatus })

		isAvailable.value = newStatus

		if (isAvailable.value) {
			// Navigate to orders page when becoming available
			navigateTo('/ayan/orders')
		}
	} catch (error) {
		console.error('Failed to toggle availability:', error)
		// Revert toggle on error
		isAvailable.value = !isAvailable.value
		// Show error toast (will add toast system later)
	} finally {
		isSwitching.value = false
	}
}

// Initialize back button
onMounted(() => {
	fetchDriverStatus()

	// Show back button and handle navigation
	showBackButton()
	onBackButtonClicked(() => {
		navigateTo('/ayan')
	})
})

onUnmounted(() => {
	hideBackButton()
})
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<!-- Loading State -->
			<div v-if="isLoading" class="flex h-[60vh] items-center justify-center">
				<ULoadingIndicator />
			</div>

			<template v-else>
				<!-- Header -->
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

				<!-- Availability Toggle -->
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
							v-model="isAvailable"
							:loading="isSwitching"
							@update:model-value="toggleAvailability"
						/>
					</div>
				</UCard>

				<!-- Stats Cards -->
				<div class="mb-6 grid grid-cols-2 gap-4">
					<UCard class="rounded-2xl border-gray-800 bg-level-1">
						<div class="text-center">
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.driver.stats.completed') }}
							</div>
							<div class="text-2xl font-semibold text-white">
								{{ driverStats.completedOrders }}
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

				<!-- Instructions -->
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
