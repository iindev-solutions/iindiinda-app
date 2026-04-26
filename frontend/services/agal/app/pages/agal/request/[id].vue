<script setup lang="ts">
import type { AgalResponse } from '../../../types/agal'

import { getApiErrorMessage } from '~/utils/api-error'
import { getAyanAccessState } from '~/utils/auth'
import { findTargetResponse } from '../../../utils/responses'

definePageMeta({ lazy: true })

const route = useRoute()
const { t } = useI18n()
const toast = useToast()
const { hapticFeedback, isInTelegram } = useTg()
const { user: authUser, isAuthenticated, isLoading: authLoading, authError } = useAuth()
const { fetchRequest, updateRequest } = useAgalRequests()
const { fetchRequestResponses, createRequestResponse, updateResponseStatus } = useAgalResponses()
const { fetchMyResponses } = useAgalMy()

const requestId = computed(() => Number(route.params.id))

const accessState = computed(() =>
	getAyanAccessState({
		isAuthenticated: isAuthenticated.value,
		isLoading: authLoading.value,
		isInTelegram: isInTelegram.value,
		hasAuthError: !!authError.value
	})
)

const canUseAgal = computed(() => accessState.value === 'ready')

const {
	data: requestItem,
	pending: loading,
	refresh: refreshRequest
} = useLazyAsyncData(`agal-request-${requestId.value}`, () => fetchRequest(requestId.value), {
	default: () => null,
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const responses = ref<AgalResponse[]>([])
const myResponses = ref<AgalResponse[]>([])
const responding = ref(false)
const responseMessage = ref('')

const isPastRequest = computed(() =>
	requestItem.value ? isPastAyanDateTime(requestItem.value.date, requestItem.value.time) : false
)

const isOwner = computed(() => {
	if (!requestItem.value || !authUser.value) return false
	return requestItem.value.sender.id === authUser.value.id
})

const myResponse = computed(() => findTargetResponse(myResponses.value, { requestId: requestId.value }))

const canRespond = computed(
	() =>
		!isOwner.value &&
		!myResponse.value &&
		!isPastRequest.value &&
		requestItem.value?.status === 'open' &&
		authUser.value?.role === 'carrier'
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

function formatBudgetLabel(budget: number | null) {
	if (budget === null) return t('agal.request.budgetNegotiable')
	return formatPrice(budget, '₽')
}

async function loadResponses() {
	try {
		responses.value = await fetchRequestResponses(requestId.value)
	} catch (error) {
		responses.value = []
		console.error('[agal.request] Failed to load responses:', error)
	}
}

async function loadMyResponses() {
	try {
		myResponses.value = await fetchMyResponses()
	} catch (error) {
		myResponses.value = []
		console.error('[agal.request] Failed to load my responses:', error)
	}
}

async function handleRespond() {
	responding.value = true
	try {
		await createRequestResponse(requestId.value, { message: responseMessage.value || undefined })
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
		await refreshRequest()
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

async function handleRequestOutcome(status: 'completed' | 'cancelled') {
	if (!requestItem.value) return

	try {
		await updateRequest(requestItem.value.id, { status })
		hapticFeedback('notification')
		await refreshRequest()
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
			refreshRequest()
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

		<AgalAccessState v-if="accessState !== 'ready'" :state="accessState" />

		<div v-else-if="loading" class="flex justify-center py-12">
			<LoadingSpinner />
		</div>

		<template v-else-if="requestItem">
			<section class="app-panel app-detail-hero">
				<h1 class="app-detail-title">{{ requestItem.from_address }} → {{ requestItem.to_address }}</h1>
				<div class="app-detail-meta">
					<UBadge :color="targetStatusColor(requestItem.status)" variant="subtle" size="xs">
						{{ t(`agal.status.${requestItem.status}`) }}
					</UBadge>
					<span class="app-chip">{{ requestItem.date }}</span>
					<span v-if="requestItem.time" class="app-chip">{{ requestItem.time }}</span>
					<span class="app-chip">{{ sizeLabel(requestItem.size_label) }}</span>
					<UBadge v-if="isPastRequest" color="neutral" variant="subtle" size="xs">
						{{ t('agal.status.past') }}
					</UBadge>
				</div>
			</section>

			<section class="app-panel app-panel--soft app-detail-card">
				<div class="app-detail-stack">
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('agal.sender') }}</span>
						<span class="app-detail-value">{{ requestItem.sender.name }}</span>
					</div>
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('agal.request.budget') }}</span>
						<span class="app-detail-value">{{ formatBudgetLabel(requestItem.budget) }}</span>
					</div>
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('agal.request.fragilityLabel') }}</span>
						<span class="app-detail-value">{{ t(`agal.fragility.${requestItem.fragility}`) }}</span>
					</div>
					<div class="app-detail-row">
						<span class="app-detail-label">{{ t('agal.request.documentsRequired') }}</span>
						<span class="app-detail-value">{{ requestItem.documents_required ? t('agal.yes') : t('agal.no') }}</span>
					</div>
					<div v-if="requestItem.weight_kg" class="app-detail-row">
						<span class="app-detail-label">{{ t('agal.request.weight') }}</span>
						<span class="app-detail-value">{{ requestItem.weight_kg }} кг</span>
					</div>
					<div class="app-detail-divider">
						<span class="app-detail-label">{{ t('agal.request.contentsSummary') }}</span>
						<p class="app-detail-copy">{{ requestItem.contents_summary }}</p>
					</div>
					<div v-if="requestItem.notes" class="app-detail-divider">
						<span class="app-detail-label">{{ t('agal.notes') }}</span>
						<p class="app-detail-copy">{{ requestItem.notes }}</p>
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
				<div v-if="myResponse.status === 'accepted' && requestItem.sender.username">
					<a :href="`https://t.me/${requestItem.sender.username.replace('@', '')}`" target="_blank" class="app-inline-link">
						<UIcon name="i-lucide-send" class="size-4" />
						{{ requestItem.sender.username }}
					</a>
				</div>
			</section>

			<section v-if="isOwner && requestItem.status === 'matched'" class="app-panel app-panel--soft app-detail-card">
				<div>
					<h2 class="app-section-title mb-1">{{ t('agal.match.title') }}</h2>
					<p class="app-detail-muted">{{ t('agal.match.desc') }}</p>
				</div>
				<div class="mt-3 grid grid-cols-2 gap-2">
					<UButton color="success" variant="soft" @click="handleRequestOutcome('completed')">
						{{ t('agal.match.complete') }}
					</UButton>
					<UButton color="error" variant="soft" @click="handleRequestOutcome('cancelled')">
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
									<a :href="`https://t.me/${response.user.username.replace('@', '')}`" target="_blank" class="app-inline-link">
										<UIcon name="i-lucide-send" class="size-3" />
										{{ response.user.username }}
									</a>
								</div>
							</div>
							<div v-if="isOwner && !isPastRequest && response.status === 'pending' && !hasAcceptedResponse" class="flex shrink-0 gap-1">
								<UButton size="xs" color="success" variant="soft" icon="i-lucide-check" @click="handleAccept(response)" />
								<UButton size="xs" color="error" variant="soft" icon="i-lucide-x" @click="handleReject(response)" />
							</div>
						</div>
					</div>
				</div>
			</section>
		</template>

		<EmptyState v-else :title="t('common.error')" />
	</div>
</template>
