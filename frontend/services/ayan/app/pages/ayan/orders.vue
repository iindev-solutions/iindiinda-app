<script setup lang="ts">
import { useIntervalFn } from '@vueuse/core'
import type { TaxiOrder } from '~/types/api'

definePageMeta({
	layout: 'default'
})

const { t } = useI18n()
const { hapticFeedback } = useTg()
const { get, post } = useTaxiAPI()
const toast = useToast()

const orders = ref<TaxiOrder[]>([])
const isLoading = ref(true)
const acceptingOrderId = ref<number | null>(null)

async function fetchOrders(silent = false) {
	if (!silent) isLoading.value = true
	try {
		const response = await get<{ data: TaxiOrder[] }>('/ayan/orders')
		orders.value = response.data
	} catch (error: any) {
		if (!silent) {
			console.error('Failed to fetch orders:', error)
			toast.add({ title: error?.message || t('ayan.orders.fetchError'), color: 'gray' })
		}
	} finally {
		if (!silent) isLoading.value = false
	}
}

async function acceptOrder(orderId: number) {
	acceptingOrderId.value = orderId
	hapticFeedback('impact')

	try {
		await post(`/ayan/orders/${orderId}/accept`)
		navigateTo('/ayan/active-ride')
	} catch (error: any) {
		console.error('Failed to accept order:', error)
		toast.add({ title: error?.message || t('ayan.orders.acceptError'), color: 'gray' })
	} finally {
		acceptingOrderId.value = null
	}
}

function refreshOrders() {
	fetchOrders()
}

function formatPrice(price: number): string {
	return new Intl.NumberFormat('ru-RU').format(price)
}

const { pause: pausePolling } = useIntervalFn(() => fetchOrders(true), 10000)

onMounted(() => {
	fetchOrders()
})

onUnmounted(() => {
	pausePolling()
})
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
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

			<div v-if="isLoading" class="space-y-4">
				<UCard v-for="i in 3" :key="i" class="rounded-2xl border-gray-800 bg-level-1">
					<USkeleton class="mb-2 h-5 w-3/4" />
					<USkeleton class="h-4 w-1/2" />
				</UCard>
			</div>

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

			<div v-else class="space-y-4">
				<UCard v-for="order in orders" :key="order.id" class="rounded-2xl border-gray-800 bg-level-1">
					<div class="space-y-4">
						<div class="space-y-2">
							<div class="flex items-start gap-3">
								<div class="mt-1 flex h-2 w-2 shrink-0 rounded-full bg-green-400"></div>
								<div>
									<div class="mb-0.5 text-xs text-gray-500">
										{{ t('ayan.orders.card.pickup') }}
									</div>
									<div class="text-sm text-white">{{ order.from_address }}</div>
								</div>
							</div>
							<div class="ml-1 h-4 w-px bg-gray-700"></div>
							<div class="flex items-start gap-3">
								<div class="mt-1 flex h-2 w-2 shrink-0 rounded-full bg-cyan-400"></div>
								<div>
									<div class="mb-0.5 text-xs text-gray-500">
										{{ t('ayan.orders.card.destination') }}
									</div>
									<div class="text-sm text-white">{{ order.to_address }}</div>
								</div>
							</div>
						</div>

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
