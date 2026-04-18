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

type ActiveStatus = 'accepted' | 'arrived' | 'in_progress'

const order = ref<TaxiOrder | null>(null)
const isLoading = ref(true)
const actionInProgress = ref(false)

async function fetchActiveOrder(silent = false) {
	if (!silent) isLoading.value = true
	try {
		const response = await get<{ data: TaxiOrder }>('/ayan/orders/active')
		order.value = response.data
		if (order.value?.status === 'cancelled') {
			pausePolling()
			toast.add({ title: t('ayan.activeRide.cancelled'), color: 'gray' })
			navigateTo('/ayan/orders')
		}
	} catch (error: any) {
		if (!silent) {
			console.error('Failed to fetch active order:', error)
			toast.add({ title: error?.message || t('ayan.activeRide.fetchError'), color: 'gray' })
			navigateTo('/ayan/orders')
		}
	} finally {
		if (!silent) isLoading.value = false
	}
}

async function markArrived() {
	if (!order.value) return
	actionInProgress.value = true
	hapticFeedback('impact')

	try {
		await post(`/ayan/orders/${order.value.id}/arrive`)
		await fetchActiveOrder()
	} catch (error: any) {
		console.error('Failed to mark arrived:', error)
		toast.add({ title: error?.message || t('ayan.activeRide.actionError'), color: 'gray' })
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
	} catch (error: any) {
		console.error('Failed to start trip:', error)
		toast.add({ title: error?.message || t('ayan.activeRide.actionError'), color: 'gray' })
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
		navigateTo('/ayan/complete')
	} catch (error: any) {
		console.error('Failed to complete trip:', error)
		toast.add({ title: error?.message || t('ayan.activeRide.actionError'), color: 'gray' })
	} finally {
		actionInProgress.value = false
	}
}

const statusConfig: Record<
	ActiveStatus,
	{ title: string; description: string; icon: string; bgClass: string; textClass: string }
> = {
	accepted: {
		title: t('ayan.activeRide.status.accepted'),
		description: t('ayan.activeRide.status.accepted.description'),
		icon: 'i-lucide-check-circle',
		bgClass: 'bg-green-500/20',
		textClass: 'text-green-400'
	},
	arrived: {
		title: t('ayan.activeRide.status.arrived'),
		description: t('ayan.activeRide.status.arrived.description'),
		icon: 'i-lucide-map-pin',
		bgClass: 'bg-cyan-500/20',
		textClass: 'text-cyan-400'
	},
	in_progress: {
		title: t('ayan.activeRide.status.inProgress'),
		description: t('ayan.activeRide.status.inProgress.description'),
		icon: 'i-lucide-car',
		bgClass: 'bg-cyan-500/20',
		textClass: 'text-cyan-400'
	}
}

function formatPrice(price: number): string {
	return new Intl.NumberFormat('ru-RU').format(price)
}

const { pause: pausePolling } = useIntervalFn(() => fetchActiveOrder(true), 5000)

onMounted(() => {
	fetchActiveOrder()
})

onUnmounted(() => {
	pausePolling()
})
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<div v-if="isLoading" class="flex h-[60vh] items-center justify-center">
				<UIcon name="i-lucide-loader-circle" class="h-8 w-8 animate-spin text-cyan-400" />
			</div>

			<div v-else-if="order" class="space-y-6">
				<UCard class="rounded-2xl border-cyan-500/20 bg-level-1">
					<div class="flex items-center gap-4">
						<div
							class="flex h-14 w-14 items-center justify-center rounded-2xl"
							:class="statusConfig[order.status as ActiveStatus].bgClass"
						>
							<UIcon
								:name="statusConfig[order.status as ActiveStatus].icon"
								class="h-7 w-7"
								:class="statusConfig[order.status as ActiveStatus].textClass"
							/>
						</div>
						<div>
							<h2 class="text-lg font-medium text-white">
								{{ statusConfig[order.status as ActiveStatus].title }}
							</h2>
							<p class="text-sm text-gray-400">
								{{ statusConfig[order.status as ActiveStatus].description }}
							</p>
						</div>
					</div>
				</UCard>

				<UCard v-if="order.driver" class="rounded-2xl border-gray-800 bg-level-1">
					<div class="flex items-center gap-4">
						<UAvatar size="lg" icon="i-lucide-user" class="bg-gray-700" />
						<div>
							<h3 class="font-medium text-white">{{ order.driver.first_name }}</h3>
							<div v-if="order.driver.rating" class="text-sm text-gray-400">
								{{ order.driver.rating.toFixed(1) }} ★
							</div>
						</div>
					</div>
				</UCard>

				<UCard class="rounded-2xl border-gray-800 bg-level-1">
					<div class="space-y-4">
						<div>
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.activeRide.details.pickup') }}
							</div>
							<div class="text-white">{{ order.from_address }}</div>
						</div>
						<div>
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.activeRide.details.destination') }}
							</div>
							<div class="text-white">{{ order.to_address }}</div>
						</div>
						<div class="border-t border-gray-800 pt-4">
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.activeRide.details.price') }}
							</div>
							<div class="text-xl font-semibold text-cyan-400">{{ formatPrice(order.price) }} ₽</div>
						</div>
					</div>
				</UCard>

				<div class="space-y-3 pt-4">
					<UButton
						v-if="order.status === 'accepted'"
						block
						size="lg"
						color="primary"
						:loading="actionInProgress"
						@click="markArrived"
					>
						<UIcon name="i-lucide-map-pin" class="mr-2 h-5 w-5" />
						{{ t('ayan.activeRide.actions.markArrived') }}
					</UButton>

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

					<UButton
						v-if="order.status === 'in_progress'"
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
