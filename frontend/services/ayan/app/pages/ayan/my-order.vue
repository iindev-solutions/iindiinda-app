<script setup lang="ts">
import { useIntervalFn } from '@vueuse/core'
import type { TaxiOrder, TaxiOrderStatus } from '~/types/api'

definePageMeta({
	layout: 'default'
})

const { t } = useI18n()
const { hapticFeedback } = useTg()
const { get, post } = useTaxiAPI()
const toast = useToast()

interface ApiResponse<T> {
	data: T
}

const order = ref<TaxiOrder | null>(null)
const isLoading = ref(true)
const showCancelConfirm = ref(false)
const fetchError = ref<string | null>(null)

const { pause, resume, isActive } = useIntervalFn(
	async () => {
		if (!order.value) return

		try {
			const response = await get<ApiResponse<TaxiOrder>>(`/ayan/orders/${order.value.id}`)
			order.value = response.data
			fetchError.value = null

			if (order.value && order.value.status === 'completed') {
				pause()
				navigateTo('/ayan/complete')
			}
		} catch (error: any) {
			console.error('Failed to fetch order status:', error)
			if (isActive.value) {
				fetchError.value = error?.message || t('ayan.order.fetchError')
			}
		}
	},
	5000,
	{ immediate: false }
)

async function fetchActiveOrder() {
	isLoading.value = true
	fetchError.value = null

	try {
		const response = await get<ApiResponse<TaxiOrder>>('/ayan/orders/my')
		order.value = response.data

		if (order.value) {
			const activeStatuses: TaxiOrderStatus[] = ['open', 'accepted', 'arrived', 'in_progress']
			if (activeStatuses.includes(order.value.status)) {
				resume()
			}
		}
	} catch (error: any) {
		console.error('Failed to fetch active order:', error)
		order.value = null
	} finally {
		isLoading.value = false
	}
}

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
		navigateTo('/ayan')
	} catch (error: any) {
		console.error('Failed to cancel order:', error)
		toast.add({
			title: error?.message || t('ayan.order.cancelError'),
			color: 'gray'
		})
	}
}

const statusConfig: Record<TaxiOrderStatus, { title: string; icon: string; bgClass: string; textClass: string }> = {
	open: {
		title: t('ayan.order.status.open'),
		icon: 'i-lucide-search',
		bgClass: 'bg-cyan-500/20',
		textClass: 'text-cyan-400'
	},
	accepted: {
		title: t('ayan.order.status.accepted'),
		icon: 'i-lucide-check-circle',
		bgClass: 'bg-green-500/20',
		textClass: 'text-green-400'
	},
	arrived: {
		title: t('ayan.order.status.arrived'),
		icon: 'i-lucide-map-pin',
		bgClass: 'bg-green-500/20',
		textClass: 'text-green-400'
	},
	in_progress: {
		title: t('ayan.order.status.inProgress'),
		icon: 'i-lucide-car',
		bgClass: 'bg-cyan-500/20',
		textClass: 'text-cyan-400'
	},
	completed: {
		title: t('ayan.order.status.completed'),
		icon: 'i-lucide-check',
		bgClass: 'bg-green-500/20',
		textClass: 'text-green-400'
	},
	cancelled: {
		title: t('ayan.order.status.cancelled'),
		icon: 'i-lucide-x',
		bgClass: 'bg-gray-500/20',
		textClass: 'text-gray-400'
	}
}

onMounted(() => {
	fetchActiveOrder()
})

onUnmounted(() => {
	pause()
})
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<div v-if="isLoading" class="flex h-[60vh] items-center justify-center">
				<UIcon name="i-lucide-loader-circle" class="h-8 w-8 animate-spin text-cyan-400" />
			</div>

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

			<div v-else-if="order" class="space-y-6">
				<UCard class="rounded-2xl border-cyan-500/20 bg-level-1">
					<div class="flex items-center gap-4">
						<div
							class="flex h-14 w-14 items-center justify-center rounded-2xl"
							:class="statusConfig[order.status].bgClass"
						>
							<UIcon
								:name="statusConfig[order.status].icon"
								class="h-7 w-7"
								:class="statusConfig[order.status].textClass"
							/>
						</div>
						<div>
							<h2 class="text-lg font-medium text-white">
								{{ statusConfig[order.status].title }}
							</h2>
						</div>
					</div>
				</UCard>

				<div v-if="order.status === 'open'" class="flex justify-center py-8">
					<div class="relative">
						<div class="absolute inset-0 animate-ping rounded-full bg-cyan-500/20"></div>
						<div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-cyan-500/30">
							<UIcon name="i-lucide-search" class="h-8 w-8 text-cyan-400" />
						</div>
					</div>
				</div>

				<UCard v-if="order.driver && order.status !== 'open'" class="rounded-2xl border-gray-800 bg-level-1">
					<div class="flex items-center gap-4">
						<UAvatar size="lg" icon="i-lucide-user" class="bg-gray-700" />
						<div>
							<h3 class="font-medium text-white">{{ order.driver.first_name }}</h3>
							<div v-if="order.driver.rating" class="flex items-center gap-1 text-sm text-gray-400">
								<UIcon name="i-lucide-star" class="h-4 w-4 text-yellow-400" />
								<span>{{ order.driver.rating }}</span>
							</div>
						</div>
					</div>
				</UCard>

				<UCard class="rounded-2xl border-gray-800 bg-level-1">
					<div class="space-y-4">
						<div>
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.order.details.pickup') }}
							</div>
							<div class="text-white">{{ order.from_address }}</div>
						</div>
						<div>
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.order.details.destination') }}
							</div>
							<div class="text-white">{{ order.to_address }}</div>
						</div>
						<div class="border-t border-gray-800 pt-4">
							<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
								{{ t('ayan.order.details.price') }}
							</div>
							<div class="text-xl font-semibold text-cyan-400">{{ order.price }} ₽</div>
						</div>
					</div>
				</UCard>

				<UButton
					v-if="['open', 'accepted'].includes(order.status)"
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

			<div v-else class="py-12 text-center">
				<div class="mb-4 flex justify-center">
					<UIcon name="i-lucide-car" class="h-16 w-16 text-gray-500" />
				</div>
				<h3 class="mb-2 text-lg font-medium text-white">{{ t('ayan.order.noActiveOrder') }}</h3>
				<p class="mb-6 text-sm text-gray-400">{{ t('ayan.order.noActiveOrderDescription') }}</p>
				<UButton color="cyan" size="lg" @click="navigateTo('/ayan/create')">
					{{ t('ayan.order.createOrder') }}
				</UButton>
			</div>
		</div>

		<UModal
			v-model:open="showCancelConfirm"
			:title="t('ayan.order.cancelConfirm.title')"
			:description="t('ayan.order.cancelConfirm.message')"
		>
			<template #footer>
				<div class="flex gap-3">
					<UButton variant="ghost" @click="showCancelConfirm = false">
						{{ t('common.cancel') }}
					</UButton>
					<UButton variant="soft" color="gray" @click="cancelOrder">
						{{ t('ayan.order.cancelConfirm.confirm') }}
					</UButton>
				</div>
			</template>
		</UModal>
	</div>
</template>
