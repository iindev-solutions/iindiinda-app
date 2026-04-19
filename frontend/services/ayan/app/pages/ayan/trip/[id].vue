<script setup lang="ts">
import type { AyanResponse } from '../../../types/ayan'

definePageMeta({ lazy: true })

const route = useRoute()
const { t } = useI18n()
const toast = useToast()
const { hapticFeedback } = useTg()
const { user: authUser } = useAuth()
const { fetchTrip } = useAyanTrips()
const { fetchTripResponses, createTripResponse, updateResponseStatus } = useAyanResponses()

const tripId = computed(() => Number(route.params.id))

const { data: trip, pending: loading } = useLazyAsyncData(`ayan-trip-${tripId.value}`, () => fetchTrip(tripId.value))

const responses = ref<AyanResponse[]>([])
const responding = ref(false)
const responseMessage = ref('')

const isOwner = computed(() => {
	if (!trip.value || !authUser.value) return false
	return trip.value.driver.id === authUser.value.id
})

const hasAcceptedResponse = computed(() => responses.value.some((r) => r.status === 'accepted'))

const statusColor = (status: AyanResponse['status']) => {
	if (status === 'accepted') return 'success'
	if (status === 'rejected') return 'error'
	return 'neutral'
}

async function loadResponses() {
	responses.value = await fetchTripResponses(tripId.value)
}

async function handleRespond() {
	responding.value = true
	try {
		await createTripResponse(tripId.value, { message: responseMessage.value || undefined })
		hapticFeedback('notification')
		toast.add({ title: t('ayan.respond.success'), color: 'success', icon: 'i-lucide-check-circle', duration: 3000 })
		await loadResponses()
		responseMessage.value = ''
	} catch {
		hapticFeedback('impact')
		toast.add({ title: t('common.error'), color: 'error', icon: 'i-lucide-x-circle', duration: 4000 })
	} finally {
		responding.value = false
	}
}

async function handleAccept(r: AyanResponse) {
	try {
		await updateResponseStatus(r.id, 'accepted')
		hapticFeedback('notification')
		await loadResponses()
	} catch {
		hapticFeedback('impact')
		toast.add({ title: t('common.error'), color: 'error', icon: 'i-lucide-x-circle', duration: 4000 })
	}
}

async function handleReject(r: AyanResponse) {
	try {
		await updateResponseStatus(r.id, 'rejected')
		hapticFeedback('notification')
		await loadResponses()
	} catch {
		hapticFeedback('impact')
		toast.add({ title: t('common.error'), color: 'error', icon: 'i-lucide-x-circle', duration: 4000 })
	}
}

loadResponses()
</script>

<template>
	<div class="px-4 py-6">
		<div class="mx-auto max-w-[480px]">
			<BackButton />

			<div v-if="loading" class="flex justify-center py-12">
				<LoadingSpinner />
			</div>

			<template v-else-if="trip">
				<header class="mb-6">
					<h1 class="mb-1 text-xl font-medium tracking-tight text-cyan-50">
						{{ trip.from_address }} → {{ trip.to_address }}
					</h1>
					<div class="flex items-center gap-3 text-sm text-gray-400">
						<span>{{ trip.date }}</span>
						<span v-if="trip.time">{{ trip.time }}</span>
						<UBadge :color="trip.status === 'open' ? 'success' : 'neutral'" variant="subtle" size="xs">
							{{ t(`ayan.status.${trip.status}`) }}
						</UBadge>
					</div>
				</header>

				<UCard variant="outline" class="mb-6">
					<div class="space-y-3">
						<div class="flex items-center justify-between">
							<span class="text-sm text-gray-400">{{ t('ayan.ride.price') }}</span>
							<span class="text-sm font-semibold text-cyan-400">{{ formatPrice(trip.price) }}</span>
						</div>
						<div class="flex items-center justify-between">
							<span class="text-sm text-gray-400">{{ t('ayan.ride.seatsAvailable') }}</span>
							<span class="text-sm text-cyan-50">{{ trip.seats }}</span>
						</div>
						<div class="flex items-center justify-between">
							<span class="text-sm text-gray-400">{{ t('ayan.driver') }}</span>
							<span class="text-sm text-cyan-50">{{ trip.driver.name }}</span>
						</div>
						<div v-if="trip.comment" class="border-t border-gray-800 pt-3">
							<span class="text-sm text-gray-400">{{ t('ayan.ride.comment') }}</span>
							<p class="mt-1 text-sm text-cyan-50">{{ trip.comment }}</p>
						</div>
					</div>
				</UCard>

				<template v-if="!isOwner && trip.status === 'open'">
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
								class="w-full"
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
				</template>

				<div v-if="responses.length > 0">
					<h2 class="mb-3 text-sm font-medium text-gray-400">{{ t('ayan.responses') }}</h2>
					<div class="space-y-2">
						<UCard v-for="r in responses" :key="r.id" variant="subtle">
							<div class="flex items-start justify-between gap-3">
								<div class="min-w-0 flex-1">
									<div class="flex items-center gap-2">
										<span class="text-sm text-cyan-50">{{ r.user.name }}</span>
										<UBadge :color="statusColor(r.status)" variant="subtle" size="xs">
											{{ t(`ayan.respond.status.${r.status}`) }}
										</UBadge>
									</div>
									<div v-if="r.message" class="mt-1 text-xs text-gray-400">{{ r.message }}</div>
									<div v-if="r.status === 'accepted' && r.user.username" class="mt-2">
										<a
											:href="`https://t.me/${r.user.username.replace('@', '')}`"
											target="_blank"
											class="inline-flex items-center gap-1 text-xs text-cyan-400 hover:text-cyan-300"
										>
											<UIcon name="i-lucide-send" class="size-3" />
											{{ r.user.username }}
										</a>
									</div>
								</div>
								<div
									v-if="isOwner && r.status === 'pending' && !hasAcceptedResponse"
									class="flex shrink-0 gap-1"
								>
									<UButton
										size="xs"
										color="success"
										variant="soft"
										icon="i-lucide-check"
										@click="handleAccept(r)"
									/>
									<UButton
										size="xs"
										color="error"
										variant="soft"
										icon="i-lucide-x"
										@click="handleReject(r)"
									/>
								</div>
							</div>
						</UCard>
					</div>
				</div>
			</template>

			<EmptyState v-else :title="t('common.error')" />
		</div>
	</div>
</template>
