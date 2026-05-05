<script setup lang="ts">
import type { TalBooking, TalCategory, TalMasterStatus } from '../../../types/tal'

import { getApiErrorMessage } from '~/utils/api-error'
import { getServiceAccessState } from '~/utils/auth'

definePageMeta({ lazy: true })

const route = useRoute()
const { t } = useI18n()
const toast = useToast()
const { hapticFeedback, isInTelegram } = useTg()
const { user: authUser, isAuthenticated, isLoading: authLoading, authError } = useAuth()
const { fetchMaster, updateMaster } = useTalMasters()
const { fetchMasterBookings, createMasterBooking, updateBookingStatus, cancelBooking } = useTalBookings()
const { fetchMyBookings } = useTalMy()

const masterId = computed(() => Number(route.params.id))

const accessState = computed(() =>
	getServiceAccessState({
		isAuthenticated: isAuthenticated.value,
		isLoading: authLoading.value,
		isInTelegram: isInTelegram.value,
		hasAuthError: !!authError.value
	})
)

const canUseTal = computed(() => accessState.value === 'ready')

const {
	data: talMaster,
	pending: loading,
	refresh: refreshMaster
} = useLazyAsyncData(`tal-master-${masterId.value}`, () => fetchMaster(masterId.value), {
	default: () => null,
	watch: [canUseTal],
	immediate: canUseTal.value
})

const bookings = ref<TalBooking[]>([])
const myBookings = ref<TalBooking[]>([])
const bookingMessage = ref('')
const bookingDesiredTime = ref('')
const responding = ref(false)
const cancellingBooking = ref(false)

const isOwner = computed(() => !!talMaster.value && !!authUser.value && talMaster.value.master.id === authUser.value.id)
const myBooking = computed(() => myBookings.value.find((item) => item.tal_master_id === masterId.value) ?? null)
const canBook = computed(
	() =>
		!!talMaster.value &&
		!isOwner.value &&
		!myBooking.value &&
		talMaster.value.status === 'open' &&
		talMaster.value.availability_status !== 'busy'
)

function masterStatusColor(status: TalMasterStatus) {
	if (status === 'open') return 'success'
	if (status === 'matched') return 'primary'
	if (status === 'cancelled') return 'error'
	return 'neutral'
}

function bookingStatusColor(status: TalBooking['status']) {
	if (status === 'accepted') return 'success'
	if (status === 'rejected') return 'error'
	return 'neutral'
}

function availabilityColor(status: 'now' | 'later' | 'tomorrow' | 'busy') {
	if (status === 'now') return 'success'
	if (status === 'later') return 'primary'
	if (status === 'tomorrow') return 'neutral'
	return 'error'
}

function categoryLabel(category: TalCategory) {
	return t(`tal.category.${category}`)
}

function categoryIcon(category: TalCategory) {
	if (category === 'beauty') return 'i-lucide-scissors'
	if (category === 'home') return 'i-lucide-house'
	return 'i-lucide-hammer'
}

function formatPriceFrom() {
	if (!talMaster.value) return '—'
	if (talMaster.value.price_from === null) return t('tal.priceNegotiable')
	return t('tal.priceFromValue', { price: formatPrice(talMaster.value.price_from, '₽') })
}

function blurActiveField() {
	if (typeof document === 'undefined') return
	const activeElement = document.activeElement
	if (activeElement instanceof HTMLElement) activeElement.blur()
}

async function loadBookings() {
	try {
		bookings.value = await fetchMasterBookings(masterId.value)
	} catch (error) {
		bookings.value = []
		console.error('[tal.master] Failed to load bookings:', error)
	}
}

async function loadMyBookings() {
	try {
		myBookings.value = await fetchMyBookings()
	} catch (error) {
		myBookings.value = []
		console.error('[tal.master] Failed to load my bookings:', error)
	}
}

async function handleBook() {
	blurActiveField()
	responding.value = true
	try {
		await createMasterBooking(masterId.value, {
			message: bookingMessage.value.trim() || undefined,
			desired_time: bookingDesiredTime.value.trim() || undefined
		})
		await loadMyBookings()
		bookingMessage.value = ''
		bookingDesiredTime.value = ''
		hapticFeedback('notification')
		toast.add({
			title: t('tal.respond.successTitle'),
			description: t('tal.respond.successDesc'),
			color: 'success',
			icon: 'i-lucide-check-circle',
			duration: 3000
		})
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

async function handleCancelOwnBooking() {
	if (!myBooking.value) return

	cancellingBooking.value = true
	try {
		await cancelBooking(myBooking.value.id)
		await loadMyBookings()
		hapticFeedback('notification')
		toast.add({
			title: t('tal.respond.cancelledTitle'),
			description: t('tal.respond.cancelledDesc'),
			color: 'success',
			icon: 'i-lucide-check-circle',
			duration: 3000
		})
	} catch (error) {
		hapticFeedback('impact')
		toast.add({
			title: getApiErrorMessage(error, t('common.error')),
			color: 'error',
			icon: 'i-lucide-x-circle',
			duration: 4000
		})
	} finally {
		cancellingBooking.value = false
	}
}

async function handleAccept(booking: TalBooking) {
	try {
		await updateBookingStatus(booking.id, 'accepted')
		hapticFeedback('notification')
		await refreshMaster()
		await loadBookings()
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

async function handleReject(booking: TalBooking) {
	try {
		await updateBookingStatus(booking.id, 'rejected')
		hapticFeedback('notification')
		await loadBookings()
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

async function handleMasterOutcome(status: 'completed' | 'cancelled') {
	if (!talMaster.value) return

	try {
		await updateMaster(talMaster.value.id, { status })
		hapticFeedback('notification')
		await refreshMaster()
		await loadBookings()
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
	canUseTal,
	(ready) => {
		if (ready) refreshMaster()
	},
	{ immediate: true }
)

watch(
	isOwner,
	async (owner) => {
		if (!canUseTal.value) {
			bookings.value = []
			myBookings.value = []
			return
		}

		if (owner) {
			await loadBookings()
			myBookings.value = []
			return
		}

		bookings.value = []
		await loadMyBookings()
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

		<template v-else-if="talMaster">
			<section class="app-panel app-detail-hero">
				<div class="flex items-start justify-between gap-4">
					<div class="min-w-0 flex-1">
						<p class="app-kicker">{{ t('servicePages.tal.badge') }}</p>
						<h1 class="app-detail-title mt-2">{{ talMaster.service_label }}</h1>
					</div>
					<div class="tal-master-icon">
						<UIcon :name="categoryIcon(talMaster.category)" class="size-6" />
					</div>
				</div>
				<div class="app-detail-meta">
					<UBadge :color="masterStatusColor(talMaster.status)" variant="subtle" size="xs">
						{{ t(`tal.status.${talMaster.status}`) }}
					</UBadge>
					<UBadge :color="availabilityColor(talMaster.availability_status)" variant="outline" size="xs">
						{{ t(`tal.availability.${talMaster.availability_status}`) }}
					</UBadge>
					<span class="app-chip">{{ talMaster.location }}</span>
					<span class="app-chip">{{ categoryLabel(talMaster.category) }}</span>
					<span class="app-chip">{{ formatPriceFrom() }}</span>
				</div>
			</section>

			<section class="app-panel app-panel--soft app-detail-card">
				<div class="app-detail-stack">
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('tal.master.master') }}</span>
						<span class="app-detail-value">{{ talMaster.master.name }}</span>
					</div>
					<div v-if="talMaster.available_note" class="app-detail-row">
						<span class="app-detail-label">{{ t('tal.master.availableNote') }}</span>
						<span class="app-detail-value">{{ talMaster.available_note }}</span>
					</div>
					<div class="app-detail-divider">
						<span class="app-detail-label">{{ t('tal.create.description') }}</span>
						<p class="app-detail-copy">{{ talMaster.description }}</p>
					</div>
				</div>
			</section>

			<section v-if="canBook" class="app-panel app-panel--soft app-detail-card tma-no-zoom">
				<h2 class="app-section-title">{{ t('tal.respond.title') }}</h2>
				<div class="app-detail-stack">
					<UInput
						v-model="bookingDesiredTime"
						fixed
						icon="i-lucide-clock-3"
						variant="outline"
						size="lg"
						:placeholder="t('tal.respond.desiredTimePlaceholder')"
						class="w-full"
					/>
					<UTextarea
						v-model="bookingMessage"
						fixed
						:rows="3"
						autoresize
						:placeholder="t('tal.respond.messagePlaceholder')"
						class="w-full"
					/>
					<UButton
						block
						class="tma-no-zoom-button"
						color="primary"
						:loading="responding"
						icon="i-lucide-send"
						:label="t('tal.respond.button')"
						@click="handleBook"
					/>
				</div>
			</section>

			<section v-else-if="myBooking" class="app-panel app-panel--soft app-detail-card">
				<div class="flex items-center justify-between gap-3">
					<div>
						<h2 class="app-section-title mb-1">{{ t('tal.myBooking.title') }}</h2>
						<p class="app-detail-muted">{{ t('tal.myBooking.desc') }}</p>
					</div>
					<UBadge :color="bookingStatusColor(myBooking.status)" variant="subtle" size="xs">
						{{ t(`tal.respond.status.${myBooking.status}`) }}
					</UBadge>
				</div>
				<div v-if="myBooking.desired_time" class="app-detail-copy">{{ myBooking.desired_time }}</div>
				<div v-if="myBooking.message" class="app-detail-copy">{{ myBooking.message }}</div>
				<div v-if="myBooking.status === 'accepted' && talMaster.master.username">
					<a
						:href="`https://t.me/${talMaster.master.username.replace('@', '')}`"
						target="_blank"
						class="app-inline-link"
					>
						<UIcon name="i-lucide-send" class="size-4" />
						{{ talMaster.master.username }}
					</a>
				</div>
				<UButton
					v-if="myBooking.status === 'pending'"
					class="mt-4"
					color="neutral"
					variant="outline"
					block
					:loading="cancellingBooking"
					@click="handleCancelOwnBooking"
				>
					{{ t('tal.respond.cancelOwn') }}
				</UButton>
			</section>

			<section v-if="isOwner" class="app-panel app-panel--soft app-detail-card">
				<div class="flex items-start justify-between gap-3">
					<div>
						<h2 class="app-section-title mb-1">{{ t('tal.responses.title') }}</h2>
						<p class="app-detail-muted">{{ t('tal.responses.desc') }}</p>
					</div>
					<span class="tal-booking-counter">{{ bookings.length }}</span>
				</div>

				<EmptyState
					v-if="!bookings.length"
					:title="t('tal.responses.emptyTitle')"
					:description="t('tal.responses.emptyDesc')"
				/>

				<div v-else class="app-detail-stack mt-4">
					<div
						v-for="booking in bookings"
						:key="booking.id"
						class="app-panel app-panel--soft app-detail-card app-detail-card--compact"
					>
						<div class="flex items-start justify-between gap-3">
							<div class="min-w-0 flex-1">
								<div class="flex flex-wrap items-center gap-2">
									<span class="app-detail-value !text-left">{{ booking.user.name }}</span>
									<UBadge :color="bookingStatusColor(booking.status)" variant="subtle" size="xs">
										{{ t(`tal.respond.status.${booking.status}`) }}
									</UBadge>
								</div>
								<div v-if="booking.desired_time" class="app-detail-muted mt-2">
									{{ booking.desired_time }}
								</div>
								<div class="app-detail-muted mt-2">
									{{ booking.message || t('tal.responses.noMessage') }}
								</div>
								<div v-if="booking.status === 'accepted' && booking.user.username" class="mt-3">
									<a
										:href="`https://t.me/${booking.user.username.replace('@', '')}`"
										target="_blank"
										class="app-inline-link"
									>
										<UIcon name="i-lucide-send" class="size-3" />
										{{ booking.user.username }}
									</a>
								</div>
							</div>
						</div>
						<div
							v-if="booking.status === 'pending' && talMaster.status === 'open'"
							class="mt-4 grid grid-cols-2 gap-2"
						>
							<UButton color="success" variant="soft" @click="handleAccept(booking)">
								{{ t('tal.responses.accept') }}
							</UButton>
							<UButton color="error" variant="soft" @click="handleReject(booking)">
								{{ t('tal.responses.reject') }}
							</UButton>
						</div>
					</div>
				</div>
			</section>

			<section v-if="isOwner && talMaster.status === 'matched'" class="app-panel app-panel--soft app-detail-card">
				<h2 class="app-section-title mb-3">{{ t('tal.match.title') }}</h2>
				<div class="grid grid-cols-2 gap-2">
					<UButton color="success" variant="soft" @click="handleMasterOutcome('completed')">
						{{ t('tal.match.complete') }}
					</UButton>
					<UButton color="error" variant="soft" @click="handleMasterOutcome('cancelled')">
						{{ t('tal.match.cancel') }}
					</UButton>
				</div>
			</section>

			<section
				v-else-if="isOwner && talMaster.status === 'open'"
				class="app-panel app-panel--soft app-detail-card"
			>
				<UButton block color="neutral" variant="outline" @click="handleMasterOutcome('cancelled')">
					{{ t('tal.match.cancel') }}
				</UButton>
			</section>
		</template>

		<EmptyState v-else :title="t('common.error')" />
	</div>
</template>

<style scoped>
.tal-master-icon {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 46px;
	height: 46px;
	border-radius: 16px;
	border: 1px solid rgb(94 218 198 / 0.16);
	background: rgb(94 218 198 / 0.1);
	color: var(--color-cyan-300);
	flex-shrink: 0;
}

.tal-booking-counter {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-width: 24px;
	min-height: 24px;
	padding: 0 10px;
	border-radius: 999px;
	border: 1px solid rgb(94 218 198 / 0.2);
	background: rgb(94 218 198 / 0.08);
	font-size: 12px;
	font-weight: 600;
	line-height: 1;
	font-variant-numeric: tabular-nums;
	color: var(--text-primary);
}
</style>
