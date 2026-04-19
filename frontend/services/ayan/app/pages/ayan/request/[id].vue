<script setup lang="ts">
import type { AyanResponse } from '../../../types/ayan'

const route = useRoute()
const { t } = useI18n()
const { hapticFeedback } = useTg()
const { fetchRequest } = useAyanRequests()
const { fetchRequestResponses, createRequestResponse } = useAyanResponses()

const requestId = computed(() => Number(route.params.id))

const { data: request, pending: loading } = await useAsyncData(`ayan-request-${requestId.value}`, () =>
	fetchRequest(requestId.value)
)

const responses = ref<AyanResponse[]>([])
const responding = ref(false)
const responseMessage = ref('')

async function loadResponses() {
	responses.value = await fetchRequestResponses(requestId.value)
}

async function handleRespond() {
	responding.value = true
	try {
		await createRequestResponse(requestId.value, { message: responseMessage.value || undefined })
		hapticFeedback('notification')
		await loadResponses()
		responseMessage.value = ''
	} catch {
		hapticFeedback('impact')
	} finally {
		responding.value = false
	}
}

await loadResponses()
</script>

<template>
	<div class="min-h-screen px-4 py-6">
		<div class="mx-auto max-w-[480px]">
			<BackButton />

			<div v-if="loading" class="flex justify-center py-12">
				<LoadingSpinner />
			</div>

			<template v-else-if="request">
				<header class="mb-6">
					<h1 class="mb-1 text-xl font-medium tracking-tight text-[#eff3f5]">
						{{ request.from_address }} → {{ request.to_address }}
					</h1>
					<div class="flex items-center gap-3 text-sm text-gray-400">
						<span>{{ request.date }}</span>
						<span v-if="request.time">{{ request.time }}</span>
					</div>
				</header>

				<UCard variant="outline" class="mb-6">
					<div class="space-y-3">
						<div class="flex items-center justify-between">
							<span class="text-sm text-gray-400">{{ t('ayan.passenger') }}</span>
							<span class="text-sm text-[#eff3f5]">{{ request.passenger.name }}</span>
						</div>
						<div v-if="request.description" class="border-t border-gray-800 pt-3">
							<span class="text-sm text-gray-400">{{ t('ayan.request.comment') }}</span>
							<p class="mt-1 text-sm text-[#eff3f5]">{{ request.description }}</p>
						</div>
					</div>
				</UCard>

				<div class="mb-6">
					<h2 class="mb-3 text-sm font-medium text-gray-400">
						{{ t('ayan.respond.button') }}
					</h2>
					<div class="space-y-3">
						<UTextarea
							v-model="responseMessage"
							:placeholder="t('ayan.respond.messagePlaceholder')"
							:rows="2"
							autoresize
						/>
						<UButton
							block
							color="primary"
							:loading="responding"
							:label="t('ayan.respond.button')"
							icon="i-lucide-send"
							@click="handleRespond"
						/>
					</div>
				</div>

				<div v-if="responses.length > 0">
					<h2 class="mb-3 text-sm font-medium text-gray-400">{{ t('ayan.responses') }}</h2>
					<div class="space-y-2">
						<UCard v-for="r in responses" :key="r.id" variant="subtle">
							<div class="text-sm text-[#eff3f5]">{{ r.user.name }}</div>
							<div v-if="r.message" class="mt-1 text-xs text-gray-400">{{ r.message }}</div>
						</UCard>
					</div>
				</div>
			</template>

			<EmptyState v-else :title="t('common.error')" />
		</div>
	</div>
</template>
