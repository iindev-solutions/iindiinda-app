<script setup lang="ts">
const { t } = useI18n()
const { hapticFeedback } = useTg()

const activeTab = ref('trips')

const tabs = computed(() => [
	{ label: t('ayan.rides'), value: 'trips', icon: 'i-lucide-car' },
	{ label: t('ayan.requests'), value: 'requests', icon: 'i-lucide-map-pin' },
	{ label: t('ayan.myRides'), value: 'my', icon: 'i-lucide-user' }
])

const { data: trips, pending: tripsLoading } = await useAsyncData('ayan-trips', () => useAyanTrips().fetchTrips())

const { data: requests, pending: requestsLoading } = await useAsyncData('ayan-requests', () =>
	useAyanRequests().fetchRequests()
)

const { data: myTrips, pending: myTripsLoading } = await useAsyncData('ayan-my-trips', () => useAyanMy().fetchMyTrips())

const { data: myRequests, pending: myRequestsLoading } = await useAsyncData('ayan-my-requests', () =>
	useAyanMy().fetchMyRequests()
)

const loading = computed(() => {
	if (activeTab.value === 'trips') return tripsLoading.value
	if (activeTab.value === 'requests') return requestsLoading.value
	return myTripsLoading.value || myRequestsLoading.value
})

function handleTabChange(val: string | number) {
	activeTab.value = val as string
	hapticFeedback('impact')
}

function handleCreateTrip() {
	hapticFeedback('impact')
	navigateTo('/ayan/create-trip')
}

function handleCreateRequest() {
	hapticFeedback('impact')
	navigateTo('/ayan/create-request')
}

function handleTripClick(tripId: number) {
	hapticFeedback('impact')
	navigateTo(`/ayan/trip/${tripId}`)
}

function handleRequestClick(requestId: number) {
	hapticFeedback('impact')
	navigateTo(`/ayan/request/${requestId}`)
}
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-6">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
					{{ t('ayan.category') }}
				</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('ayan.name') }}
				</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					{{ t('ayan.desc') }}
				</p>
			</header>

			<UTabs
				:items="tabs"
				:model-value="activeTab"
				variant="pill"
				size="sm"
				class="mb-6"
				@update:model-value="handleTabChange"
			/>

			<div class="mb-4 flex gap-2">
				<UButton
					:label="t('ayan.createRide')"
					icon="i-lucide-plus"
					size="sm"
					variant="soft"
					color="primary"
					class="flex-1"
					@click="handleCreateTrip"
				/>
				<UButton
					:label="t('ayan.createRequest')"
					icon="i-lucide-plus"
					size="sm"
					variant="outline"
					color="neutral"
					class="flex-1"
					@click="handleCreateRequest"
				/>
			</div>

			<div v-if="loading" class="flex justify-center py-12">
				<LoadingSpinner />
			</div>

			<template v-else-if="activeTab === 'trips'">
				<div v-if="!trips?.length">
					<EmptyState :title="t('ayan.noRides')" :description="t('ayan.noRidesDesc')" />
				</div>
				<div v-else class="space-y-3">
					<UCard
						v-for="trip in trips"
						:key="trip.id"
						variant="outline"
						class="cursor-pointer transition-colors hover:border-cyan-500/30"
						@click="handleTripClick(trip.id)"
					>
						<div class="flex items-start justify-between gap-3">
							<div class="min-w-0 flex-1">
								<div class="mb-1 flex items-center gap-2 text-sm font-medium text-[#eff3f5]">
									<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
									<span class="truncate">{{ trip.from_address }}</span>
									<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
									<span class="truncate">{{ trip.to_address }}</span>
								</div>
								<div class="mt-1 flex items-center gap-3 text-xs text-gray-400">
									<span>{{ trip.date }}</span>
									<span v-if="trip.time">{{ trip.time }}</span>
									<span>{{ trip.seats }} {{ t('ayan.ride.seats') }}</span>
								</div>
								<div class="mt-1 text-xs text-gray-500">
									{{ trip.driver.name }}
								</div>
							</div>
							<div class="shrink-0 text-right">
								<div class="text-sm font-semibold text-cyan-400">{{ formatPrice(trip.price) }} ₽</div>
							</div>
						</div>
					</UCard>
				</div>
			</template>

			<template v-else-if="activeTab === 'requests'">
				<div v-if="!requests?.length">
					<EmptyState :title="t('ayan.noRequests')" :description="t('ayan.noRequestsDesc')" />
				</div>
				<div v-else class="space-y-3">
					<UCard
						v-for="request in requests"
						:key="request.id"
						variant="outline"
						class="cursor-pointer transition-colors hover:border-cyan-500/30"
						@click="handleRequestClick(request.id)"
					>
						<div class="min-w-0">
							<div class="mb-1 flex items-center gap-2 text-sm font-medium text-[#eff3f5]">
								<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
								<span class="truncate">{{ request.from_address }}</span>
								<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
								<span class="truncate">{{ request.to_address }}</span>
							</div>
							<div class="mt-1 flex items-center gap-3 text-xs text-gray-400">
								<span>{{ request.date }}</span>
								<span v-if="request.time">{{ request.time }}</span>
							</div>
							<div class="mt-1 text-xs text-gray-500">
								{{ request.passenger.name }}
							</div>
							<div v-if="request.description" class="mt-1 text-xs text-gray-400">
								{{ request.description }}
							</div>
						</div>
					</UCard>
				</div>
			</template>

			<template v-else>
				<div v-if="!myTrips?.length && !myRequests?.length">
					<EmptyState :title="t('ayan.noRides')" :description="t('ayan.noRidesDesc')" />
				</div>
				<div v-else class="space-y-3">
					<UCard
						v-for="trip in myTrips"
						:key="'my-' + trip.id"
						variant="outline"
						class="cursor-pointer transition-colors hover:border-cyan-500/30"
						@click="handleTripClick(trip.id)"
					>
						<div class="flex items-start justify-between gap-3">
							<div class="min-w-0 flex-1">
								<div class="mb-1 flex items-center gap-2 text-sm font-medium text-[#eff3f5]">
									<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
									<span class="truncate">{{ trip.from_address }}</span>
									<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
									<span class="truncate">{{ trip.to_address }}</span>
								</div>
								<div class="mt-1 flex items-center gap-3 text-xs text-gray-400">
									<span>{{ trip.date }}</span>
									<span v-if="trip.time">{{ trip.time }}</span>
									<span>{{ trip.seats }} {{ t('ayan.ride.seats') }}</span>
								</div>
							</div>
							<div class="shrink-0 text-right">
								<div class="text-sm font-semibold text-cyan-400">{{ formatPrice(trip.price) }} ₽</div>
							</div>
						</div>
					</UCard>
					<UCard
						v-for="req in myRequests"
						:key="'my-req-' + req.id"
						variant="outline"
						class="cursor-pointer transition-colors hover:border-cyan-500/30"
						@click="handleRequestClick(req.id)"
					>
						<div class="min-w-0">
							<div class="mb-1 flex items-center gap-2 text-sm font-medium text-[#eff3f5]">
								<UIcon name="i-lucide-map-pin" class="shrink-0 text-cyan-400" />
								<span class="truncate">{{ req.from_address }}</span>
								<UIcon name="i-lucide-arrow-right" class="shrink-0 text-gray-500" />
								<span class="truncate">{{ req.to_address }}</span>
							</div>
							<div class="mt-1 flex items-center gap-3 text-xs text-gray-400">
								<span>{{ req.date }}</span>
								<span v-if="req.time">{{ req.time }}</span>
							</div>
						</div>
					</UCard>
				</div>
			</template>
		</div>
	</div>
</template>
