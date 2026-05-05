<script setup lang="ts">
import type { UusCategory, UusResponse, UusTaskStatus } from '../../../types/uus'

import { getApiErrorMessage } from '~/utils/api-error'
import { getServiceAccessState } from '~/utils/auth'

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
	getServiceAccessState({
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

function urgencyColor(urgency: 'urgent' | 'normal') {
	return urgency === 'urgent' ? 'error' : 'primary'
}

function categoryLabel(category: string) {
	return t(`uus.category.${category}`)
}

function categoryIcon(category: UusCategory) {
	if (category === 'home') return 'i-lucide-house'
	if (category === 'repair') return 'i-lucide-hammer'
	if (category === 'delivery') return 'i-lucide-package'
	return 'i-lucide-briefcase-business'
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

function sanitizeIntegerInput(value: string | number) {
	return String(value ?? '').replace(/[^\d]/g, '')
}

function handlePriceInput(value: string | number) {
	responsePrice.value = sanitizeIntegerInput(value)
}

function parsePrice(value: string) {
	if (!value) return null
	const parsed = Number.parseInt(value.replace(/[^\d]/g, ''), 10)
	return Number.isFinite(parsed) ? parsed : null
}

function blurActiveField() {
	if (typeof document === 'undefined') return
	const activeElement = document.activeElement
	if (activeElement instanceof HTMLElement) activeElement.blur()
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
	blurActiveField()
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
		toast.add({
			title: t('uus.respond.successTitle'),
			description: t('uus.respond.successDesc'),
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

async function handleCancelOwnResponse() {
	if (!myResponse.value) return

	cancellingResponse.value = true
	try {
		await cancelResponse(myResponse.value.id)
		await loadMyResponses()
		hapticFeedback('notification')
		toast.add({
			title: t('uus.respond.cancelledTitle'),
			description: t('uus.respond.cancelledDesc'),
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
		toast.add({
			title: getApiErrorMessage(error, t('common.error')),
			color: 'error',
			icon: 'i-lucide-x-circle',
			duration: 4000
		})
	}
}

async function handleReject(response: UusResponse) {
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

async function handleTaskOutcome(status: 'completed' | 'cancelled') {
	if (!task.value) return

	try {
		await updateTask(task.value.id, { status })
		hapticFeedback('notification')
		await refreshTask()
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

		<AppAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<div v-else-if="loading" class="flex justify-center py-12">
			<LoadingSpinner />
		</div>

		<template v-else-if="task">
			<section class="app-panel app-detail-hero">
				<div class="flex items-start justify-between gap-4">
					<div class="min-w-0 flex-1">
						<p class="app-kicker">{{ t('uus.badge') }}</p>
						<h1 class="app-detail-title mt-2">{{ categoryLabel(task.category) }}</h1>
					</div>
					<div class="uus-task-icon">
						<UIcon :name="categoryIcon(task.category)" class="size-6" />
					</div>
				</div>
				<div class="app-detail-meta">
					<UBadge :color="taskStatusColor(task.status)" variant="subtle" size="xs">
						{{ t(`uus.status.${task.status}`) }}
					</UBadge>
					<UBadge :color="urgencyColor(task.urgency)" variant="outline" size="xs">
						{{ t(`uus.urgency.${task.urgency}`) }}
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
						<span class="app-detail-label">{{ t('uus.task.responseLimit') }}</span>
						<span class="app-detail-value">{{ task.response_limit }}</span>
					</div>
					<div class="app-detail-divider">
						<span class="app-detail-label">{{ t('uus.create.description') }}</span>
						<p class="app-detail-copy">{{ task.description }}</p>
					</div>
				</div>
			</section>

			<section v-if="canRespond" class="app-panel app-panel--soft app-detail-card tma-no-zoom">
				<h2 class="app-section-title">{{ t('uus.respond.title') }}</h2>
				<div class="app-detail-stack">
					<UTextarea
						v-model="responseMessage"
						fixed
						:rows="3"
						autoresize
						:placeholder="t('uus.respond.messagePlaceholder')"
						class="w-full uus-response-field"
					/>
					<UInput
						:model-value="responsePrice"
						fixed
						inputmode="numeric"
						variant="outline"
						size="lg"
						:placeholder="t('uus.respond.pricePlaceholder')"
						class="w-full uus-response-field"
						@update:model-value="handlePriceInput"
					>
						<template #trailing>
							<span class="text-sm text-gray-500">₽</span>
						</template>
					</UInput>
					<UButton
						block
						class="tma-no-zoom-button"
						color="primary"
						:loading="responding"
						icon="i-lucide-send"
						:label="t('uus.respond.button')"
						@click="handleRespond"
					/>
				</div>
			</section>

			<section v-else-if="myResponse" class="app-panel app-panel--soft app-detail-card">
				<div class="flex items-center justify-between gap-3">
					<div>
						<h2 class="app-section-title mb-1">{{ t('uus.myResponse.title') }}</h2>
						<p class="app-detail-muted">{{ t('uus.myResponse.desc') }}</p>
					</div>
					<UBadge :color="responseStatusColor(myResponse.status)" variant="subtle" size="xs">
						{{ t(`uus.respond.status.${myResponse.status}`) }}
					</UBadge>
				</div>
				<div v-if="myResponse.message" class="app-detail-copy">{{ myResponse.message }}</div>
				<div v-if="myResponse.offered_price !== null" class="app-detail-copy">
					{{ formatPrice(myResponse.offered_price, '₽') }}
				</div>
				<div v-if="myResponse.status === 'accepted' && task.customer.username">
					<a
						:href="`https://t.me/${task.customer.username.replace('@', '')}`"
						target="_blank"
						class="app-inline-link"
					>
						<UIcon name="i-lucide-send" class="size-4" />
						{{ task.customer.username }}
					</a>
				</div>
				<UButton
					v-if="myResponse.status === 'pending'"
					class="mt-4"
					color="neutral"
					variant="outline"
					block
					:loading="cancellingResponse"
					@click="handleCancelOwnResponse"
				>
					{{ t('uus.respond.cancelOwn') }}
				</UButton>
			</section>

			<section v-if="isOwner" class="app-panel app-panel--soft app-detail-card">
				<div class="flex items-start justify-between gap-3">
					<div>
						<h2 class="app-section-title mb-1">{{ t('uus.responses.title') }}</h2>
						<p class="app-detail-muted">{{ t('uus.responses.desc') }}</p>
					</div>
					<span class="uus-response-counter">{{ responses.length }}/{{ task.response_limit }}</span>
				</div>

				<EmptyState
					v-if="!responses.length"
					:title="t('uus.responses.emptyTitle')"
					:description="t('uus.responses.emptyDesc')"
				/>

				<div v-else class="app-detail-stack mt-4">
					<div
						v-for="response in responses"
						:key="response.id"
						class="app-panel app-panel--soft app-detail-card app-detail-card--compact"
					>
						<div class="flex items-start justify-between gap-3">
							<div class="min-w-0 flex-1">
								<div class="flex flex-wrap items-center gap-2">
									<span class="app-detail-value !text-left">{{ response.user.name }}</span>
									<UBadge :color="responseStatusColor(response.status)" variant="subtle" size="xs">
										{{ t(`uus.respond.status.${response.status}`) }}
									</UBadge>
									<span v-if="response.offered_price !== null" class="app-chip">
										{{ formatPrice(response.offered_price, '₽') }}
									</span>
								</div>
								<div class="app-detail-muted mt-2">
									{{ response.message || t('uus.responses.noMessage') }}
								</div>
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
						</div>
						<div
							v-if="response.status === 'pending' && task.status === 'open'"
							class="mt-4 grid grid-cols-2 gap-2"
						>
							<UButton color="success" variant="soft" @click="handleAccept(response)">
								{{ t('uus.responses.accept') }}
							</UButton>
							<UButton color="error" variant="soft" @click="handleReject(response)">
								{{ t('uus.responses.reject') }}
							</UButton>
						</div>
					</div>
				</div>
			</section>

			<section v-if="isOwner && task.status === 'matched'" class="app-panel app-panel--soft app-detail-card">
				<h2 class="app-section-title mb-3">{{ t('uus.task.outcomeTitle') }}</h2>
				<div class="grid grid-cols-2 gap-2">
					<UButton color="success" variant="soft" @click="handleTaskOutcome('completed')">
						{{ t('uus.task.complete') }}
					</UButton>
					<UButton color="error" variant="soft" @click="handleTaskOutcome('cancelled')">
						{{ t('uus.task.cancel') }}
					</UButton>
				</div>
			</section>

			<section v-else-if="isOwner && task.status === 'open'" class="app-panel app-panel--soft app-detail-card">
				<UButton block color="neutral" variant="outline" @click="handleTaskOutcome('cancelled')">
					{{ t('uus.task.cancel') }}
				</UButton>
			</section>
		</template>

		<EmptyState v-else :title="t('common.error')" />
	</div>
</template>

<style scoped>
.uus-task-icon {
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

.uus-response-counter {
	display: inline-flex;
	align-items: center;
	justify-content: center;
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
