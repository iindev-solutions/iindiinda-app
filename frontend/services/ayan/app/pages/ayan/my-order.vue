<script setup lang="ts">
import { useIntervalFn } from '@vueuse/core'

// Page metadata
definePageMeta({
	layout: 'default'
})

// Composables
const { t } = useI18n()
const { hapticFeedback, showBackButton, hideBackButton, onBackButtonClicked } = useTg()
const { get, post } = useTaxiAPI() // Используем Taxi API (мок или реальный)
const toast = useToast()
const router = useRouter()

// Order state
type OrderStatus = 'searching' | 'matched' | 'arrived' | 'on-trip' | 'completed' | 'cancelled'

interface Order {
	id: number
	status: OrderStatus
	pickup: string
	destination: string
	price: number
	driver?: {
		name: string
		rating: number
	}
	createdAt: string
}

// API response type
interface ApiResponse<T> {
	data: T
}

const order = ref<Order | null>(null)
const isLoading = ref(true)
const showCancelConfirm = ref(false)
const fetchError = ref<string | null>(null)

// Polling for order status
const { pause, resume, isActive } = useIntervalFn(
	async () => {
		if (!order.value) return

		try {
			const response = await get<ApiResponse<Order>>(`/ayan/orders/${order.value.id}`)
			order.value = response.data
			fetchError.value = null

			// Navigate to completed page if trip is done
			if (order.value && order.value.status === 'completed') {
				pause()
				router.push('/ayan/complete')
			}
		} catch (error: any) {
			console.error('Failed to fetch order status:', error)
			// Don't show error on first fetch, only on subsequent polling failures
			if (isActive.value) {
				fetchError.value = error?.message || t('ayan.order.fetchError')
			}
		}
	},
	5000, // Poll every 5 seconds
	{ immediate: false }
)

// Fetch current active order
async function fetchActiveOrder() {
	isLoading.value = true
	fetchError.value = null

	try {
		const response = await get<ApiResponse<Order>>('/ayan/orders/me')
		order.value = response.data

		if (order.value) {
			const activeStatuses: OrderStatus[] = ['searching', 'matched', 'arrived', 'on-trip']
			if (activeStatuses.includes(order.value.status)) {
				resume() // Start polling
			}
		}
	} catch (error: any) {
		console.error('Failed to fetch active order:', error)
		// If no active order, redirect to create page
		router.push('/ayan/create')
	} finally {
		isLoading.value = false
	}
}

// Cancel order
async function cancelOrder() {
	if (!order.value) return

	hapticFeedback('impact')

	try {
		await post(`/ayan/orders/${order.value.id}/cancel`)
		pause()
		toast.add({
			title: t('ayan.order.cancelSuccess'),
			color: 'cyan'
		})
		router.push('/ayan/create')
	} catch (error: any) {
		console.error('Failed to cancel order:', error)
		toast.add({
			title: error?.message || t('ayan.order.cancelError'),
			color: 'gray'
		})
	}
}

// Status display
const statusConfig: Record<OrderStatus, { title: string; description: string; color: string; icon: string }> = {
	searching: {
		title: t('ayan.order.status.searching.title'),
		description: t('ayan.order.status.searching.description'),
		color: 'cyan',
		icon: 'i-lucide-search'
	},
	matched: {
		title: t('ayan.order.status.matched.title'),
		description: t('ayan.order.status.matched.description'),
		color: 'green',
		icon: 'i-lucide-check-circle'
	},
	arrived: {
		title: t('ayan.order.status.arrived.title'),
		description: t('ayan.order.status.arrived.description'),
		color: 'green',
		icon: 'i-lucide-map-pin'
	},
	'on-trip': {
		title: t('ayan.order.status.onTrip.title'),
		description: t('ayan.order.status.onTrip.description'),
		color: 'cyan',
		icon: 'i-lucide-car'
	},
	completed: {
		title: t('ayan.order.status.completed.title'),
		description: t('ayan.order.status.completed.description'),
		color: 'green',
		icon: 'i-lucide-check'
	},
	cancelled: {
		title: t('ayan.order.status.cancelled.title'),
		description: t('ayan.order.status.cancelled.description'),
		color: 'gray',
		icon: 'i-lucide-x'
	}
}

// Initialize
onMounted(() => {
	fetchActiveOrder()

	// Show back button
	showBackButton()
	onBackButtonClicked(() => {
		router.push('/ayan')
	})
})

onUnmounted(() => {
	pause()
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

			<!-- Error State -->
			<div v-else-if="fetchError" class="py-12 text-center">
				<div class="mb-4 flex justify-center">
					<UIcon name="i-lucide-alert-triangle" class="h-16 w-16 text-yellow-400" />
				</div>
				<h3 class="mb-2 text-lg font-medium text-white">{{ t('common.error') }}</h3>
				<p class="mb-6 text-sm text-gray-400">{{ fetchError }}</p>
				<UButton color="cyan" @click="fetchActiveOrder">
					{{ t('common.retry') }}
				</UButton>
			</div>

			<!-- Order Display -->
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

				<!-- Pulsing Animation for Searching -->
				<div v-if="order.status === 'searching'" class="flex justify-center py-8">
					<div class="relative">
						<div class="absolute inset-0 animate-ping rounded-full bg-cyan-500/20"></div>
						<div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-cyan-500/30">
							<UIcon name="i-lucide-search" class="h-8 w-8 text-cyan-400" />
						</div>
					</div>
				</div>

				<!-- Driver Info (when matched) -->
				<UCard
					v-if="order.driver && order.status !== 'searching'"
					class="rounded-2xl border-gray-800 bg-level-1"
				>
					<div class="flex items-center gap-4">
						<UAvatar size="lg" icon="i-lucide-user" class="bg-gray-700" />
						<div>
							<h3 class="font-medium text-white">{{ order.driver.name }}</h3>
							<div class="flex items-center gap-1 text-sm text-gray-400">
								<UIcon name="i-lucide-star" class="h-4 w-4 text-yellow-400" />
								<span>{{ order.driver.rating }}</span>
							</div>
						</div>
					</div>
				</UCard>

				<!-- Order Details -->
				<UCard class="rounded-2xl border-gray-800 bg-level-1">
					<div class="space-y-4">
						<div>
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.order.details.pickup') }}
							</div>
							<div class="text-white">{{ order.pickup }}</div>
						</div>
						<div>
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.order.details.destination') }}
							</div>
							<div class="text-white">{{ order.destination }}</div>
						</div>
						<div class="border-t border-gray-800 pt-4">
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.order.details.price') }}
							</div>
							<div class="text-xl font-semibold text-cyan-400">{{ order.price }} ₽</div>
						</div>
					</div>
				</UCard>

				<!-- Cancel Button -->
				<UButton
					v-if="['searching', 'matched'].includes(order.status)"
					block
					variant="outline"
					color="gray"
					size="lg"
					class="mt-8"
					@click="showCancelConfirm = true"
				>
					{{ t('ayan.order.cancelButton') }}
				</UButton>
			</div>
		</div>

		<!-- Cancel Confirmation Modal -->
		<UModal v-model:open="showCancelConfirm">
			<UCard>
				<template #header>
					<h3 class="text-lg font-medium text-white">
						{{ t('ayan.order.cancelConfirm.title') }}
					</h3>
				</template>
				<p class="text-gray-400">
					{{ t('ayan.order.cancelConfirm.message') }}
				</p>
				<template #footer>
					<div class="flex gap-3">
						<UButton variant="ghost" @click="showCancelConfirm = false">
							{{ t('common.cancel') }}
						</UButton>
						<UButton color="gray" @click="cancelOrder">
							{{ t('ayan.order.cancelConfirm.confirm') }}
						</UButton>
					</div>
				</template>
			</UCard>
		</UModal>
	</div>
</template>
