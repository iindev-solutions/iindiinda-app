<script setup lang="ts">
import type { UusCategory, UusResponse, UusTask } from '../../types/uus'

import { getAyanAccessState } from '~/utils/auth'

definePageMeta({ lazy: true })

const { t } = useI18n()
const { hapticFeedback, isInTelegram } = useTg()
const { isAuthenticated, isLoading: authLoading, authError } = useAuth()

const activeTab = ref<'feed' | 'my-tasks' | 'my-responses'>('feed')
const createOpen = ref(false)
const filtersOpen = ref(false)

const filterCategory = ref('')
const filterLocation = ref('')
const filterUrgency = ref('')
const filterDesiredWhen = ref('')

const accessState = computed(() =>
	getAyanAccessState({
		isAuthenticated: isAuthenticated.value,
		isLoading: authLoading.value,
		isInTelegram: isInTelegram.value,
		hasAuthError: !!authError.value
	})
)

const canUseUus = computed(() => accessState.value === 'ready')

const {
	data: tasks,
	pending: tasksLoading,
	refresh: refreshTasks
} = useLazyAsyncData('uus-tasks', () => useUusTasks().fetchTasks(), {
	deep: false,
	default: () => [],
	watch: [canUseUus],
	immediate: canUseUus.value
})

const {
	data: myTasks,
	pending: myTasksLoading,
	refresh: refreshMyTasks
} = useLazyAsyncData('uus-my-tasks', () => useUusMy().fetchMyTasks(), {
	deep: false,
	default: () => [],
	watch: [canUseUus],
	immediate: canUseUus.value
})

const {
	data: myResponses,
	pending: myResponsesLoading,
	refresh: refreshMyResponses
} = useLazyAsyncData('uus-my-responses', () => useUusMy().fetchMyResponses(), {
	deep: false,
	default: () => [],
	watch: [canUseUus],
	immediate: canUseUus.value
})

const tabs = computed(() => [
	{ label: t('uus.tabs.feed'), value: 'feed', icon: 'i-lucide-list' },
	{ label: t('uus.tabs.myTasks'), value: 'my-tasks', icon: 'i-lucide-clipboard-list' },
	{ label: t('uus.tabs.myResponses'), value: 'my-responses', icon: 'i-lucide-message-square' }
])

const categoryOptions = computed(() => [
	{ label: t('uus.filters.allCategories'), value: '' },
	{ label: t('uus.category.home'), value: 'home' },
	{ label: t('uus.category.repair'), value: 'repair' },
	{ label: t('uus.category.delivery'), value: 'delivery' },
	{ label: t('uus.category.other'), value: 'other' }
])

const urgencyOptions = computed(() => [
	{ label: t('uus.filters.allUrgency'), value: '' },
	{ label: t('uus.urgency.normal'), value: 'normal' },
	{ label: t('uus.urgency.urgent'), value: 'urgent' }
])

const desiredWhenOptions = computed(() => [
	{ label: t('uus.filters.allWhen'), value: '' },
	{ label: t('uus.when.today'), value: 'today' },
	{ label: t('uus.when.tomorrow'), value: 'tomorrow' },
	{ label: t('uus.when.date'), value: 'date' },
	{ label: t('uus.when.flexible'), value: 'flexible' }
])

const aboutExamples = computed(() => [
	{
		title: t('serviceAbout.uus.examples.cleaning.title'),
		description: t('serviceAbout.uus.examples.cleaning.description')
	},
	{
		title: t('serviceAbout.uus.examples.repair.title'),
		description: t('serviceAbout.uus.examples.repair.description')
	}
])

const hasFilters = computed(
	() => !!filterCategory.value || !!filterLocation.value || !!filterUrgency.value || !!filterDesiredWhen.value
)

const activeFilterCount = computed(() => {
	let n = 0
	if (filterCategory.value) n++
	if (filterLocation.value) n++
	if (filterUrgency.value) n++
	if (filterDesiredWhen.value) n++
	return n
})

const loading = computed(() => {
	if (activeTab.value === 'feed') return tasksLoading.value
	if (activeTab.value === 'my-tasks') return myTasksLoading.value
	return myResponsesLoading.value
})

function matchTask(task: UusTask) {
	if (filterCategory.value && task.category !== filterCategory.value) return false
	if (filterUrgency.value && task.urgency !== filterUrgency.value) return false
	if (filterDesiredWhen.value && task.desired_when !== filterDesiredWhen.value) return false
	if (filterLocation.value && !task.location.toLowerCase().includes(filterLocation.value.toLowerCase())) return false
	return true
}

const filteredTasks = computed(() => {
	if (!tasks.value) return []
	if (!hasFilters.value) return tasks.value
	return tasks.value.filter(matchTask)
})

const filteredMyTasks = computed(() => {
	if (!myTasks.value) return []
	if (!hasFilters.value) return myTasks.value
	return myTasks.value.filter(matchTask)
})

const filteredMyResponses = computed(() => {
	if (!myResponses.value) return []
	if (!hasFilters.value) return myResponses.value
	return myResponses.value.filter((response) => (response.task ? matchTask(response.task) : false))
})

function formatBudget(task: UusTask | UusResponse['task']) {
	if (!task) return '—'
	if (task.budget === null) return t('uus.budgetNegotiable')
	return formatPrice(task.budget, '₽')
}

function categoryLabel(category: UusTask['category']) {
	return t(`uus.category.${category}`)
}

function categoryIcon(category: UusCategory) {
	if (category === 'home') return 'i-lucide-house'
	if (category === 'repair') return 'i-lucide-hammer'
	if (category === 'delivery') return 'i-lucide-package'
	return 'i-lucide-briefcase-business'
}

function urgencyLabel(urgency: UusTask['urgency']) {
	return t(`uus.urgency.${urgency}`)
}

function urgencyColor(urgency: UusTask['urgency']) {
	return urgency === 'urgent' ? 'error' : 'primary'
}

function whenLabel(task: UusTask) {
	if (task.desired_when === 'date' && task.date) return task.date
	return t(`uus.when.${task.desired_when}`)
}

function taskStatusColor(status: UusTask['status']) {
	if (status === 'open') return 'success'
	if (status === 'matched') return 'primary'
	if (status === 'cancelled') return 'error'
	return 'neutral'
}

function responseStatusColor(status: UusResponse['status']) {
	if (status === 'accepted') return 'success'
	if (status === 'rejected') return 'error'
	return 'neutral'
}

function clearFilters() {
	filterCategory.value = ''
	filterLocation.value = ''
	filterUrgency.value = ''
	filterDesiredWhen.value = ''
}

function handleTabChange(val: string | number) {
	activeTab.value = val as 'feed' | 'my-tasks' | 'my-responses'
	hapticFeedback('impact')
}

function toggleFilters() {
	filtersOpen.value = !filtersOpen.value
	hapticFeedback('impact')
}

function handleCreated() {
	refreshTasks()
	refreshMyTasks()
	refreshMyResponses()
}

function openCreate() {
	hapticFeedback('impact')
	createOpen.value = true
}

function openTask(id: number) {
	hapticFeedback('impact')
	navigateTo(`/uus/task/${id}`)
}

watch(
	canUseUus,
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
		<UusAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<template v-else>
			<AppHero
				:eyebrow="t('servicePages.uus.badge')"
				:title="t('servicePages.uus.title')"
				:description="t('servicePages.uus.intro')"
				icon="i-lucide-wrench"
			>
				<AppServiceAbout
					:label="t('serviceAbout.label')"
					:description="t('serviceAbout.uus.description')"
					:examples-title="t('serviceAbout.examplesTitle')"
					:examples="aboutExamples"
				/>
			</AppHero>

			<div class="app-panel app-panel--soft uus-tabs-panel">
				<UTabs
					:items="tabs"
					:model-value="activeTab"
					variant="pill"
					size="sm"
					class="w-full"
					@update:model-value="handleTabChange"
				/>
			</div>

			<div class="app-panel app-panel--soft uus-filter-panel">
				<UButton
					icon="i-lucide-filter"
					size="sm"
					variant="ghost"
					color="neutral"
					:trailing-icon="filtersOpen ? 'i-lucide-chevron-up' : 'i-lucide-chevron-down'"
					@click="toggleFilters"
				>
					{{ t('uus.filters.title') }}
					<UBadge v-if="hasFilters" color="primary" variant="subtle" size="xs" class="ml-1">
						{{ activeFilterCount }}
					</UBadge>
				</UButton>

				<Transition name="filter-slide">
					<div v-if="filtersOpen" class="uus-filter-panel__body">
						<div class="grid gap-2 sm:grid-cols-2">
							<USelect v-model="filterCategory" :items="categoryOptions" size="sm" class="w-full" />
							<UInput
								v-model="filterLocation"
								:placeholder="t('uus.filters.location')"
								icon="i-lucide-map-pin"
								variant="outline"
								size="sm"
								class="w-full"
							/>
							<USelect v-model="filterUrgency" :items="urgencyOptions" size="sm" class="w-full" />
							<USelect v-model="filterDesiredWhen" :items="desiredWhenOptions" size="sm" class="w-full" />
						</div>

						<UButton
							v-if="hasFilters"
							size="xs"
							variant="ghost"
							color="neutral"
							icon="i-lucide-x"
							@click="clearFilters"
						>
							{{ t('uus.filters.clear') }}
						</UButton>
					</div>
				</Transition>
			</div>

			<div class="uus-cta">
				<UButton icon="i-lucide-plus" size="lg" variant="soft" color="primary" block @click="openCreate">
					{{ t('uus.create.cta') }}
				</UButton>
			</div>

			<div v-if="loading" class="flex justify-center py-12">
				<LoadingSpinner />
			</div>

			<template v-else-if="activeTab === 'feed'">
				<EmptyState
					v-if="!filteredTasks.length"
					:title="hasFilters ? t('empty.noResults') : t('uus.feed.emptyTitle')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('uus.feed.emptyDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="task in filteredTasks"
						:key="task.id"
						type="button"
						class="app-feed-card"
						@click="openTask(task.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon :name="categoryIcon(task.category)" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ categoryLabel(task.category) }}</span>
								</div>
								<div class="mt-3 flex flex-wrap gap-2">
									<UBadge :color="urgencyColor(task.urgency)" variant="subtle" size="xs">
										{{ urgencyLabel(task.urgency) }}
									</UBadge>
									<UBadge color="primary" variant="outline" size="xs">
										{{ t('uus.responseLimit', { count: task.response_limit }) }}
									</UBadge>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">
										<UIcon name="i-lucide-map-pin" class="size-3.5" />
										{{ task.location }}
									</span>
									<span class="app-feed-card__meta-item">
										<UIcon name="i-lucide-calendar" class="size-3.5" />
										{{ whenLabel(task) }}
									</span>
									<span class="app-feed-card__meta-item">
										<UIcon name="i-lucide-user" class="size-3.5" />
										{{ task.customer.name }}
									</span>
								</div>
								<div class="app-feed-card__subtext app-feed-card__subtext--bright">
									{{ task.description }}
								</div>
							</div>
							<div class="app-feed-card__price">{{ formatBudget(task) }}</div>
						</div>
					</button>
				</div>
			</template>

			<template v-else-if="activeTab === 'my-tasks'">
				<EmptyState
					v-if="!filteredMyTasks.length"
					:title="hasFilters ? t('empty.noResults') : t('uus.my.noTasksTitle')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('uus.my.noTasksDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="task in filteredMyTasks"
						:key="`my-task-${task.id}`"
						type="button"
						class="app-feed-card"
						@click="openTask(task.id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon :name="categoryIcon(task.category)" class="shrink-0 text-cyan-400" />
									<span class="app-feed-card__route-text">{{ categoryLabel(task.category) }}</span>
								</div>
								<div class="mt-3 flex flex-wrap gap-2">
									<UBadge :color="taskStatusColor(task.status)" variant="subtle" size="xs">
										{{ t(`uus.status.${task.status}`) }}
									</UBadge>
									<UBadge :color="urgencyColor(task.urgency)" variant="outline" size="xs">
										{{ urgencyLabel(task.urgency) }}
									</UBadge>
								</div>
								<div class="app-feed-card__meta">
									<span class="app-feed-card__meta-item">
										<UIcon name="i-lucide-map-pin" class="size-3.5" />
										{{ task.location }}
									</span>
									<span class="app-feed-card__meta-item">
										<UIcon name="i-lucide-calendar" class="size-3.5" />
										{{ whenLabel(task) }}
									</span>
									<span class="app-feed-card__meta-item">
										<UIcon name="i-lucide-users" class="size-3.5" />
										{{ t('uus.responseLimit', { count: task.response_limit }) }}
									</span>
								</div>
								<div class="app-feed-card__subtext app-feed-card__subtext--bright">
									{{ task.description }}
								</div>
							</div>
							<div class="app-feed-card__price">{{ formatBudget(task) }}</div>
						</div>
					</button>
				</div>
			</template>

			<template v-else>
				<EmptyState
					v-if="!filteredMyResponses.length"
					:title="hasFilters ? t('empty.noResults') : t('uus.my.noResponsesTitle')"
					:description="hasFilters ? t('empty.noResultsDesc') : t('uus.my.noResponsesDesc')"
				/>
				<div v-else class="app-section-stack">
					<button
						v-for="response in filteredMyResponses"
						:key="`my-response-${response.id}`"
						type="button"
						class="app-feed-card"
						@click="openTask(response.task_id)"
					>
						<div class="app-feed-card__row">
							<div class="app-feed-card__main">
								<div class="app-feed-card__route">
									<UIcon
										:name="
											response.task
												? categoryIcon(response.task.category)
												: 'i-lucide-message-square'
										"
										class="shrink-0 text-cyan-400"
									/>
									<span class="app-feed-card__route-text">
										{{
											response.task
												? categoryLabel(response.task.category)
												: t('uus.response.title')
										}}
									</span>
								</div>
								<div class="mt-3 flex flex-wrap gap-2">
									<UBadge :color="responseStatusColor(response.status)" variant="subtle" size="xs">
										{{ t(`uus.respond.status.${response.status}`) }}
									</UBadge>
									<UBadge
										v-if="response.task"
										:color="taskStatusColor(response.task.status)"
										variant="outline"
										size="xs"
									>
										{{ t(`uus.status.${response.task.status}`) }}
									</UBadge>
								</div>
								<div class="app-feed-card__meta">
									<span v-if="response.task" class="app-feed-card__meta-item">
										<UIcon name="i-lucide-map-pin" class="size-3.5" />
										{{ response.task.location }}
									</span>
									<span v-if="response.task" class="app-feed-card__meta-item">
										<UIcon name="i-lucide-calendar" class="size-3.5" />
										{{ whenLabel(response.task) }}
									</span>
									<span v-if="response.offered_price !== null" class="app-feed-card__meta-item">
										<UIcon name="i-lucide-badge-russian-ruble" class="size-3.5" />
										{{ formatPrice(response.offered_price, '₽') }}
									</span>
								</div>
								<div class="app-feed-card__subtext app-feed-card__subtext--bright">
									{{ response.task?.description || response.message || t('uus.responses.noMessage') }}
								</div>
							</div>
							<div class="app-feed-card__price">{{ formatBudget(response.task) }}</div>
						</div>
					</button>
				</div>
			</template>
		</template>

		<UusCreateSlideover v-model:open="createOpen" @created="handleCreated" />
	</div>
</template>

<style scoped>
.uus-tabs-panel,
.uus-filter-panel {
	padding: 14px;
	margin-bottom: 12px;
}

.uus-filter-panel__body {
	margin-top: 12px;
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.uus-cta {
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
	max-height: 260px;
}
</style>
