<script setup lang="ts">
import type { AgalResponse, AgalSizeLabel, AgalStatus } from '../../types/agal'

import { getAgalCreateMode } from '../../utils/role'
import { getResponseTargetPath } from '../../utils/responses'
import { getServiceAccessState } from '~/utils/auth'

definePageMeta({ lazy: true })

const { t } = useI18n()
const { hapticFeedback, isInTelegram } = useTg()
const { user: authUser, isAuthenticated, isLoading: authLoading, authError } = useAuth()

const activeTab = ref<'routes' | 'requests' | 'my'>('routes')
const createOpen = ref(false)
const filtersOpen = ref(false)

const filterFrom = ref('')
const filterTo = ref('')
const filterDate = ref('')

const tabs = computed(() => [
	{ label: t('agal.routes'), value: 'routes', icon: 'i-lucide-route' },
	{ label: t('agal.requests'), value: 'requests', icon: 'i-lucide-package-open' },
	{ label: t('agal.my'), value: 'my', icon: 'i-lucide-user' }
])

const createMode = computed<'route' | 'request' | null>(() => getAgalCreateMode(authUser.value?.role))

const accessState = computed(() =>
	getServiceAccessState({
		isAuthenticated: isAuthenticated.value,
		isLoading: authLoading.value,
		isInTelegram: isInTelegram.value,
		hasAuthError: !!authError.value
	})
)

const canUseAgal = computed(() => accessState.value === 'ready')

const createLabel = computed(() => (createMode.value === 'request' ? t('agal.createRequest') : t('agal.createRoute')))

const {
	data: routes,
	pending: routesLoading,
	refresh: refreshRoutes
} = useLazyAsyncData('agal-routes', () => useAgalRoutes().fetchRoutes(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const {
	data: requests,
	pending: requestsLoading,
	refresh: refreshRequests
} = useLazyAsyncData('agal-requests', () => useAgalRequests().fetchRequests(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const {
	data: myRoutes,
	pending: myRoutesLoading,
	refresh: refreshMyRoutes
} = useLazyAsyncData('agal-my-routes', () => useAgalMy().fetchMyRoutes(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const {
	data: myRequests,
	pending: myRequestsLoading,
	refresh: refreshMyRequests
} = useLazyAsyncData('agal-my-requests', () => useAgalMy().fetchMyRequests(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const {
	data: myResponses,
	pending: myResponsesLoading,
	refresh: refreshMyResponses
} = useLazyAsyncData('agal-my-responses', () => useAgalMy().fetchMyResponses(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const today = new Date().toISOString().split('T')[0] ?? ''
const tomorrow = new Date(Date.now() + 86400000).toISOString().split('T')[0] ?? ''

const dateChips = computed(() => [
	{ label: t('agal.filter.all'), value: '' },
	{ label: t('agal.filter.today'), value: today },
	{ label: t('agal.filter.tomorrow'), value: tomorrow }
])

const aboutExamples = computed(() => [
	{
		title: t('serviceAbout.agal.examples.carrier.title'),
		description: t('serviceAbout.agal.examples.carrier.description')
	},
	{
		title: t('serviceAbout.agal.examples.sender.title'),
		description: t('serviceAbout.agal.examples.sender.description')
	}
])

const { services } = usePublicRoadmap()

const roadmap = computed(() => services.value.find((item) => item.id === 'agal'))

const hasFilters = computed(() => filterFrom.value || filterTo.value || filterDate.value)

const activeFilterCount = computed(() => {
	let n = 0
	if (filterFrom.value) n++
	if (filterTo.value) n++
	if (filterDate.value) n++
	return n
})

const loading = computed(() => {
	if (activeTab.value === 'routes') return routesLoading.value
	if (activeTab.value === 'requests') return requestsLoading.value
	return myRoutesLoading.value || myRequestsLoading.value || myResponsesLoading.value
})

function formatMoney(
	value: number | null | undefined,
	fallbackKey: 'agal.route.priceNegotiable' | 'agal.request.budgetNegotiable'
) {
	if (value === null || value === undefined) return t(fallbackKey)
	return formatPrice(value, '₽')
}

function sizeLabel(size: AgalSizeLabel) {
	return t(`agal.size.${size}`)
}

function statusColor(status: AgalStatus) {
	if (status === 'open') return 'success'
	if (status === 'matched') return 'primary'
	if (status === 'cancelled') return 'error'
	return 'neutral'
}

function responseStatusColor(status: AgalResponse['status']) {
	if (status === 'accepted') return 'success'
	if (status === 'rejected') return 'error'
	return 'neutral'
}

function isPastTarget(date: string, time?: string | null) {
	return isPastAyanDateTime(date, time)
}

function clearFilters() {
	filterFrom.value = ''
	filterTo.value = ''
	filterDate.value = ''
}

function matchItem(item: { from_address: string; to_address: string; date: string }) {
	if (filterFrom.value && !item.from_address.toLowerCase().includes(filterFrom.value.toLowerCase())) return false
	if (filterTo.value && !item.to_address.toLowerCase().includes(filterTo.value.toLowerCase())) return false
	if (filterDate.value && item.date !== filterDate.value) return false
	return true
}

const filteredRoutes = computed(() => {
	if (!routes.value) return []
	if (!hasFilters.value) return routes.value
	return routes.value.filter(matchItem)
})

const filteredRequests = computed(() => {
	if (!requests.value) return []
	if (!hasFilters.value) return requests.value
	return requests.value.filter(matchItem)
})

const filteredMyRoutes = computed(() => {
	if (!myRoutes.value) return []
	if (!hasFilters.value) return myRoutes.value
	return myRoutes.value.filter(matchItem)
})

const filteredMyRequests = computed(() => {
	if (!myRequests.value) return []
	if (!hasFilters.value) return myRequests.value
	return myRequests.value.filter(matchItem)
})

function handleTabChange(val: string | number) {
	activeTab.value = val as 'routes' | 'requests' | 'my'
	hapticFeedback('impact')
}

function handleCreate() {
	if (!createMode.value) return
	hapticFeedback('impact')
	createOpen.value = true
}

function handleCreated() {
	refreshRoutes()
	refreshRequests()
	refreshMyRoutes()
	refreshMyRequests()
	refreshMyResponses()
}

function handleRoleChanged(role: 'carrier' | 'sender') {
	activeTab.value = role === 'carrier' ? 'routes' : 'requests'
	createOpen.value = false
	refreshRoutes()
	refreshRequests()
	refreshMyRoutes()
	refreshMyRequests()
	refreshMyResponses()
}

watch(
	canUseAgal,
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

function handleRouteClick(routeId: number) {
	hapticFeedback('impact')
	navigateTo(`/agal/route/${routeId}`)
}

function handleRequestClick(requestId: number) {
	hapticFeedback('impact')
	navigateTo(`/agal/request/${requestId}`)
}

function handleResponseClick(response: AgalResponse) {
	const targetPath = getResponseTargetPath(response)
	if (!targetPath) return
	hapticFeedback('impact')
	navigateTo(targetPath)
}
</script>

<template>
	<div class="app-page">
		<AppAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<template v-else>
			<AppHero
				:eyebrow="t('servicePages.agal.badge')"
				:title="t('servicePages.agal.title')"
				:description="t('servicePages.agal.intro')"
				icon="i-lucide-box"
			>
				<AppServiceAbout
					:label="t('serviceAbout.label')"
					:description="t('serviceAbout.agal.description')"
					:examples-title="t('serviceAbout.examplesTitle')"
					:examples="aboutExamples"
				/>
				<AgalRoleSwitch @changed="handleRoleChanged" />
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

			<div class="app-panel app-panel--soft agal-tabs-panel">
				<UTabs
					:items="tabs"
					:model-value="activeTab"
					variant="pill"
					size="sm"
					class="w-full"
					@update:model-value="handleTabChange"
				/>
			</div>

			<div v-if="activeTab !== 'my'" class="app-panel app-panel--soft agal-filter-panel">
				<UButton
					icon="i-lucide-filter"
					size="sm"
					variant="ghost"
					color="neutral"
					:trailing-icon="filtersOpen ? 'i-lucide-chevron-up' : 'i-lucide-chevron-down'"
					@click="toggleFilters"
				>
					{{ t('agal.filter.title') }}
					<UBadge v-if="hasFilters" color="primary" variant="subtle" size="xs" class="ml-1">
						{{ activeFilterCount }}
					</UBadge>
				</UButton>

				<Transition name="filter-slide">
					<div v-if="filtersOpen" class="agal-filter-panel__body">
						<div class="grid grid-cols-2 gap-2">
							<UInput
								v-model="filterFrom"
								:placeholder="t('agal.filter.from')"
								icon="i-lucide-circle-dot"
								variant="outline"
								size="sm"
								class="w-full"
							/>
							<UInput
								v-model="filterTo"
								:placeholder="t('agal.filter.to')"
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
							{{ t('agal.filter.clear') }}
						</UButton>
					</div>
				</Transition>
			</div>

			<div v-if="createMode" class="agal-cta">
				<UButton icon="i-lucide-plus" size="lg" variant="soft" color="primary" block @click="handleCreate">
					{{ createLabel }}
				</UButton>
			</div>

			<div v-if="loading" class="flex justify-center py-12">
				<LoadingSpinner />
			</div>

			<template v-else-if="activeTab === 'routes'">
				<EmptyState
					v-if="!filteredRoutes.length"
					:title="hasFilters ? t('empty.noResults') : t('agal.noRoutes')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('agal.noRoutesDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="routeItem in filteredRoutes"
						:key="routeItem.id"
						type="button"
						class="app-feed-card"
						@click="handleRouteClick(routeItem.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ routeItem.from_address }}</span>
									<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
									<span class="app-feed-card__route-text">{{ routeItem.to_address }}</span>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">{{ routeItem.date }}</span>
									<span v-if="routeItem.time" class="app-feed-card__meta-item">
										{{ routeItem.time }}
									</span>
									<span class="app-feed-card__meta-item">{{ sizeLabel(routeItem.size_label) }}</span>
									<span v-if="routeItem.weight_kg_max" class="app-feed-card__meta-item">
										{{ routeItem.weight_kg_max }} кг
									</span>
								</div>
								<div class="app-feed-card__subtext">{{ routeItem.carrier.name }}</div>
							</div>
							<div class="app-feed-card__price">
								{{ formatMoney(routeItem.price, 'agal.route.priceNegotiable') }}
							</div>
						</div>
					</button>
				</div>
			</template>

			<template v-else-if="activeTab === 'requests'">
				<EmptyState
					v-if="!filteredRequests.length"
					:title="hasFilters ? t('empty.noResults') : t('agal.noRequests')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('agal.noRequestsDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="requestItem in filteredRequests"
						:key="requestItem.id"
						type="button"
						class="app-feed-card"
						@click="handleRequestClick(requestItem.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ requestItem.from_address }}</span>
									<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
									<span class="app-feed-card__route-text">{{ requestItem.to_address }}</span>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">{{ requestItem.date }}</span>
									<span v-if="requestItem.time" class="app-feed-card__meta-item">
										{{ requestItem.time }}
									</span>
									<span class="app-feed-card__meta-item">
										{{ sizeLabel(requestItem.size_label) }}
									</span>
									<span v-if="requestItem.weight_kg" class="app-feed-card__meta-item">
										{{ requestItem.weight_kg }} кг
									</span>
								</div>
								<div class="app-feed-card__subtext">{{ requestItem.sender.name }}</div>
								<div class="app-feed-card__subtext app-feed-card__subtext--bright">
									{{ requestItem.contents_summary }}
								</div>
							</div>
							<div class="app-feed-card__price">
								{{ formatMoney(requestItem.budget, 'agal.request.budgetNegotiable') }}
							</div>
						</div>
					</button>
				</div>
			</template>

			<template v-else>
				<EmptyState
					v-if="!filteredMyRoutes.length && !filteredMyRequests.length && !myResponses.length"
					:title="t('agal.noMy')"
					:description="t('agal.noMyDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="routeItem in filteredMyRoutes"
						:key="`my-route-${routeItem.id}`"
						type="button"
						class="app-feed-card"
						@click="handleRouteClick(routeItem.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon name="i-lucide-route" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ routeItem.from_address }}</span>
									<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
									<span class="app-feed-card__route-text">{{ routeItem.to_address }}</span>
								</div>
								<div class="mt-3 flex flex-wrap gap-2">
									<UBadge :color="statusColor(routeItem.status)" variant="subtle" size="xs">
										{{ t(`agal.status.${routeItem.status}`) }}
									</UBadge>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">{{ routeItem.date }}</span>
									<span v-if="routeItem.time" class="app-feed-card__meta-item">
										{{ routeItem.time }}
									</span>
									<span class="app-feed-card__meta-item">{{ sizeLabel(routeItem.size_label) }}</span>
									<UBadge
										v-if="isPastTarget(routeItem.date, routeItem.time)"
										color="neutral"
										variant="subtle"
										size="xs"
									>
										{{ t('agal.status.past') }}
									</UBadge>
								</div>
							</div>
							<div class="app-feed-card__price">
								{{ formatMoney(routeItem.price, 'agal.route.priceNegotiable') }}
							</div>
						</div>
					</button>

					<button
						v-for="requestItem in filteredMyRequests"
						:key="`my-request-${requestItem.id}`"
						type="button"
						class="app-feed-card"
						@click="handleRequestClick(requestItem.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon name="i-lucide-package-open" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ requestItem.from_address }}</span>
									<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
									<span class="app-feed-card__route-text">{{ requestItem.to_address }}</span>
								</div>
								<div class="mt-3 flex flex-wrap gap-2">
									<UBadge :color="statusColor(requestItem.status)" variant="subtle" size="xs">
										{{ t(`agal.status.${requestItem.status}`) }}
									</UBadge>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">{{ requestItem.date }}</span>
									<span v-if="requestItem.time" class="app-feed-card__meta-item">
										{{ requestItem.time }}
									</span>
									<span class="app-feed-card__meta-item">
										{{ sizeLabel(requestItem.size_label) }}
									</span>
									<UBadge
										v-if="isPastTarget(requestItem.date, requestItem.time)"
										color="neutral"
										variant="subtle"
										size="xs"
									>
										{{ t('agal.status.past') }}
									</UBadge>
								</div>
								<div class="app-feed-card__subtext app-feed-card__subtext--bright">
									{{ requestItem.contents_summary }}
								</div>
							</div>
							<div class="app-feed-card__price">
								{{ formatMoney(requestItem.budget, 'agal.request.budgetNegotiable') }}
							</div>
						</div>
					</button>

					<button
						v-for="response in myResponses"
						:key="`my-response-${response.id}`"
						type="button"
						class="app-feed-card"
						@click="handleResponseClick(response)"
					>
						<div class="app-feed-card__main">
							<div class="app-feed-card__route">
								<UIcon
									:name="response.route_id ? 'i-lucide-route' : 'i-lucide-package-open'"
									class="shrink-0 text-cyan-400"
								/>
								<span class="app-feed-card__route-text">
									{{
										response.route
											? `${response.route.from_address} → ${response.route.to_address}`
											: response.request
												? `${response.request.from_address} → ${response.request.to_address}`
												: response.route_id
													? t('agal.myResponse.route')
													: t('agal.myResponse.request')
									}}
								</span>
							</div>
							<div class="mt-3 flex flex-wrap gap-2">
								<UBadge :color="responseStatusColor(response.status)" variant="subtle" size="xs">
									{{ t(`agal.respond.status.${response.status}`) }}
								</UBadge>
								<UBadge
									v-if="response.route?.status || response.request?.status"
									color="primary"
									variant="outline"
									size="xs"
								>
									{{ t(`agal.status.${response.route?.status || response.request?.status}`) }}
								</UBadge>
							</div>
							<div class="app-feed-card__meta">
								<span class="app-feed-card__meta-item">
									#{{ response.route_id ?? response.request_id }}
								</span>
								<span
									v-if="response.route?.date || response.request?.date"
									class="app-feed-card__meta-item"
								>
									{{ response.route?.date || response.request?.date }}
								</span>
								<span
									v-if="response.route?.time || response.request?.time"
									class="app-feed-card__meta-item"
								>
									{{ response.route?.time || response.request?.time }}
								</span>
							</div>
							<div class="app-feed-card__subtext">
								{{ response.route?.carrier.name || response.request?.sender.name }}
							</div>
							<div v-if="response.message" class="app-feed-card__subtext app-feed-card__subtext--bright">
								{{ response.message }}
							</div>
						</div>
					</button>
				</div>
			</template>

			<AgalCreateSlideover v-model:open="createOpen" @created="handleCreated" />
		</template>
	</div>
</template>

<style scoped>
.agal-tabs-panel,
.agal-filter-panel {
	padding: 14px;
	margin-bottom: 12px;
}

.agal-filter-panel__body {
	margin-top: 12px;
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.agal-cta {
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
