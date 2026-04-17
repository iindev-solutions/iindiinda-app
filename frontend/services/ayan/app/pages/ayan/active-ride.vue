<script setup lang="ts">
// Page metadata
definePageMeta({
	layout: 'default'
})

// Composables
const { t } = useI18n()
const { hapticFeedback } = useTg()
const { get, post } = useAPI()
const router = useRouter()

// Types
type ActiveStatus = 'matched' | 'arrived' | 'on-trip'

interface Order {
	id: number
	status: ActiveStatus
	pickup: string
	destination: string
	price: number
	passenger?: {
		name: string
		phone: string
	}
}

// State
const order = ref<Order | null>(null)
const isLoading = ref(true)
const actionInProgress = ref(false)

// Fetch current active order
async function fetchActiveOrder() {
	isLoading.value = true
	try {
		const response = await get<{ data: Order }>('/ayan/orders/active')
		order.value = response.data
	} catch (error) {
		console.error('Failed to fetch active order:', error)
		// No active order - go back to orders list
		router.push('/ayan/orders')
	} finally {
		isLoading.value = false
	}
}

// Driver actions
async function markArrived() {
	if (!order.value) return
	actionInProgress.value = true
	hapticFeedback('impact')

	try {
		await post(`/ayan/orders/${order.value.id}/arrive`)
		await fetchActiveOrder()
	} catch (error) {
		console.error('Failed to mark arrived:', error)
	} finally {
		actionInProgress.value = false
	}
}

async function startTrip() {
	if (!order.value) return
	actionInProgress.value = true
	hapticFeedback('impact')

	try {
		await post(`/ayan/orders/${order.value.id}/start`)
		await fetchActiveOrder()
	} catch (error) {
		console.error('Failed to start trip:', error)
	} finally {
		actionInProgress.value = false
	}
}

async function completeTrip() {
	if (!order.value) return
	actionInProgress.value = true
	hapticFeedback('impact')

	try {
		await post(`/ayan/orders/${order.value.id}/complete`)
		router.push('/ayan/complete')
	} catch (error) {
		console.error('Failed to complete trip:', error)
	} finally {
		actionInProgress.value = false
	}
}

// Status display
const statusConfig: Record<ActiveStatus, { title: string; description: string; color: string; icon: string }> = {
	matched: {
		title: t('ayan.activeRide.status.matched.title'),
		description: t('ayan.activeRide.status.matched.description'),
		color: 'green',
		icon: 'i-lucide-check-circle'
	},
	arrived: {
		title: t('ayan.activeRide.status.arrived.title'),
		description: t('ayan.activeRide.status.arrived.description'),
		color: 'cyan',
		icon: 'i-lucide-map-pin'
	},
	'on-trip': {
		title: t('ayan.activeRide.status.onTrip.title'),
		description: t('ayan.activeRide.status.onTrip.description'),
		color: 'cyan',
		icon: 'i-lucide-car'
	}
}

// Format price
function formatPrice(price: number): string {
	return new Intl.NumberFormat('ru-RU').format(price)
}

// Initialize
onMounted(() => {
	fetchActiveOrder()
})
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<!-- Loading State -->
			<div v-if="isLoading" class="flex h-[60vh] items-center justify-center">
				<ULoadingIndicator />
			</div>

			<!-- Active Ride Display -->
			<div v-else-if="order" class="space-y-6">
				<!-- Status Card -->
				<UCard class="rounded-2xl border-cyan-500/20 bg-level-1">
					<div class="flex items-center gap-4">
						<div
							class="flex h-14 w-14 items-center justify-center rounded-2xl"
							:class="`bg-${statusConfig[order.status].color}-500/20`"
						>
							<UIcon
								:name="statusConfig[order.status].icon"
								class="h-7 w-7"
								:class="`text-${statusConfig[order.status].color}-400`"
							/>
						</div>
						<div>
							<h2 class="text-lg font-medium text-white">
								{{ statusConfig[order.status].title }}
							</h2>
							<p class="text-sm text-gray-400">
								{{ statusConfig[order.status].description }}
							</p>
						</div>
					</div>
				</UCard>

				<!-- Passenger Info (if available) -->
				<UCard v-if="order.passenger" class="rounded-2xl border-gray-800 bg-level-1">
					<div class="flex items-center gap-4">
						<UAvatar size="lg" icon="i-lucide-user" class="bg-gray-700" />
						<div>
							<h3 class="font-medium text-white">{{ order.passenger.name }}</h3>
							<div class="text-sm text-gray-400">{{ order.passenger.phone }}</div>
						</div>
					</div>
				</UCard>

				<!-- Order Details -->
				<UCard class="rounded-2xl border-gray-800 bg-level-1">
					<div class="space-y-4">
						<div>
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.activeRide.details.pickup') }}
							</div>
							<div class="text-white">{{ order.pickup }}</div>
						</div>
						<div>
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.activeRide.details.destination') }}
							</div>
							<div class="text-white">{{ order.destination }}</div>
						</div>
						<div class="border-t border-gray-800 pt-4">
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.activeRide.details.price') }}
							</div>
							<div class="text-xl font-semibold text-cyan-400">{{ formatPrice(order.price) }} ₽</div>
						</div>
					</div>
				</UCard>

				<!-- Action Buttons -->
				<div class="space-y-3 pt-4">
					<!-- Mark Arrived -->
					<UButton
						v-if="order.status === 'matched'"
						block
						size="lg"
						color="primary"
						:loading="actionInProgress"
						@click="markArrived"
					>
						<UIcon name="i-lucide-map-pin" class="mr-2 h-5 w-5" />
						{{ t('ayan.activeRide.actions.markArrived') }}
					</UButton>

					<!-- Start Trip -->
					<UButton
						v-if="order.status === 'arrived'"
						block
						size="lg"
						color="primary"
						:loading="actionInProgress"
						@click="startTrip"
					>
						<UIcon name="i-lucide-play" class="mr-2 h-5 w-5" />
						{{ t('ayan.activeRide.actions.startTrip') }}
					</UButton>

					<!-- Complete Trip -->
					<UButton
						v-if="order.status === 'on-trip'"
						block
						size="lg"
						color="primary"
						:loading="actionInProgress"
						@click="completeTrip"
					>
						<UIcon name="i-lucide-check" class="mr-2 h-5 w-5" />
						{{ t('ayan.activeRide.actions.completeTrip') }}
					</UButton>
				</div>
			</div>
		</div>
	</div>
</template>
