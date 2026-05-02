<script setup lang="ts">
import type { UusResponse, UusTaskStatus } from '../../../types/uus'

import { getApiErrorMessage } from '~/utils/api-error'
import { getAyanAccessState } from '~/utils/auth'

definePageMeta({ lazy: true })

const route = useRoute()
const { t } = useI18n()
const toast = useToast()
const { hapticFeedback, isInTelegram } = useTg()
const { user: authUser, isAuthenticated, isLoading: authLoading, authError } = useAuth()
const { fetchTask, updateTask } = useUusTasks()
const { fetchTaskResponses, createTaskResponse, updateResponseStatus, cancelResponse } = useUusResponses()
const { fetchMyResponses } = useUusMy()

const taskId = computed(() => Number(route.params.id))

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
	data: task,
	pending: loading,
	refresh: refreshTask
} = useLazyAsyncData(`uus-task-${taskId.value}`, () => fetchTask(taskId.value), {
	default: () => null,
	watch: [canUseUus],
	immediate: canUseUus.value
})

const responses = ref<UusResponse[]>([])
const myResponses = ref<UusResponse[]>([])
const responseMessage = ref('')
const responsePrice = ref('')
const responding = ref(false)
const cancellingResponse = ref(false)

const isOwner = computed(() => !!task.value && !!authUser.value && task.value.customer.id === authUser.value.id)
const myResponse = computed(() => myResponses.value.find((item) => item.task_id === taskId.value) ?? null)
const canRespond = computed(() => !!task.value && !isOwner.value && !myResponse.value && task.value.status === 'open')

function taskStatusColor(status: UusTaskStatus) {
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

function categoryLabel(category: string) {
	return t(`uus.category.${category}`)
}

function whenLabel() {
	if (!task.value) return '—'
	if (task.value.desired_when === 'date' && task.value.date) return task.value.date
	return t(`uus.when.${task.value.desired_when}`)
}

function budgetLabel() {
	if (!task.value) return '—'
	if (task.value.budget === null) return t('uus.budgetNegotiable')
	return formatPrice(task.value.budget, '₽')
}

function parsePrice(value: string) {
	if (!value) return null
	const parsed = Number.parseInt(value.replace(/[^\d]/g, ''), 10)
	return Number.isFinite(parsed) ? parsed : null
}

async function loadResponses() {
	try {
		responses.value = await fetchTaskResponses(taskId.value)
	} catch (error) {
		responses.value = []
		console.error('[uus.task] Failed to load responses:', error)
	}
}

async function loadMyResponses() {
	try {
		myResponses.value = await fetchMyResponses()
	} catch (error) {
		myResponses.value = []
		console.error('[uus.task] Failed to load my responses:', error)
	}
}

async function handleRespond() {
	responding.value = true
	try {
		await createTaskResponse(taskId.value, {
			message: responseMessage.value.trim() || undefined,
			offered_price: parsePrice(responsePrice.value)
		})
		await loadMyResponses()
		responseMessage.value = ''
		responsePrice.value = ''
		hapticFeedback('notification')
		toast.add({ title: t('uus.respond.successTitle'), description: t('uus.respond.successDesc'), color: 'success', icon: 'i-lucide-check-circle', duration: 3000 })
	} catch (error) {
		hapticFeedback('impact')
		toast.add({ title: getApiErrorMessage(error, t('common.error')), color: 'error', icon: 'i-lucide-x-circle', duration: 4000 })
	} finally {
		responding.value = false
	}
}

async function handleCancelOwnResponse() {
		if (!myResponse.value) return
		cancellingResponse.value = true
		try {
			await cancelResponse(myResponse.value.id)
			await loadMyResponses()
			hapticFeedback('notification')
			toast.add({ title: t('uus.respond.cancelledTitle'), description: t('uus.respond.cancelledDesc'), color: 'success', icon: 'i-lucide-check-circle', duration: 3000 })
		} catch (error) {
			hapticFeedback('impact')
			toast.add({ title: getApiErrorMessage(error, t('common.error')), color: 'error', icon: 'i-lucide-x-circle', duration: 4000 })
		} finally {
			cancellingResponse.value = false
		}
}

async function handleAccept(response: UusResponse) {
	try {
		await updateResponseStatus(response.id, 'accepted')
		hapticFeedback('notification')
		await refreshTask()
		await loadResponses()
	} catch (error) {
		hapticFeedback('impact')
		toast.add({ title: getApiErrorMessage(error, t('common.error')), color: 'error', icon: 'i-lucide-x-circle', duration: 4000 })
	}
}

async function handleReject(response: UusResponse) {
	try {
		await updateResponseStatus(response.id, 'rejected')
		hapticFeedback('notification')
		await loadResponses()
	} catch (error) {
		hapticFeedback('impact')
		toast.add({ title: getApiErrorMessage(error, t('common.error')), color: 'error', icon: 'i-lucide-x-circle', duration: 4000 })
	}
}

async function handleTaskOutcome(status: 'completed' | 'cancelled') {
	if (!task.value) return

	try {
		await updateTask(task.value.id, { status })
		hapticFeedback('notification')
		await refreshTask()
		await loadResponses()
	} catch (error) {
		hapticFeedback('impact')
		toast.add({ title: getApiErrorMessage(error, t('common.error')), color: 'error', icon: 'i-lucide-x-circle', duration: 4000 })
	}
}

watch(
	canUseUus,
	(ready) => {
		if (ready) refreshTask()
	},
	{ immediate: true }
)

watch(
	isOwner,
	async (owner) => {
		if (!canUseUus.value) {
			responses.value = []
			myResponses.value = []
			return
		}

		if (owner) {
			await loadResponses()
			myResponses.value = []
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

		<UusAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<div v-else-if="loading" class="flex justify-center py-12">
			<LoadingSpinner />
		</div>

		<template v-else-if="task">
			<section class="app-panel app-detail-hero">
				<h1 class="app-detail-title">{{ categoryLabel(task.category) }}</h1>
				<div class="app-detail-meta">
					<UBadge :color="taskStatusColor(task.status)" variant="subtle" size="xs">
						{{ t(`uus.status.${task.status}`) }}
					</UBadge>
					<span class="app-chip">{{ task.location }}</span>
					<span class="app-chip">{{ whenLabel() }}</span>
					<span class="app-chip">{{ budgetLabel() }}</span>
				</div>
			</section>

			<section class="app-panel app-panel--soft app-detail-card">
				<div class="app-detail-stack">
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('uus.task.customer') }}</span>
						<span class="app-detail-value">{{ task.customer.name }}</span>
					</div>
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('uus.task.urgency') }}</span>
						<span class="app-detail-value">{{ t(`uus.urgency.${task.urgency}`) }}</span>
					</div>
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('uus.task.responseLimit') }}</span>
						<span class="app-detail-value">{{ task.response_limit }}</span>
					</div>
				</div>
				<div class="app-detail-copy mt-4">{{ task.description }}</div>
			</section>

			<section v-if="canRespond" class="app-panel app-panel--soft app-detail-card">
				<h2 class="app-section-title mb-3">{{ t('uus.respond.title') }}</h2>
				<div class="grid gap-3">
					<UTextarea v-model="responseMessage" :rows="4" :placeholder="t('uus.respond.messagePlaceholder')" />
					<UInput v-model="responsePrice" inputmode="numeric" :placeholder="t('uus.respond.pricePlaceholder')" />
					<UButton color="primary" :loading="responding" @click="handleRespond">
						{{ t('uus.respond.button') }}
					</UButton>
				</div>
			</section>

			<section v-else-if="myResponse" class="app-panel app-panel--soft app-detail-card">
				<div class="flex items-start justify-between gap-3">
					<div>
						<h2 class="app-section-title mb-1">{{ t('uus.myResponse.title') }}</h2>
						<p class="app-detail-muted">{{ t('uus.myResponse.desc') }}</p>
					</div>
					<UBadge :color="responseStatusColor(myResponse.status)" variant="subtle" size="xs">
						{{ t(`uus.respond.status.${myResponse.status}`) }}
					</UBadge>
				</div>
				<div v-if="myResponse.message" class="app-detail-copy mt-3">{{ myResponse.message }}</div>
				<div v-if="myResponse.offered_price !== null" class="app-detail-copy mt-2">{{ formatPrice(myResponse.offered_price, '₽') }}</div>
				<div v-if="myResponse.status === 'accepted' && task.customer.username" class="mt-3">
					<a :href="`https://t.me/${task.customer.username.replace('@', '')}`" target="_blank" class="app-inline-link">
						{{ task.customer.username }}
					</a>
				</div>
				<UButton v-if="myResponse.status === 'pending'" class="mt-4" color="neutral" variant="outline" :loading="cancellingResponse" @click="handleCancelOwnResponse">
					{{ t('uus.respond.cancelOwn') }}
				</UButton>
			</section>

			<section v-if="isOwner" class="app-panel app-panel--soft app-detail-card">
				<div class="flex items-start justify-between gap-3">
					<div>
						<h2 class="app-section-title mb-1">{{ t('uus.responses.title') }}</h2>
						<p class="app-detail-muted">{{ t('uus.responses.desc') }}</p>
					</div>
					<UBadge color="primary" variant="outline" size="xs">
						{{ responses.length }} / {{ task.response_limit }}
					</UBadge>
				</div>

				<EmptyState v-if="!responses.length" :title="t('uus.responses.emptyTitle')" :description="t('uus.responses.emptyDesc')" />

				<div v-for="response in responses" :key="response.id" class="app-panel app-panel--soft mt-4">
					<div class="flex items-start justify-between gap-3">
						<div>
							<p class="font-medium text-white">{{ response.user.name }}</p>
							<p class="app-detail-muted">{{ response.message || t('uus.responses.noMessage') }}</p>
						</div>
						<UBadge :color="responseStatusColor(response.status)" variant="subtle" size="xs">
							{{ t(`uus.respond.status.${response.status}`) }}
						</UBadge>
					</div>
					<div v-if="response.offered_price !== null" class="app-detail-copy mt-3">{{ formatPrice(response.offered_price, '₽') }}</div>
					<div v-if="response.status === 'pending' && task.status === 'open'" class="mt-4 flex flex-wrap gap-3">
						<UButton color="primary" @click="handleAccept(response)">{{ t('uus.responses.accept') }}</UButton>
						<UButton color="neutral" variant="outline" @click="handleReject(response)">{{ t('uus.responses.reject') }}</UButton>
					</div>
					<div v-if="response.status === 'accepted' && response.user.username" class="mt-3">
						<a :href="`https://t.me/${response.user.username.replace('@', '')}`" target="_blank" class="app-inline-link">
							{{ response.user.username }}
						</a>
					</div>
				</div>
			</section>

			<section v-if="isOwner && task.status === 'matched'" class="app-panel app-panel--soft app-detail-card">
				<h2 class="app-section-title mb-3">{{ t('uus.task.outcomeTitle') }}</h2>
				<div class="flex flex-wrap gap-3">
					<UButton color="primary" @click="handleTaskOutcome('completed')">{{ t('uus.task.complete') }}</UButton>
					<UButton color="neutral" variant="outline" @click="handleTaskOutcome('cancelled')">{{ t('uus.task.cancel') }}</UButton>
				</div>
			</section>

			<section v-else-if="isOwner && task.status === 'open'" class="app-panel app-panel--soft app-detail-card">
				<UButton color="neutral" variant="outline" @click="handleTaskOutcome('cancelled')">{{ t('uus.task.cancel') }}</UButton>
			</section>
		</template>
	</div>
</template>
