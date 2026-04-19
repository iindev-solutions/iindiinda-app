<script setup lang="ts">
import type { AyanTripCreate } from '../../types/ayan'

const { t } = useI18n()
const { hapticFeedback } = useTg()
const { createTrip } = useAyanTrips()

const submitting = ref(false)

const state = reactive<AyanTripCreate>({
	from_address: '',
	to_address: '',
	date: '',
	time: '',
	seats: 1,
	price: 0,
	comment: ''
})

const validate = (s: Partial<AyanTripCreate>) => {
	const errors: { name: string; message: string }[] = []
	if (!s.from_address) errors.push({ name: 'from_address', message: t('ayan.validation.required') })
	if (!s.to_address) errors.push({ name: 'to_address', message: t('ayan.validation.required') })
	if (!s.date) errors.push({ name: 'date', message: t('ayan.validation.required') })
	if (!s.time) errors.push({ name: 'time', message: t('ayan.validation.required') })
	if (s.seats !== undefined && s.seats < 1) errors.push({ name: 'seats', message: t('ayan.validation.minSeats') })
	if (s.price !== undefined && s.price < 0) errors.push({ name: 'price', message: t('ayan.validation.minPrice') })
	return errors
}

async function onSubmit() {
	submitting.value = true
	try {
		await createTrip(state)
		hapticFeedback('notification')
		navigateTo('/ayan')
	} catch {
		hapticFeedback('impact')
	} finally {
		submitting.value = false
	}
}
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-20">
		<div class="mx-auto max-w-[480px]">
			<BackButton />

			<header class="mb-6">
				<h1 class="mb-1 text-xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('ayan.ride.create') }}
				</h1>
				<p class="text-sm text-gray-400">
					{{ t('ayan.ride.createDesc') }}
				</p>
			</header>

			<UForm :state="state" :validate="validate" @submit="onSubmit">
				<div class="space-y-4">
					<UFormField :label="t('ayan.ride.from')" name="from_address" required>
						<UInput
							v-model="state.from_address"
							:placeholder="t('ayan.ride.from')"
							icon="i-lucide-map-pin"
						/>
					</UFormField>

					<UFormField :label="t('ayan.ride.to')" name="to_address" required>
						<UInput v-model="state.to_address" :placeholder="t('ayan.ride.to')" icon="i-lucide-map-pin" />
					</UFormField>

					<UFormField :label="t('ayan.ride.date')" name="date" required>
						<UInput v-model="state.date" type="date" icon="i-lucide-calendar" />
					</UFormField>

					<UFormField :label="t('ayan.ride.time')" name="time" required>
						<UInput v-model="state.time" type="time" icon="i-lucide-clock" />
					</UFormField>

					<UFormField :label="t('ayan.ride.seatsAvailable')" name="seats" required>
						<UInputNumber v-model="state.seats" :min="1" :max="10" />
					</UFormField>

					<UFormField :label="t('ayan.ride.price')" name="price" required>
						<UInputNumber v-model="state.price" :min="0" />
					</UFormField>

					<UFormField :label="t('ayan.ride.comment')" name="comment">
						<UTextarea v-model="state.comment" :placeholder="t('ayan.ride.comment')" :rows="2" autoresize />
					</UFormField>

					<UButton
						type="submit"
						block
						size="lg"
						color="primary"
						:loading="submitting"
						:label="t('ayan.ride.create')"
					/>
				</div>
			</UForm>
		</div>
	</div>
</template>
