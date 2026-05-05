<script setup lang="ts">
import type { TalAvailabilityStatus, TalBooking, TalCategory, TalMaster } from '../../types/tal'

import { getServiceAccessState } from '~/utils/auth'

definePageMeta({ lazy: true })

const { t } = useI18n()
const { hapticFeedback, isInTelegram } = useTg()
const { user: authUser, isAuthenticated, isLoading: authLoading, authError } = useAuth()

const activeTab = ref<'feed' | 'my-masters' | 'my-bookings'>('feed')
const createOpen = ref(false)
const filtersOpen = ref(false)

const filterCategory = ref('')
const filterAvailability = ref('')
const filterLocation = ref('')

const accessState = computed(() =>
	getServiceAccessState({
		isAuthenticated: isAuthenticated.value,
		isLoading: authLoading.value,
		isInTelegram: isInTelegram.value,
		hasAuthError: !!authError.value
	})
)

const canUseTal = computed(() => accessState.value === 'ready')
const isMasterRole = computed(() => authUser.value?.role === 'master')

const {
	data: masters,
	pending: mastersLoading,
	refresh: refreshMasters
} = useLazyAsyncData('tal-masters', () => useTalMasters().fetchMasters(), {
	deep: false,
	default: () => [],
	watch: [canUseTal],
	immediate: canUseTal.value
})

const {
	data: myMasters,
	pending: myMastersLoading,
	refresh: refreshMyMasters
} = useLazyAsyncData('tal-my-masters', () => useTalMy().fetchMyMasters(), {
	deep: false,
	default: () => [],
	watch: [canUseTal],
	immediate: canUseTal.value
})

const {
	data: myBookings,
	pending: myBookingsLoading,
	refresh: refreshMyBookings
} = useLazyAsyncData('tal-my-bookings', () => useTalMy().fetchMyBookings(), {
	deep: false,
	default: () => [],
	watch: [canUseTal],
	immediate: canUseTal.value
})

const tabs = computed(() => [
	{ label: t('tal.tabs.feed'), value: 'feed', icon: 'i-lucide-scissors' },
	{ label: t('tal.tabs.myMasters'), value: 'my-masters', icon: 'i-lucide-badge-check' },
	{ label: t('tal.tabs.myBookings'), value: 'my-bookings', icon: 'i-lucide-calendar-check-2' }
])

const categoryOptions = computed(() => [
	{ label: t('tal.filters.allCategories'), value: '' },
	{ label: t('tal.category.beauty'), value: 'beauty' },
	{ label: t('tal.category.home'), value: 'home' },
	{ label: t('tal.category.repair'), value: 'repair' }
])

const availabilityOptions = computed(() => [
	{ label: t('tal.filters.allAvailability'), value: '' },
	{ label: t('tal.availability.now'), value: 'now' },
	{ label: t('tal.availability.later'), value: 'later' },
	{ label: t('tal.availability.tomorrow'), value: 'tomorrow' },
	{ label: t('tal.availability.busy'), value: 'busy' }
])

const aboutExamples = computed(() => [
	{
		title: t('serviceAbout.tal.examples.now.title'),
		description: t('serviceAbout.tal.examples.now.description')
	},
	{
		title: t('serviceAbout.tal.examples.booking.title'),
		description: t('serviceAbout.tal.examples.booking.description')
	}
])

const { services } = usePublicRoadmap()

const roadmap = computed(() => services.value.find((item) => item.id === 'tal'))

const hasFilters = computed(() => !!filterCategory.value || !!filterAvailability.value || !!filterLocation.value)

const activeFilterCount = computed(() => {
	let n = 0
	if (filterCategory.value) n++
	if (filterAvailability.value) n++
	if (filterLocation.value) n++
	return n
})

const loading = computed(() => {
	if (activeTab.value === 'feed') return mastersLoading.value
	if (activeTab.value === 'my-masters') return myMastersLoading.value
	return myBookingsLoading.value
})

function matchMaster(item: TalMaster) {
	if (filterCategory.value && item.category !== filterCategory.value) return false
	if (filterAvailability.value && item.availability_status !== filterAvailability.value) return false
	if (filterLocation.value && !item.location.toLowerCase().includes(filterLocation.value.toLowerCase())) return false
	return true
}

const filteredMasters = computed(() => {
	if (!masters.value) return []
	if (!hasFilters.value) return masters.value
	return masters.value.filter(matchMaster)
})

const filteredMyMasters = computed(() => {
	if (!myMasters.value) return []
	if (!hasFilters.value) return myMasters.value
	return myMasters.value.filter(matchMaster)
})

const filteredMyBookings = computed(() => {
	if (!myBookings.value) return []
	if (!hasFilters.value) return myBookings.value
	return myBookings.value.filter((booking) => (booking.tal_master ? matchMaster(booking.tal_master) : false))
})

function categoryLabel(category: TalCategory) {
	return t(`tal.category.${category}`)
}

function categoryIcon(category: TalCategory) {
	if (category === 'beauty') return 'i-lucide-scissors'
	if (category === 'home') return 'i-lucide-house'
	return 'i-lucide-hammer'
}

function availabilityLabel(status: TalAvailabilityStatus) {
	return t(`tal.availability.${status}`)
}

function availabilityColor(status: TalAvailabilityStatus) {
	if (status === 'now') return 'success'
	if (status === 'later') return 'primary'
	if (status === 'tomorrow') return 'neutral'
	return 'error'
}

function masterStatusColor(status: TalMaster['status']) {
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

function formatPriceFrom(item: TalMaster | TalBooking['tal_master']) {
	if (!item) return '—'
	if (item.price_from === null) return t('tal.priceNegotiable')
	return t('tal.priceFromValue', { price: formatPrice(item.price_from, '₽') })
}

function clearFilters() {
	filterCategory.value = ''
	filterAvailability.value = ''
	filterLocation.value = ''
}

function handleTabChange(val: string | number) {
	activeTab.value = val as 'feed' | 'my-masters' | 'my-bookings'
	hapticFeedback('impact')
}

function toggleFilters() {
	filtersOpen.value = !filtersOpen.value
	hapticFeedback('impact')
}

function handleCreated() {
	refreshMasters()
	refreshMyMasters()
}

function handleRoleChanged() {
	refreshMasters()
	refreshMyMasters()
	refreshMyBookings()
}

function openCreate() {
	if (!isMasterRole.value) return
	hapticFeedback('impact')
	createOpen.value = true
}

function openMaster(id: number) {
	hapticFeedback('impact')
	navigateTo(`/tal/master/${id}`)
}

watch(
	canUseTal,
	(ready) => {
		if (!ready) {
			createOpen.value = false
			filtersOpen.value = false
		}
	},
	{ immediate: true }
)
</script>

<template>
	<div class="app-page">
		<AppAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<template v-else>
			<AppHero
				:eyebrow="t('servicePages.tal.badge')"
				:title="t('servicePages.tal.title')"
				:description="t('servicePages.tal.intro')"
				icon="i-lucide-calendar"
			>
				<AppServiceAbout
					:label="t('serviceAbout.label')"
					:description="t('serviceAbout.tal.description')"
					:examples-title="t('serviceAbout.examplesTitle')"
					:examples="aboutExamples"
				/>
				<AppRoadmapCard
					v-if="roadmap"
					:label="t('roadmap.previewLabel')"
					:title="t('roadmap.previewTitle')"
					:description="roadmap.summary"
					:live-label="t('roadmap.sections.live')"
					:building-label="t('roadmap.sections.building')"
					:planned-label="t('roadmap.sections.planned')"
					:live="roadmap.live"
					:building="roadmap.building"
					:planned="roadmap.planned"
					compact
					:limit-per-section="1"
					:action-label="t('roadmap.openFull')"
					action-route="/roadmap"
					icon="i-lucide-map"
				/>
			</AppHero>

			<TalRoleSwitch @changed="handleRoleChanged" />

			<div class="app-panel app-panel--soft tal-tabs-panel">
				<UTabs :items="tabs" :model-value="activeTab" variant="pill" @update:model-value="handleTabChange" />
			</div>

			<div class="app-panel app-panel--soft tal-filter-panel">
				<div class="flex items-center justify-between gap-3">
					<div>
						<p class="app-kicker">{{ t('tal.filters.title') }}</p>
						<p class="app-copy mt-1">
							{{
								activeFilterCount
									? t('tal.filters.active', { count: activeFilterCount })
									: t('tal.filters.desc')
							}}
						</p>
					</div>
					<div class="flex items-center gap-2">
						<UButton v-if="hasFilters" size="xs" color="neutral" variant="ghost" @click="clearFilters">
							{{ t('tal.filters.clear') }}
						</UButton>
						<UButton
							size="sm"
							color="neutral"
							variant="outline"
							icon="i-lucide-sliders-horizontal"
							@click="toggleFilters"
						>
							{{ t('tal.filters.toggle') }}
						</UButton>
					</div>
				</div>

				<Transition name="filter-slide">
					<div v-if="filtersOpen" class="tal-filter-panel__body">
						<USelect v-model="filterCategory" :items="categoryOptions" size="lg" class="w-full" />
						<USelect v-model="filterAvailability" :items="availabilityOptions" size="lg" class="w-full" />
						<UInput
							v-model="filterLocation"
							fixed
							icon="i-lucide-map-pin"
							variant="outline"
							size="lg"
							:placeholder="t('tal.filters.location')"
							class="w-full"
						/>
					</div>
				</Transition>
			</div>

			<div class="app-panel app-panel--soft tal-cta">
				<UButton
					block
					size="lg"
					color="primary"
					icon="i-lucide-plus"
					:disabled="!isMasterRole"
					@click="openCreate"
				>
					{{ t('tal.createStatus') }}
				</UButton>
				<p class="tal-cta__hint">
					{{ isMasterRole ? t('tal.create.desc') : t('tal.create.masterOnlyHint') }}
				</p>
			</div>

			<div v-if="loading" class="flex justify-center py-12">
				<LoadingSpinner />
			</div>

			<template v-else-if="activeTab === 'feed'">
				<EmptyState
					v-if="!filteredMasters.length"
					:title="hasFilters ? t('empty.noResults') : t('tal.feed.emptyTitle')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('tal.feed.emptyDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="item in filteredMasters"
						:key="item.id"
						type="button"
						class="app-feed-card"
						@click="openMaster(item.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon :name="categoryIcon(item.category)" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ item.service_label }}</span>
								</div>
								<div class="mt-3 flex flex-wrap gap-2">
									<UBadge
										:color="availabilityColor(item.availability_status)"
										variant="subtle"
										size="xs"
									>
										{{ availabilityLabel(item.availability_status) }}
									</UBadge>
									<UBadge color="primary" variant="outline" size="xs">
										{{ categoryLabel(item.category) }}
									</UBadge>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">
										<UIcon name="i-lucide-map-pin" class="size-3.5" />
										{{ item.location }}
									</span>
									<span class="app-feed-card__meta-item">
										<UIcon name="i-lucide-user" class="size-3.5" />
										{{ item.master.name }}
									</span>
									<span v-if="item.available_note" class="app-feed-card__meta-item">
										<UIcon name="i-lucide-clock-3" class="size-3.5" />
										{{ item.available_note }}
									</span>
								</div>
								<div class="app-feed-card__subtext app-feed-card__subtext--bright">
									{{ item.description }}
								</div>
							</div>
							<div class="app-feed-card__price">{{ formatPriceFrom(item) }}</div>
						</div>
					</button>
				</div>
			</template>

			<template v-else-if="activeTab === 'my-masters'">
				<EmptyState
					v-if="!filteredMyMasters.length"
					:title="hasFilters ? t('empty.noResults') : t('tal.my.noMastersTitle')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('tal.my.noMastersDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="item in filteredMyMasters"
						:key="`my-master-${item.id}`"
						type="button"
						class="app-feed-card"
						@click="openMaster(item.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon :name="categoryIcon(item.category)" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ item.service_label }}</span>
								</div>
								<div class="mt-3 flex flex-wrap gap-2">
									<UBadge :color="masterStatusColor(item.status)" variant="subtle" size="xs">
										{{ t(`tal.status.${item.status}`) }}
									</UBadge>
									<UBadge
										:color="availabilityColor(item.availability_status)"
										variant="outline"
										size="xs"
									>
										{{ availabilityLabel(item.availability_status) }}
									</UBadge>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">
										<UIcon name="i-lucide-map-pin" class="size-3.5" />
										{{ item.location }}
									</span>
									<span v-if="item.available_note" class="app-feed-card__meta-item">
										<UIcon name="i-lucide-clock-3" class="size-3.5" />
										{{ item.available_note }}
									</span>
								</div>
								<div class="app-feed-card__subtext app-feed-card__subtext--bright">
									{{ item.description }}
								</div>
							</div>
							<div class="app-feed-card__price">{{ formatPriceFrom(item) }}</div>
						</div>
					</button>
				</div>
			</template>

			<template v-else>
				<EmptyState
					v-if="!filteredMyBookings.length"
					:title="hasFilters ? t('empty.noResults') : t('tal.my.noBookingsTitle')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('tal.my.noBookingsDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="booking in filteredMyBookings"
						:key="`my-booking-${booking.id}`"
						type="button"
						class="app-feed-card"
						@click="booking.tal_master && openMaster(booking.tal_master.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon
										:name="
											booking.tal_master
												? categoryIcon(booking.tal_master.category)
												: 'i-lucide-calendar-check-2'
										"
										class="shrink-0 text-cyan-400"
									/>
									<span class="app-feed-card__route-text">
										{{ booking.tal_master?.service_label || t('tal.myBooking.title') }}
									</span>
								</div>
								<div class="mt-3 flex flex-wrap gap-2">
									<UBadge :color="bookingStatusColor(booking.status)" variant="subtle" size="xs">
										{{ t(`tal.respond.status.${booking.status}`) }}
									</UBadge>
									<UBadge
										v-if="booking.tal_master"
										:color="masterStatusColor(booking.tal_master.status)"
										variant="outline"
										size="xs"
									>
										{{ t(`tal.status.${booking.tal_master.status}`) }}
									</UBadge>
								</div>
								<div class="app-feed-card__meta">
									<span v-if="booking.tal_master" class="app-feed-card__meta-item">
										<UIcon name="i-lucide-map-pin" class="size-3.5" />
										{{ booking.tal_master.location }}
									</span>
									<span v-if="booking.desired_time" class="app-feed-card__meta-item">
										<UIcon name="i-lucide-clock-3" class="size-3.5" />
										{{ booking.desired_time }}
									</span>
								</div>
								<div class="app-feed-card__subtext app-feed-card__subtext--bright">
									{{
										booking.message ||
										booking.tal_master?.description ||
										t('tal.responses.noMessage')
									}}
								</div>
							</div>
							<div class="app-feed-card__price">{{ formatPriceFrom(booking.tal_master) }}</div>
						</div>
					</button>
				</div>
			</template>
		</template>

		<TalCreateSlideover v-model:open="createOpen" @created="handleCreated" />
	</div>
</template>

<style scoped>
.tal-tabs-panel,
.tal-filter-panel,
.tal-cta {
	padding: 14px;
	margin-bottom: 12px;
}

.tal-filter-panel__body {
	margin-top: 12px;
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.tal-cta__hint {
	margin: 10px 0 0;
	text-align: center;
	font-size: 12px;
	line-height: 1.5;
	color: var(--text-secondary);
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
	max-height: 220px;
	margin-top: 12px;
}
</style>
