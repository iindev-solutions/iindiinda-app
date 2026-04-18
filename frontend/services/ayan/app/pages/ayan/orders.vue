<script setup lang="ts">
// Page metadata
definePageMeta({
	layout: 'default'
})

// Composables
const { t } = useI18n()
const { hapticFeedback } = useTg()
const { get, post } = useTaxiAPI()

// Types
interface Order {
	id: number
	pickup: string
	destination: string
	price: number
	createdAt: string
}

// State
const orders = ref<Order[]>([])
const isLoading = ref(true)
const acceptingOrderId = ref<number | null>(null)

// Fetch available orders
async function fetchOrders() {
	isLoading.value = true
	try {
		const response = await get<{ data: Order[] }>('/ayan/orders')
		orders.value = response.data
	} catch (error) {
		console.error('Failed to fetch orders:', error)
	} finally {
		isLoading.value = false
	}
}

// Accept order
async function acceptOrder(orderId: number) {
	acceptingOrderId.value = orderId
	hapticFeedback('impact')

	try {
		await post(`/ayan/orders/${orderId}/accept`)
		// Navigate to active ride page
		navigateTo('/ayan/active-ride')
	} catch (error) {
		console.error('Failed to accept order:', error)
	} finally {
		acceptingOrderId.value = null
	}
}

// Refresh orders
function refreshOrders() {
	fetchOrders()
}

// Format price
function formatPrice(price: number): string {
	return new Intl.NumberFormat('ru-RU').format(price)
}

// Initialize
onMounted(() => {
	fetchOrders()
})
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<!-- Header -->
			<header class="mb-6 pt-2">
				<div class="flex items-center justify-between">
					<div>
						<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
							{{ t('ayan.orders.header.subtitle') }}
						</div>
						<h1 class="text-2xl font-medium tracking-tight text-[#eff3f5]">
							{{ t('ayan.orders.title') }}
						</h1>
					</div>
					<UButton variant="ghost" color="gray" size="sm" :loading="isLoading" @click="refreshOrders">
						<UIcon name="i-lucide-refresh-cw" class="h-5 w-5" />
					</UButton>
				</div>
			</header>

			<!-- Loading State -->
			<div v-if="isLoading" class="space-y-4">
				<UCard v-for="i in 3" :key="i" class="rounded-2xl border-gray-800 bg-level-1">
					<USkeleton class="mb-2 h-5 w-3/4" />
					<USkeleton class="h-4 w-1/2" />
				</UCard>
			</div>

			<!-- Empty State -->
			<div v-else-if="orders.length === 0" class="py-12 text-center">
				<div class="mb-4 flex justify-center">
					<div class="flex h-20 w-20 items-center justify-center rounded-full bg-gray-800">
						<UIcon name="i-lucide-inbox" class="h-10 w-10 text-gray-500" />
					</div>
				</div>
				<h3 class="mb-2 text-lg font-medium text-white">
					{{ t('ayan.orders.empty.title') }}
				</h3>
				<p class="text-sm text-gray-400">
					{{ t('ayan.orders.empty.description') }}
				</p>
			</div>

			<!-- Orders List -->
			<div v-else class="space-y-4">
				<UCard v-for="order in orders" :key="order.id" class="rounded-2xl border-gray-800 bg-level-1">
					<div class="space-y-4">
						<!-- Route -->
						<div class="space-y-2">
							<div class="flex items-start gap-3">
								<div class="mt-1 flex h-2 w-2 shrink-0 rounded-full bg-green-400"></div>
								<div>
									<div class="mb-0.5 text-xs text-gray-500">
										{{ t('ayan.orders.card.pickup') }}
									</div>
									<div class="text-sm text-white">{{ order.pickup }}</div>
								</div>
							</div>
							<div class="ml-1 h-4 w-px bg-gray-700"></div>
							<div class="flex items-start gap-3">
								<div class="mt-1 flex h-2 w-2 shrink-0 rounded-full bg-cyan-400"></div>
								<div>
									<div class="mb-0.5 text-xs text-gray-500">
										{{ t('ayan.orders.card.destination') }}
									</div>
									<div class="text-sm text-white">{{ order.destination }}</div>
								</div>
							</div>
						</div>

						<!-- Price and Accept -->
						<div class="flex items-center justify-between border-t border-gray-800 pt-4">
							<div>
								<div class="mb-0.5 text-xs text-gray-500">
									{{ t('ayan.orders.card.price') }}
								</div>
								<div class="text-xl font-semibold text-cyan-400">{{ formatPrice(order.price) }} ₽</div>
							</div>
							<UButton
								color="primary"
								size="md"
								:loading="acceptingOrderId === order.id"
								@click="acceptOrder(order.id)"
							>
								{{ t('ayan.orders.card.acceptButton') }}
							</UButton>
						</div>
					</div>
				</UCard>
			</div>
		</div>
	</div>
</template>
