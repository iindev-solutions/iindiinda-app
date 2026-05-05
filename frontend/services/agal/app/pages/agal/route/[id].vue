<script setup lang="ts">
import type { AgalResponse } from '../../../types/agal'

import { getApiErrorMessage } from '~/utils/api-error'
import { getServiceAccessState } from '~/utils/auth'
import { findTargetResponse } from '../../../utils/responses'

definePageMeta({ lazy: true })

const route = useRoute()
const { t } = useI18n()
const toast = useToast()
const { hapticFeedback, isInTelegram } = useTg()
const { user: authUser, isAuthenticated, isLoading: authLoading, authError } = useAuth()
const { fetchRoute, updateRoute } = useAgalRoutes()
const { fetchRouteResponses, createRouteResponse, updateResponseStatus } = useAgalResponses()
const { fetchMyResponses } = useAgalMy()

const routeId = computed(() => Number(route.params.id))

const accessState = computed(() =>
	getServiceAccessState({
		isAuthenticated: isAuthenticated.value,
		isLoading: authLoading.value,
		isInTelegram: isInTelegram.value,
		hasAuthError: !!authError.value
	})
)

const canUseAgal = computed(() => accessState.value === 'ready')

const {
	data: routeItem,
	pending: loading,
	refresh: refreshRoute
} = useLazyAsyncData(`agal-route-${routeId.value}`, () => fetchRoute(routeId.value), {
	default: () => null,
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const responses = ref<AgalResponse[]>([])
const myResponses = ref<AgalResponse[]>([])
const responding = ref(false)
const responseMessage = ref('')

const isPastRoute = computed(() =>
	routeItem.value ? isPastAyanDateTime(routeItem.value.date, routeItem.value.time) : false
)

const isOwner = computed(() => {
	if (!routeItem.value || !authUser.value) return false
	return routeItem.value.carrier.id === authUser.value.id
})

const myResponse = computed(() => findTargetResponse(myResponses.value, { routeId: routeId.value }))

const canRespond = computed(
	() =>
		!isOwner.value &&
		!myResponse.value &&
		!isPastRoute.value &&
		routeItem.value?.status === 'open' &&
		authUser.value?.role === 'sender'
)

const hasAcceptedResponse = computed(() => responses.value.some((response) => response.status === 'accepted'))

function responseStatusColor(status: AgalResponse['status']) {
	if (status === 'accepted') return 'success'
	if (status === 'rejected') return 'error'
	return 'neutral'
}

function targetStatusColor(status: string) {
	if (status === 'open') return 'success'
	if (status === 'matched') return 'primary'
	if (status === 'cancelled') return 'error'
	return 'neutral'
}

function sizeLabel(size: string) {
	return t(`agal.size.${size}`)
}

function formatPriceLabel(price: number | null) {
	if (price === null) return t('agal.route.priceNegotiable')
	return formatPrice(price, '₽')
}

async function loadResponses() {
	try {
		responses.value = await fetchRouteResponses(routeId.value)
	} catch (error) {
		responses.value = []
		console.error('[agal.route] Failed to load responses:', error)
	}
}

async function loadMyResponses() {
	try {
		myResponses.value = await fetchMyResponses()
	} catch (error) {
		myResponses.value = []
		console.error('[agal.route] Failed to load my responses:', error)
	}
}

async function handleRespond() {
	responding.value = true
	try {
		await createRouteResponse(routeId.value, { message: responseMessage.value || undefined })
		await loadMyResponses()
		hapticFeedback('notification')
		toast.add({ title: t('agal.respond.success'), color: 'success', icon: 'i-lucide-check-circle', duration: 3000 })
		responseMessage.value = ''
	} catch (error) {
		hapticFeedback('impact')
		toast.add({
			title: getApiErrorMessage(error, t('common.error')),
			color: 'error',
			icon: 'i-lucide-x-circle',
			duration: 4000
		})
	} finally {
		responding.value = false
	}
}

async function handleAccept(response: AgalResponse) {
	try {
		await updateResponseStatus(response.id, 'accepted')
		hapticFeedback('notification')
		await refreshRoute()
		await loadResponses()
	} catch (error) {
		hapticFeedback('impact')
		toast.add({
			title: getApiErrorMessage(error, t('common.error')),
			color: 'error',
			icon: 'i-lucide-x-circle',
			duration: 4000
		})
	}
}

async function handleReject(response: AgalResponse) {
	try {
		await updateResponseStatus(response.id, 'rejected')
		hapticFeedback('notification')
		await loadResponses()
	} catch (error) {
		hapticFeedback('impact')
		toast.add({
			title: getApiErrorMessage(error, t('common.error')),
			color: 'error',
			icon: 'i-lucide-x-circle',
			duration: 4000
		})
	}
}

async function handleRouteOutcome(status: 'completed' | 'cancelled') {
	if (!routeItem.value) return

	try {
		await updateRoute(routeItem.value.id, { status })
		hapticFeedback('notification')
		await refreshRoute()
		await loadResponses()
	} catch (error) {
		hapticFeedback('impact')
		toast.add({
			title: getApiErrorMessage(error, t('common.error')),
			color: 'error',
			icon: 'i-lucide-x-circle',
			duration: 4000
		})
	}
}

watch(
	canUseAgal,
	(ready) => {
		if (ready) {
			refreshRoute()
		}
	},
	{ immediate: true }
)

watch(
	isOwner,
	async (owner) => {
		if (!canUseAgal.value) {
			responses.value = []
			myResponses.value = []
			return
		}

		if (owner) {
			await loadResponses()
			return
		}

		responses.value = []
		await loadMyResponses()
	},
	{ immediate: true }
)
</script>

<template>
	<div class="app-page">
		<BackButton force-ui />

		<AppAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<div v-else-if="loading" class="flex justify-center py-12">
			<LoadingSpinner />
		</div>

		<template v-else-if="routeItem">
			<section class="app-panel app-detail-hero">
				<h1 class="app-detail-title">{{ routeItem.from_address }} → {{ routeItem.to_address }}</h1>
				<div class="app-detail-meta">
					<UBadge :color="targetStatusColor(routeItem.status)" variant="subtle" size="xs">
						{{ t(`agal.status.${routeItem.status}`) }}
					</UBadge>
					<span class="app-chip">{{ routeItem.date }}</span>
					<span v-if="routeItem.time" class="app-chip">{{ routeItem.time }}</span>
					<span class="app-chip">{{ sizeLabel(routeItem.size_label) }}</span>
					<UBadge v-if="isPastRoute" color="neutral" variant="subtle" size="xs">
						{{ t('agal.status.past') }}
					</UBadge>
				</div>
			</section>

			<section class="app-panel app-panel--soft app-detail-card">
				<div class="app-detail-stack">
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('agal.route.price') }}</span>
						<span class="app-detail-value">{{ formatPriceLabel(routeItem.price) }}</span>
					</div>
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('agal.carrier') }}</span>
						<span class="app-detail-value">{{ routeItem.carrier.name }}</span>
					</div>
					<div v-if="routeItem.weight_kg_max" class="app-detail-row">
						<span class="app-detail-label">{{ t('agal.route.weightMax') }}</span>
						<span class="app-detail-value">{{ routeItem.weight_kg_max }} кг</span>
					</div>
					<div v-if="routeItem.accepted_items" class="app-detail-divider">
						<span class="app-detail-label">{{ t('agal.route.acceptedItems') }}</span>
						<p class="app-detail-copy">{{ routeItem.accepted_items }}</p>
					</div>
					<div v-if="routeItem.restricted_items" class="app-detail-divider">
						<span class="app-detail-label">{{ t('agal.route.restrictedItems') }}</span>
						<p class="app-detail-copy">{{ routeItem.restricted_items }}</p>
					</div>
					<div v-if="routeItem.notes" class="app-detail-divider">
						<span class="app-detail-label">{{ t('agal.notes') }}</span>
						<p class="app-detail-copy">{{ routeItem.notes }}</p>
					</div>
				</div>
			</section>

			<section v-if="canRespond" class="app-panel app-panel--soft app-detail-card">
				<h2 class="app-section-title">{{ t('agal.respond.button') }}</h2>
				<div class="app-detail-stack">
					<UTextarea
						v-model="responseMessage"
						fixed
						:placeholder="t('agal.respond.messagePlaceholder')"
						:rows="2"
						autoresize
						class="w-full"
					/>
					<UButton
						block
						color="primary"
						:loading="responding"
						:label="t('agal.respond.button')"
						icon="i-lucide-send"
						@click="handleRespond"
					/>
				</div>
			</section>

			<section v-else-if="myResponse" class="app-panel app-panel--soft app-detail-card">
				<div class="flex items-center justify-between gap-3">
					<div>
						<h2 class="app-section-title mb-1">{{ t('agal.myResponse.title') }}</h2>
						<p class="app-detail-muted">{{ t('agal.myResponse.desc') }}</p>
					</div>
					<UBadge :color="responseStatusColor(myResponse.status)" variant="subtle" size="xs">
						{{ t(`agal.respond.status.${myResponse.status}`) }}
					</UBadge>
				</div>
				<div v-if="myResponse.message" class="app-detail-copy">{{ myResponse.message }}</div>
				<div v-if="myResponse.status === 'accepted' && routeItem.carrier.username">
					<a
						:href="`https://t.me/${routeItem.carrier.username.replace('@', '')}`"
						target="_blank"
						class="app-inline-link"
					>
						<UIcon name="i-lucide-send" class="size-4" />
						{{ routeItem.carrier.username }}
					</a>
				</div>
			</section>

			<section v-if="isOwner && routeItem.status === 'matched'" class="app-panel app-panel--soft app-detail-card">
				<div>
					<h2 class="app-section-title mb-1">{{ t('agal.match.title') }}</h2>
					<p class="app-detail-muted">{{ t('agal.match.desc') }}</p>
				</div>
				<div class="mt-3 grid grid-cols-2 gap-2">
					<UButton color="success" variant="soft" @click="handleRouteOutcome('completed')">
						{{ t('agal.match.complete') }}
					</UButton>
					<UButton color="error" variant="soft" @click="handleRouteOutcome('cancelled')">
						{{ t('agal.match.cancel') }}
					</UButton>
				</div>
			</section>

			<section v-if="responses.length > 0">
				<h2 class="app-section-title">{{ t('agal.responses') }}</h2>
				<div class="app-detail-stack">
					<div
						v-for="response in responses"
						:key="response.id"
						class="app-panel app-panel--soft app-detail-card app-detail-card--compact"
					>
						<div class="flex items-start justify-between gap-3">
							<div class="min-w-0 flex-1">
								<div class="flex items-center gap-2">
									<span class="app-detail-value !text-left">{{ response.user.name }}</span>
									<UBadge :color="responseStatusColor(response.status)" variant="subtle" size="xs">
										{{ t(`agal.respond.status.${response.status}`) }}
									</UBadge>
								</div>
								<div v-if="response.message" class="app-detail-muted mt-2">{{ response.message }}</div>
								<div v-if="response.status === 'accepted' && response.user.username" class="mt-3">
									<a
										:href="`https://t.me/${response.user.username.replace('@', '')}`"
										target="_blank"
										class="app-inline-link"
									>
										<UIcon name="i-lucide-send" class="size-3" />
										{{ response.user.username }}
									</a>
								</div>
							</div>
							<div
								v-if="isOwner && !isPastRoute && response.status === 'pending' && !hasAcceptedResponse"
								class="flex shrink-0 gap-1"
							>
								<UButton
									size="xs"
									color="success"
									variant="soft"
									icon="i-lucide-check"
									@click="handleAccept(response)"
								/>
								<UButton
									size="xs"
									color="error"
									variant="soft"
									icon="i-lucide-x"
									@click="handleReject(response)"
								/>
							</div>
						</div>
					</div>
				</div>
			</section>
		</template>

		<EmptyState v-else :title="t('common.error')" />
	</div>
</template>
