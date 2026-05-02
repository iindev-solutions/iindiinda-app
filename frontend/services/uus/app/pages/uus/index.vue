<script setup lang="ts">
import type { UusResponse, UusTask } from '../../types/uus'

import { getAyanAccessState } from '~/utils/auth'

definePageMeta({ lazy: true })

const { t } = useI18n()
const { hapticFeedback, isInTelegram } = useTg()
const { user: authUser, isAuthenticated, isLoading: authLoading, authError } = useAuth()

const createOpen = ref(false)
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

const loading = computed(() => tasksLoading.value || myTasksLoading.value || myResponsesLoading.value)

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

function matchTask(task: UusTask) {
	if (filterCategory.value && task.category !== filterCategory.value) return false
	if (filterUrgency.value && task.urgency !== filterUrgency.value) return false
	if (filterDesiredWhen.value && task.desired_when !== filterDesiredWhen.value) return false
	if (filterLocation.value && !task.location.toLowerCase().includes(filterLocation.value.toLowerCase())) return false
	return true
}

const filteredTasks = computed(() => (tasks.value ?? []).filter(matchTask))
const filteredMyTasks = computed(() => (myTasks.value ?? []).filter(matchTask))
const filteredMyResponses = computed(() => myResponses.value ?? [])

function formatBudget(task: UusTask | UusResponse['task']) {
	if (!task) return '—'
	if (task.budget === null) return t('uus.budgetNegotiable')
	return formatPrice(task.budget, '₽')
}

function categoryLabel(category: UusTask['category']) {
	return t(`uus.category.${category}`)
}

function urgencyLabel(urgency: UusTask['urgency']) {
	return t(`uus.urgency.${urgency}`)
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
</script>

<template>
	<div class="app-page">
		<UusAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<template v-else>
			<section class="app-panel app-detail-hero">
				<div class="flex items-start justify-between gap-4">
					<div>
						<p class="app-detail-muted">{{ t('uus.badge') }}</p>
						<h1 class="app-detail-title">{{ t('uus.title') }}</h1>
						<p class="app-detail-copy mt-2">{{ t('uus.intro') }}</p>
					</div>
					<UButton color="primary" icon="i-lucide-plus" @click="openCreate">
						{{ t('uus.create.cta') }}
					</UButton>
				</div>
			</section>

			<section class="app-panel app-panel--soft app-detail-card">
				<h2 class="app-section-title mb-4">{{ t('uus.filters.title') }}</h2>
				<div class="grid gap-3 md:grid-cols-2">
					<USelect v-model="filterCategory" :items="categoryOptions" size="lg" class="w-full" />
					<UInput v-model="filterLocation" :placeholder="t('uus.filters.location')" size="lg" class="w-full" />
					<USelect v-model="filterUrgency" :items="urgencyOptions" size="lg" class="w-full" />
					<USelect v-model="filterDesiredWhen" :items="desiredWhenOptions" size="lg" class="w-full" />
				</div>
			</section>

			<div v-if="loading" class="flex justify-center py-12">
				<LoadingSpinner />
			</div>

			<template v-else>
				<section class="app-section-stack">
					<h2 class="app-section-title">{{ t('uus.my.tasksTitle') }}</h2>
					<EmptyState v-if="!filteredMyTasks.length" :title="t('uus.my.noTasksTitle')" :description="t('uus.my.noTasksDesc')" />
					<button
						v-for="task in filteredMyTasks"
						:key="`my-task-${task.id}`"
						type="button"
						class="app-panel app-panel--soft app-list-card text-left"
						@click="openTask(task.id)"
					>
						<div class="app-list-card__header">
							<h3 class="app-list-card__title">{{ categoryLabel(task.category) }}</h3>
							<UBadge :color="taskStatusColor(task.status)" variant="subtle" size="xs">
								{{ t(`uus.status.${task.status}`) }}
							</UBadge>
						</div>
						<p class="app-list-card__description">{{ task.description }}</p>
						<div class="app-list-card__meta">
							<span class="app-chip">{{ task.location }}</span>
							<span class="app-chip">{{ whenLabel(task) }}</span>
							<span class="app-chip">{{ formatBudget(task) }}</span>
						</div>
					</button>
				</section>

				<section class="app-section-stack">
					<h2 class="app-section-title">{{ t('uus.my.responsesTitle') }}</h2>
					<EmptyState v-if="!filteredMyResponses.length" :title="t('uus.my.noResponsesTitle')" :description="t('uus.my.noResponsesDesc')" />
					<button
						v-for="response in filteredMyResponses"
						:key="`my-response-${response.id}`"
						type="button"
						class="app-panel app-panel--soft app-list-card text-left"
						@click="openTask(response.task_id)"
					>
						<div class="app-list-card__header">
							<h3 class="app-list-card__title">{{ response.task ? categoryLabel(response.task.category) : t('uus.response.title') }}</h3>
							<UBadge :color="responseStatusColor(response.status)" variant="subtle" size="xs">
								{{ t(`uus.respond.status.${response.status}`) }}
							</UBadge>
						</div>
						<p class="app-list-card__description">{{ response.task?.description || response.message }}</p>
						<div class="app-list-card__meta">
							<span v-if="response.task" class="app-chip">{{ response.task.location }}</span>
							<span v-if="response.offered_price !== null" class="app-chip">{{ formatPrice(response.offered_price, '₽') }}</span>
						</div>
					</button>
				</section>

				<section class="app-section-stack">
					<h2 class="app-section-title">{{ t('uus.feed.title') }}</h2>
					<EmptyState v-if="!filteredTasks.length" :title="t('uus.feed.emptyTitle')" :description="t('uus.feed.emptyDesc')" />
					<button
						v-for="task in filteredTasks"
						:key="task.id"
						type="button"
						class="app-panel app-panel--soft app-list-card text-left"
						@click="openTask(task.id)"
					>
						<div class="app-list-card__header">
							<h3 class="app-list-card__title">{{ categoryLabel(task.category) }}</h3>
							<UBadge :color="taskStatusColor(task.status)" variant="subtle" size="xs">
								{{ urgencyLabel(task.urgency) }}
							</UBadge>
						</div>
						<p class="app-list-card__description">{{ task.description }}</p>
						<div class="app-list-card__meta">
							<span class="app-chip">{{ task.location }}</span>
							<span class="app-chip">{{ whenLabel(task) }}</span>
							<span class="app-chip">{{ formatBudget(task) }}</span>
							<span class="app-chip">{{ t('uus.responseLimit', { count: task.response_limit }) }}</span>
						</div>
					</button>
				</section>
			</template>
		</template>

		<UusCreateSlideover v-model:open="createOpen" @created="handleCreated" />
	</div>
</template>
