<script setup lang="ts">
import type { AyanResponse } from '../../types/ayan'

import { getAyanCreateMode } from '../../utils/role'
import { getResponseTargetPath } from '../../utils/responses'
import { getAyanAccessState } from '~/utils/auth'

definePageMeta({ lazy: true })

const { t } = useI18n()
const { hapticFeedback, isInTelegram } = useTg()
const { user: authUser, isAuthenticated, isLoading: authLoading, authError } = useAuth()

const activeTab = ref('trips')
const createOpen = ref(false)
const filtersOpen = ref(false)

const filterFrom = ref('')
const filterTo = ref('')
const filterDate = ref('')

const tabs = computed(() => [
	{ label: t('ayan.rides'), value: 'trips', icon: 'i-lucide-car' },
	{ label: t('ayan.requests'), value: 'requests', icon: 'i-lucide-map-pin' },
	{ label: t('ayan.myRides'), value: 'my', icon: 'i-lucide-user' }
])

const createMode = computed<'trip' | 'request' | null>(() => {
	return getAyanCreateMode(authUser.value?.role)
})

const accessState = computed(() =>
	getAyanAccessState({
		isAuthenticated: isAuthenticated.value,
		isLoading: authLoading.value,
		isInTelegram: isInTelegram.value,
		hasAuthError: !!authError.value
	})
)

const canUseAyan = computed(() => accessState.value === 'ready')

const createLabel = computed(() => (createMode.value === 'request' ? t('ayan.createRequest') : t('ayan.createRide')))

const formatTripPrice = (price: number) => formatPrice(price, '₽', t('ayan.ride.free'))

const statusColor = (status: AyanResponse['status']) => {
	if (status === 'accepted') return 'success'
	if (status === 'rejected') return 'error'
	return 'neutral'
}

const isPastTrip = (date: string, time: string) => isPastAyanDateTime(date, time)
const isPastRequest = (date: string, time?: string | null) => isPastAyanDateTime(date, time)

const {
	data: trips,
	pending: tripsLoading,
	refresh: refreshTrips
} = useLazyAsyncData('ayan-trips', () => useAyanTrips().fetchTrips(), {
	deep: false,
	default: () => [],
	watch: [canUseAyan],
	immediate: canUseAyan.value
})

const {
	data: requests,
	pending: requestsLoading,
	refresh: refreshRequests
} = useLazyAsyncData('ayan-requests', () => useAyanRequests().fetchRequests(), {
	deep: false,
	default: () => [],
	watch: [canUseAyan],
	immediate: canUseAyan.value
})

const {
	data: myTrips,
	pending: myTripsLoading,
	refresh: refreshMyTrips
} = useLazyAsyncData('ayan-my-trips', () => useAyanMy().fetchMyTrips(), {
	deep: false,
	default: () => [],
	watch: [canUseAyan],
	immediate: canUseAyan.value
})

const {
	data: myRequests,
	pending: myRequestsLoading,
	refresh: refreshMyRequests
} = useLazyAsyncData('ayan-my-requests', () => useAyanMy().fetchMyRequests(), {
	deep: false,
	default: () => [],
	watch: [canUseAyan],
	immediate: canUseAyan.value
})

const {
	data: myResponses,
	pending: myResponsesLoading,
	refresh: refreshMyResponses
} = useLazyAsyncData('ayan-my-responses', () => useAyanMy().fetchMyResponses(), {
	deep: false,
	default: () => [],
	watch: [canUseAyan],
	immediate: canUseAyan.value
})

const today = new Date().toISOString().split('T')[0] ?? ''
const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0] ?? ''

const dateChips = computed(() => [
	{ label: t('ayan.filter.all'), value: '' },
	{ label: t('ayan.filter.today'), value: today },
	{ label: t('ayan.filter.tomorrow'), value: tomorrow }
])

const aboutExamples = computed(() => [
	{
		title: t('serviceAbout.ayan.examples.driver.title'),
		description: t('serviceAbout.ayan.examples.driver.description')
	},
	{
		title: t('serviceAbout.ayan.examples.passenger.title'),
		description: t('serviceAbout.ayan.examples.passenger.description')
	}
])

const hasFilters = computed(() => filterFrom.value || filterTo.value || filterDate.value)

const activeFilterCount = computed(() => {
	let n = 0
	if (filterFrom.value) n++
	if (filterTo.value) n++
	if (filterDate.value) n++
	return n
})

function clearFilters() {
	filterFrom.value = ''
	filterTo.value = ''
	filterDate.value = ''
}

function matchItem(item: { from_address: string; to_address: string; date: string }): boolean {
	if (filterFrom.value && !item.from_address.toLowerCase().includes(filterFrom.value.toLowerCase())) return false
	if (filterTo.value && !item.to_address.toLowerCase().includes(filterTo.value.toLowerCase())) return false
	if (filterDate.value && item.date !== filterDate.value) return false
	return true
}

const filteredTrips = computed(() => {
	if (!trips.value) return []
	if (!hasFilters.value) return trips.value
	return trips.value.filter(matchItem)
})

const filteredRequests = computed(() => {
	if (!requests.value) return []
	if (!hasFilters.value) return requests.value
	return requests.value.filter(matchItem)
})

const filteredMyTrips = computed(() => {
	if (!myTrips.value) return []
	if (!hasFilters.value) return myTrips.value
	return myTrips.value.filter(matchItem)
})

const filteredMyRequests = computed(() => {
	if (!myRequests.value) return []
	if (!hasFilters.value) return myRequests.value
	return myRequests.value.filter(matchItem)
})

const filteredMyResponses = computed(() => {
	if (!myResponses.value) return []
	return myResponses.value
})

const loading = computed(() => {
	if (activeTab.value === 'trips') return tripsLoading.value
	if (activeTab.value === 'requests') return requestsLoading.value
	return myTripsLoading.value || myRequestsLoading.value || myResponsesLoading.value
})

function handleTabChange(val: string | number) {
	activeTab.value = val as string
	hapticFeedback('impact')
}

function handleCreate() {
	if (!createMode.value) return
	hapticFeedback('impact')
	createOpen.value = true
}

function handleCreated() {
	refreshTrips()
	refreshRequests()
	refreshMyTrips()
	refreshMyRequests()
	refreshMyResponses()
}

function handleRoleChanged(role: 'driver' | 'passenger') {
	activeTab.value = role === 'driver' ? 'trips' : 'requests'
	createOpen.value = false
	refreshTrips()
	refreshRequests()
	refreshMyTrips()
	refreshMyRequests()
	refreshMyResponses()
}

watch(
	canUseAyan,
	(ready) => {
		if (!ready) {
			createOpen.value = false
			filtersOpen.value = false
		}
	},
	{ immediate: true }
)

function toggleFilters() {
	filtersOpen.value = !filtersOpen.value
	hapticFeedback('impact')
}

function handleDateChip(val: string) {
	filterDate.value = filterDate.value === val ? '' : val
	hapticFeedback('impact')
}

function handleTripClick(tripId: number) {
	hapticFeedback('impact')
	navigateTo(`/ayan/trip/${tripId}`)
}

function handleRequestClick(requestId: number) {
	hapticFeedback('impact')
	navigateTo(`/ayan/request/${requestId}`)
}

function handleResponseClick(response: AyanResponse) {
	const targetPath = getResponseTargetPath(response)
	if (!targetPath) return
	hapticFeedback('impact')
	navigateTo(targetPath)
}
</script>

<template>
	<div class="app-page">
		<AyanAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<template v-else>
			<AppHero
				:eyebrow="t('ayan.category')"
				:title="t('ayan.name')"
				:description="t('ayan.desc')"
				icon="i-lucide-car-front"
			>
				<AppServiceAbout
					:label="t('serviceAbout.label')"
					:description="t('serviceAbout.ayan.description')"
					:examples-title="t('serviceAbout.examplesTitle')"
					:examples="aboutExamples"
				/>
				<AyanRoleSwitch @changed="handleRoleChanged" />
			</AppHero>

			<div class="app-panel app-panel--soft ayan-tabs-panel">
				<UTabs
					:items="tabs"
					:model-value="activeTab"
					variant="pill"
					size="sm"
					class="w-full"
					@update:model-value="handleTabChange"
				/>
			</div>

			<div v-if="activeTab !== 'my'" class="app-panel app-panel--soft ayan-filter-panel">
				<UButton
					icon="i-lucide-filter"
					size="sm"
					variant="ghost"
					color="neutral"
					:trailing-icon="filtersOpen ? 'i-lucide-chevron-up' : 'i-lucide-chevron-down'"
					@click="toggleFilters"
				>
					{{ t('ayan.filter.title') }}
					<UBadge v-if="hasFilters" color="primary" variant="subtle" size="xs" class="ml-1">
						{{ activeFilterCount }}
					</UBadge>
				</UButton>

				<Transition name="filter-slide">
					<div v-if="filtersOpen" class="ayan-filter-panel__body">
						<div class="grid grid-cols-2 gap-2">
							<UInput
								v-model="filterFrom"
								:placeholder="t('ayan.filter.from')"
								icon="i-lucide-circle-dot"
								variant="outline"
								size="sm"
								class="w-full"
							/>
							<UInput
								v-model="filterTo"
								:placeholder="t('ayan.filter.to')"
								icon="i-lucide-map-pin"
								variant="outline"
								size="sm"
								class="w-full"
							/>
						</div>

						<div class="flex flex-wrap gap-2">
							<UButton
								v-for="chip in dateChips"
								:key="chip.value"
								size="xs"
								:variant="filterDate === chip.value ? 'soft' : 'ghost'"
								:color="filterDate === chip.value ? 'primary' : 'neutral'"
								@click="handleDateChip(chip.value)"
							>
								{{ chip.label }}
							</UButton>
						</div>

						<UButton
							v-if="hasFilters"
							size="xs"
							variant="ghost"
							color="neutral"
							icon="i-lucide-x"
							@click="clearFilters"
						>
							{{ t('ayan.filter.clear') }}
						</UButton>
					</div>
				</Transition>
			</div>

			<div v-if="createMode" class="ayan-cta">
				<UButton icon="i-lucide-plus" size="lg" variant="soft" color="primary" block @click="handleCreate">
					{{ createLabel }}
				</UButton>
			</div>

			<div v-if="loading" class="flex justify-center py-12">
				<LoadingSpinner />
			</div>

			<template v-else-if="activeTab === 'trips'">
				<EmptyState
					v-if="!filteredTrips.length"
					:title="hasFilters ? t('empty.noResults') : t('ayan.noRides')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('ayan.noRidesDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="trip in filteredTrips"
						:key="trip.id"
						type="button"
						class="app-feed-card"
						@click="handleTripClick(trip.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ trip.from_address }}</span>
									<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
									<span class="app-feed-card__route-text">{{ trip.to_address }}</span>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">{{ trip.date }}</span>
									<span v-if="trip.time" class="app-feed-card__meta-item">{{ trip.time }}</span>
									<span class="app-feed-card__meta-item">
										{{ trip.seats }} {{ t('ayan.ride.seats') }}
									</span>
								</div>
								<div class="app-feed-card__subtext">{{ trip.driver.name }}</div>
							</div>
							<div class="app-feed-card__price">{{ formatTripPrice(trip.price) }}</div>
						</div>
					</button>
				</div>
			</template>

			<template v-else-if="activeTab === 'requests'">
				<EmptyState
					v-if="!filteredRequests.length"
					:title="hasFilters ? t('empty.noResults') : t('ayan.noRequests')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('ayan.noRequestsDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="request in filteredRequests"
						:key="request.id"
						type="button"
						class="app-feed-card"
						@click="handleRequestClick(request.id)"
					>
						<div class="app-feed-card__main">
							<div class="app-feed-card__route">
								<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
								<span class="app-feed-card__route-text">{{ request.from_address }}</span>
								<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
								<span class="app-feed-card__route-text">{{ request.to_address }}</span>
							</div>
							<div class="app-feed-card__meta">
								<span class="app-feed-card__meta-item">{{ request.date }}</span>
								<span v-if="request.time" class="app-feed-card__meta-item">{{ request.time }}</span>
							</div>
							<div class="app-feed-card__subtext">{{ request.passenger.name }}</div>
							<div
								v-if="request.description"
								class="app-feed-card__subtext app-feed-card__subtext--bright"
							>
								{{ request.description }}
							</div>
						</div>
					</button>
				</div>
			</template>

			<template v-else>
				<EmptyState
					v-if="!filteredMyTrips.length && !filteredMyRequests.length && !filteredMyResponses.length"
					:title="t('ayan.noMy')"
					:description="t('ayan.noMyDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="trip in filteredMyTrips"
						:key="`my-${trip.id}`"
						type="button"
						class="app-feed-card"
						@click="handleTripClick(trip.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ trip.from_address }}</span>
									<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
									<span class="app-feed-card__route-text">{{ trip.to_address }}</span>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">{{ trip.date }}</span>
									<span v-if="trip.time" class="app-feed-card__meta-item">{{ trip.time }}</span>
									<span class="app-feed-card__meta-item">
										{{ trip.seats }} {{ t('ayan.ride.seats') }}
									</span>
									<UBadge
										v-if="isPastTrip(trip.date, trip.time)"
										color="neutral"
										variant="subtle"
										size="xs"
									>
										{{ t('ayan.status.past') }}
									</UBadge>
								</div>
							</div>
							<div class="app-feed-card__price">{{ formatTripPrice(trip.price) }}</div>
						</div>
					</button>

					<button
						v-for="req in filteredMyRequests"
						:key="`my-req-${req.id}`"
						type="button"
						class="app-feed-card"
						@click="handleRequestClick(req.id)"
					>
						<div class="app-feed-card__main">
							<div class="app-feed-card__route">
								<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
								<span class="app-feed-card__route-text">{{ req.from_address }}</span>
								<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
								<span class="app-feed-card__route-text">{{ req.to_address }}</span>
							</div>
							<div class="app-feed-card__meta">
								<span class="app-feed-card__meta-item">{{ req.date }}</span>
								<span v-if="req.time" class="app-feed-card__meta-item">{{ req.time }}</span>
								<UBadge
									v-if="isPastRequest(req.date, req.time)"
									color="neutral"
									variant="subtle"
									size="xs"
								>
									{{ t('ayan.status.past') }}
								</UBadge>
							</div>
						</div>
					</button>

					<button
						v-for="response in filteredMyResponses"
						:key="`my-response-${response.id}`"
						type="button"
						class="app-feed-card"
						@click="handleResponseClick(response)"
					>
						<div class="app-feed-card__main">
							<div class="app-feed-card__route">
								<UIcon
									:name="response.trip_id ? 'i-lucide-car' : 'i-lucide-map-pin'"
									class="shrink-0 text-cyan-400"
								/>
								<span class="app-feed-card__route-text">
									{{
										response.trip
											? `${response.trip.from_address} → ${response.trip.to_address}`
											: response.request
												? `${response.request.from_address} → ${response.request.to_address}`
												: response.trip_id
													? t('ayan.myResponse.trip')
													: t('ayan.myResponse.request')
									}}
								</span>
							</div>
							<div class="mt-3 flex flex-wrap gap-2">
								<UBadge :color="statusColor(response.status)" variant="subtle" size="xs">
									{{ t(`ayan.respond.status.${response.status}`) }}
								</UBadge>
								<UBadge
									v-if="response.trip?.status || response.request?.status"
									color="primary"
									variant="outline"
									size="xs"
								>
									{{ t(`ayan.status.${response.trip?.status || response.request?.status}`) }}
								</UBadge>
							</div>
							<div class="app-feed-card__meta">
								<span class="app-feed-card__meta-item">
									#{{ response.trip_id ?? response.request_id }}
								</span>
								<span
									v-if="response.trip?.date || response.request?.date"
									class="app-feed-card__meta-item"
								>
									{{ response.trip?.date || response.request?.date }}
								</span>
								<span
									v-if="response.trip?.time || response.request?.time"
									class="app-feed-card__meta-item"
								>
									{{ response.trip?.time || response.request?.time }}
								</span>
							</div>
							<div class="app-feed-card__subtext">
								{{ response.trip?.driver.name || response.request?.passenger.name }}
							</div>
							<div v-if="response.message" class="app-feed-card__subtext app-feed-card__subtext--bright">
								{{ response.message }}
							</div>
						</div>
					</button>
				</div>
			</template>

			<AyanCreateSlideover v-model:open="createOpen" @created="handleCreated" />
		</template>
	</div>
</template>

<style scoped>
.ayan-tabs-panel,
.ayan-filter-panel {
	padding: 14px;
	margin-bottom: 12px;
}

.ayan-filter-panel__body {
	margin-top: 12px;
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.ayan-cta {
	margin-bottom: 16px;
}

.app-feed-card__subtext--bright {
	color: var(--text-secondary);
}

.filter-slide-enter-active,
.filter-slide-leave-active {
	transition: all 150ms ease-out;
	overflow: hidden;
}

.filter-slide-enter-from,
.filter-slide-leave-to {
	opacity: 0;
	max-height: 0;
	margin-top: 0;
}

.filter-slide-enter-to,
.filter-slide-leave-from {
	opacity: 1;
	max-height: 240px;
}
</style>
