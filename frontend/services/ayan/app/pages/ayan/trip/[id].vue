<script setup lang="ts">
import type { AyanResponse } from '../../../types/ayan'

import { getApiErrorMessage } from '~/utils/api-error'
import { getAyanAccessState } from '~/utils/auth'
import { findTargetResponse } from '../../../utils/responses'

definePageMeta({ lazy: true })

const route = useRoute()
const { t } = useI18n()
const toast = useToast()
const { hapticFeedback, isInTelegram } = useTg()
const { user: authUser, isAuthenticated, isLoading: authLoading, authError } = useAuth()
const { fetchTrip, updateTrip } = useAyanTrips()
const { fetchTripResponses, createTripResponse, updateResponseStatus } = useAyanResponses()
const { fetchMyResponses } = useAyanMy()

const tripId = computed(() => Number(route.params.id))

const accessState = computed(() =>
	getAyanAccessState({
		isAuthenticated: isAuthenticated.value,
		isLoading: authLoading.value,
		isInTelegram: isInTelegram.value,
		hasAuthError: !!authError.value
	})
)

const canUseAyan = computed(() => accessState.value === 'ready')

const {
	data: trip,
	pending: loading,
	refresh: refreshTrip
} = useLazyAsyncData(`ayan-trip-${tripId.value}`, () => fetchTrip(tripId.value), {
	default: () => null,
	watch: [canUseAyan],
	immediate: canUseAyan.value
})

const responses = ref<AyanResponse[]>([])
const myResponses = ref<AyanResponse[]>([])
const responding = ref(false)
const responseMessage = ref('')

const isPastTrip = computed(() => (trip.value ? isPastAyanDateTime(trip.value.date, trip.value.time) : false))

const isOwner = computed(() => {
	if (!trip.value || !authUser.value) return false
	return trip.value.driver.id === authUser.value.id
})

const myResponse = computed(() => findTargetResponse(myResponses.value, { tripId: tripId.value }))

const canRespond = computed(
	() =>
		!isOwner.value &&
		!myResponse.value &&
		!isPastTrip.value &&
		trip.value?.status === 'open' &&
		authUser.value?.role === 'passenger'
)

const hasAcceptedResponse = computed(() => responses.value.some((r) => r.status === 'accepted'))

const statusColor = (status: AyanResponse['status']) => {
	if (status === 'accepted') return 'success'
	if (status === 'rejected') return 'error'
	return 'neutral'
}

const targetStatusColor = (status: string) => {
	if (status === 'open') return 'success'
	if (status === 'matched') return 'primary'
	if (status === 'cancelled') return 'error'
	return 'neutral'
}

async function loadResponses() {
	try {
		responses.value = await fetchTripResponses(tripId.value)
	} catch (error) {
		responses.value = []
		console.error('[ayan.trip] Failed to load responses:', error)
	}
}

async function loadMyResponses() {
	try {
		myResponses.value = await fetchMyResponses()
	} catch (error) {
		myResponses.value = []
		console.error('[ayan.trip] Failed to load my responses:', error)
	}
}

async function handleRespond() {
	responding.value = true
	try {
		await createTripResponse(tripId.value, { message: responseMessage.value || undefined })
		await loadMyResponses()
		hapticFeedback('notification')
		toast.add({ title: t('ayan.respond.success'), color: 'success', icon: 'i-lucide-check-circle', duration: 3000 })
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

async function handleAccept(r: AyanResponse) {
	try {
		await updateResponseStatus(r.id, 'accepted')
		hapticFeedback('notification')
		await refreshTrip()
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

async function handleReject(r: AyanResponse) {
	try {
		await updateResponseStatus(r.id, 'rejected')
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

async function handleTripOutcome(status: 'completed' | 'cancelled') {
	if (!trip.value) return

	try {
		await updateTrip(trip.value.id, { status })
		hapticFeedback('notification')
		await refreshTrip()
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
	canUseAyan,
	(ready) => {
		if (ready) {
			refreshTrip()
		}
	},
	{ immediate: true }
)

watch(
	isOwner,
	async (owner) => {
		if (!canUseAyan.value) {
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

		<AyanAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<div v-else-if="loading" class="flex justify-center py-12">
			<LoadingSpinner />
		</div>

		<template v-else-if="trip">
			<section class="app-panel app-detail-hero">
				<h1 class="app-detail-title">{{ trip.from_address }} → {{ trip.to_address }}</h1>
				<div class="app-detail-meta">
					<UBadge :color="targetStatusColor(trip.status)" variant="subtle" size="xs">
						{{ t(`ayan.status.${trip.status}`) }}
					</UBadge>
					<span class="app-chip">{{ trip.date }}</span>
					<span v-if="trip.time" class="app-chip">{{ trip.time }}</span>
					<UBadge v-if="isPastTrip" color="neutral" variant="subtle" size="xs">
						{{ t('ayan.status.past') }}
					</UBadge>
				</div>
			</section>

			<section class="app-panel app-panel--soft app-detail-card">
				<div class="app-detail-stack">
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('ayan.ride.price') }}</span>
						<span class="app-detail-value">{{ formatPrice(trip.price, '₽', t('ayan.ride.free')) }}</span>
					</div>
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('ayan.ride.seatsAvailable') }}</span>
						<span class="app-detail-value">{{ trip.seats }}</span>
					</div>
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('ayan.driver') }}</span>
						<span class="app-detail-value">{{ trip.driver.name }}</span>
					</div>
					<div v-if="trip.comment" class="app-detail-divider">
						<span class="app-detail-label">{{ t('ayan.ride.comment') }}</span>
						<p class="app-detail-copy">{{ trip.comment }}</p>
					</div>
				</div>
			</section>

			<section v-if="canRespond" class="app-panel app-panel--soft app-detail-card">
				<h2 class="app-section-title">{{ t('ayan.respond.button') }}</h2>
				<div class="app-detail-stack">
					<UTextarea
						v-model="responseMessage"
						fixed
						:placeholder="t('ayan.respond.messagePlaceholder')"
						:rows="2"
						autoresize
						class="w-full"
					/>
					<UButton
						block
						color="primary"
						:loading="responding"
						:label="t('ayan.respond.button')"
						icon="i-lucide-send"
						@click="handleRespond"
					/>
				</div>
			</section>

			<section v-else-if="myResponse" class="app-panel app-panel--soft app-detail-card">
				<div class="flex items-center justify-between gap-3">
					<div>
						<h2 class="app-section-title mb-1">{{ t('ayan.myResponse.title') }}</h2>
						<p class="app-detail-muted">{{ t('ayan.myResponse.desc') }}</p>
					</div>
					<UBadge :color="statusColor(myResponse.status)" variant="subtle" size="xs">
						{{ t(`ayan.respond.status.${myResponse.status}`) }}
					</UBadge>
				</div>
				<div v-if="myResponse.message" class="app-detail-copy">{{ myResponse.message }}</div>
				<div v-if="myResponse.status === 'accepted' && trip.driver.username">
					<a :href="`https://t.me/${trip.driver.username.replace('@', '')}`" target="_blank" class="app-inline-link">
						<UIcon name="i-lucide-send" class="size-4" />
						{{ trip.driver.username }}
					</a>
				</div>
			</section>

			<section v-if="isOwner && trip.status === 'matched'" class="app-panel app-panel--soft app-detail-card">
				<div>
					<h2 class="app-section-title mb-1">{{ t('ayan.match.title') }}</h2>
					<p class="app-detail-muted">{{ t('ayan.match.desc') }}</p>
				</div>
				<div class="mt-3 grid grid-cols-2 gap-2">
					<UButton color="success" variant="soft" @click="handleTripOutcome('completed')">
						{{ t('ayan.match.complete') }}
					</UButton>
					<UButton color="error" variant="soft" @click="handleTripOutcome('cancelled')">
						{{ t('ayan.match.cancel') }}
					</UButton>
				</div>
			</section>

			<section v-if="responses.length > 0">
				<h2 class="app-section-title">{{ t('ayan.responses') }}</h2>
				<div class="app-detail-stack">
					<div v-for="r in responses" :key="r.id" class="app-panel app-panel--soft app-detail-card app-detail-card--compact">
						<div class="flex items-start justify-between gap-3">
							<div class="min-w-0 flex-1">
								<div class="flex items-center gap-2">
									<span class="app-detail-value !text-left">{{ r.user.name }}</span>
									<UBadge :color="statusColor(r.status)" variant="subtle" size="xs">
										{{ t(`ayan.respond.status.${r.status}`) }}
									</UBadge>
								</div>
								<div v-if="r.message" class="app-detail-muted mt-2">{{ r.message }}</div>
								<div v-if="r.status === 'accepted' && r.user.username" class="mt-3">
									<a :href="`https://t.me/${r.user.username.replace('@', '')}`" target="_blank" class="app-inline-link">
										<UIcon name="i-lucide-send" class="size-3" />
										{{ r.user.username }}
									</a>
								</div>
							</div>
							<div v-if="isOwner && !isPastTrip && r.status === 'pending' && !hasAcceptedResponse" class="flex shrink-0 gap-1">
								<UButton size="xs" color="success" variant="soft" icon="i-lucide-check" @click="handleAccept(r)" />
								<UButton size="xs" color="error" variant="soft" icon="i-lucide-x" @click="handleReject(r)" />
							</div>
						</div>
					</div>
				</div>
			</section>
		</template>

		<EmptyState v-else :title="t('common.error')" />
	</div>
</template>
