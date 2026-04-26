<script setup lang="ts">
import { getAyanAccessState } from '~/utils/auth'

definePageMeta({ lazy: true })

const { t } = useI18n()
const { hapticFeedback, isInTelegram } = useTg()
const { isAuthenticated, isLoading: authLoading, authError } = useAuth()

const activeTab = ref<'routes' | 'requests' | 'my'>('routes')

const tabs = computed(() => [
	{ label: t('agal.routes'), value: 'routes', icon: 'i-lucide-route' },
	{ label: t('agal.requests'), value: 'requests', icon: 'i-lucide-package-open' },
	{ label: t('agal.my'), value: 'my', icon: 'i-lucide-user' }
])

const accessState = computed(() =>
	getAyanAccessState({
		isAuthenticated: isAuthenticated.value,
		isLoading: authLoading.value,
		isInTelegram: isInTelegram.value,
		hasAuthError: !!authError.value
	})
)

const canUseAgal = computed(() => accessState.value === 'ready')

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

const {
	data: routes,
	pending: routesLoading
} = useLazyAsyncData('agal-routes', () => useAgalRoutes().fetchRoutes(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const {
	data: requests,
	pending: requestsLoading
} = useLazyAsyncData('agal-requests', () => useAgalRequests().fetchRequests(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const {
	data: myRoutes,
	pending: myRoutesLoading
} = useLazyAsyncData('agal-my-routes', () => useAgalMy().fetchMyRoutes(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const {
	data: myRequests,
	pending: myRequestsLoading
} = useLazyAsyncData('agal-my-requests', () => useAgalMy().fetchMyRequests(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const {
	data: myResponses,
	pending: myResponsesLoading
} = useLazyAsyncData('agal-my-responses', () => useAgalMy().fetchMyResponses(), {
	deep: false,
	default: () => [],
	watch: [canUseAgal],
	immediate: canUseAgal.value
})

const loading = computed(() => {
	if (activeTab.value === 'routes') return routesLoading.value
	if (activeTab.value === 'requests') return requestsLoading.value
	return myRoutesLoading.value || myRequestsLoading.value || myResponsesLoading.value
})

function handleTabChange(val: string | number) {
	activeTab.value = val as 'routes' | 'requests' | 'my'
	hapticFeedback('impact')
}
</script>

<template>
	<div class="min-h-screen px-4 py-6">
		<div class="mx-auto max-w-[480px]">
			<AgalAccessState v-if="accessState !== 'ready'" :state="accessState" />

			<template v-else>
				<header class="mb-6">
					<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
						{{ t('servicePages.agal.badge') }}
					</div>
					<h1 class="mb-2 text-2xl font-medium tracking-tight text-cyan-50">
						{{ t('servicePages.agal.title') }}
					</h1>
					<p class="text-sm leading-relaxed text-gray-300">
						{{ t('servicePages.agal.intro') }}
					</p>
					<div class="mt-4">
						<AppServiceAbout
							:label="t('serviceAbout.label')"
							:description="t('serviceAbout.agal.description')"
							:examples-title="t('serviceAbout.examplesTitle')"
							:examples="aboutExamples"
						/>
					</div>
				</header>

				<div class="mb-4 rounded-2xl border border-gray-800 bg-gray-900/70 p-4">
					<div class="text-sm font-medium text-cyan-50">{{ t('agal.buildingTitle') }}</div>
					<p class="mt-1 text-xs leading-relaxed text-gray-400">
						{{ t('agal.buildingDesc') }}
					</p>
				</div>

				<UTabs
					:items="tabs"
					:model-value="activeTab"
					variant="pill"
					size="sm"
					class="mb-5"
					@update:model-value="handleTabChange"
				/>

				<div v-if="loading" class="flex justify-center py-12">
					<LoadingSpinner />
				</div>

				<template v-else-if="activeTab === 'routes'">
					<EmptyState
						:title="routes.length ? t('agal.routes') : t('agal.noRoutes')"
						:description="routes.length ? t('agal.feedReadyDesc') : t('agal.noRoutesDesc')"
					/>
				</template>

				<template v-else-if="activeTab === 'requests'">
					<EmptyState
						:title="requests.length ? t('agal.requests') : t('agal.noRequests')"
						:description="requests.length ? t('agal.feedReadyDesc') : t('agal.noRequestsDesc')"
					/>
				</template>

				<template v-else>
					<EmptyState
						:title="myRoutes.length || myRequests.length || myResponses.length ? t('agal.my') : t('agal.noMy')"
						:description="
							myRoutes.length || myRequests.length || myResponses.length
								? t('agal.feedReadyDesc')
								: t('agal.noMyDesc')
						"
					/>
				</template>
			</template>
		</div>
	</div>
</template>
