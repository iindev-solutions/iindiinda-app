<script setup lang="ts">
import type { FormError, FormSubmitEvent } from '@nuxt/ui'
import type { AyanTripCreate } from '../../types/ayan'

const { t } = useI18n()
const { hapticFeedback } = useTg()
const { createTrip } = useAyanTrips()

const state = reactive<AyanTripCreate>({
	from_address: '',
	to_address: '',
	date: '',
	time: '',
	seats: 1,
	price: 0,
	comment: ''
})

const validate = (s: Partial<AyanTripCreate>): FormError[] => {
	const errors: FormError[] = []
	if (!s.from_address) errors.push({ name: 'from_address', message: t('ayan.validation.required') })
	if (!s.to_address) errors.push({ name: 'to_address', message: t('ayan.validation.required') })
	if (!s.date) errors.push({ name: 'date', message: t('ayan.validation.required') })
	if (!s.time) errors.push({ name: 'time', message: t('ayan.validation.required') })
	if (s.seats !== undefined && s.seats < 1) errors.push({ name: 'seats', message: t('ayan.validation.minSeats') })
	if (s.price !== undefined && s.price < 0) errors.push({ name: 'price', message: t('ayan.validation.minPrice') })
	return errors
}

const submitting = ref(false)

async function onSubmit(_event: FormSubmitEvent<AyanTripCreate>) {
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
	<div class="min-h-screen px-4 py-6">
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
					<UFormField :label="t('ayan.ride.from')" name="from_address" required eager-validation>
						<UInput
							v-model="state.from_address"
							:placeholder="t('ayan.ride.from')"
							icon="i-lucide-circle-dot"
							variant="outline"
							size="lg"
							class="w-full"
						/>
					</UFormField>

					<UFormField :label="t('ayan.ride.to')" name="to_address" required eager-validation>
						<UInput
							v-model="state.to_address"
							:placeholder="t('ayan.ride.to')"
							icon="i-lucide-map-pin"
							variant="outline"
							size="lg"
							class="w-full"
						/>
					</UFormField>

					<div class="grid grid-cols-2 gap-3">
						<UFormField :label="t('ayan.ride.date')" name="date" required eager-validation>
							<UInput
								v-model="state.date"
								type="date"
								icon="i-lucide-calendar"
								variant="outline"
								size="lg"
								class="w-full"
							/>
						</UFormField>
						<UFormField :label="t('ayan.ride.time')" name="time" required eager-validation>
							<UInput
								v-model="state.time"
								type="time"
								icon="i-lucide-clock"
								variant="outline"
								size="lg"
								placeholder="--:--"
								class="w-full"
							/>
						</UFormField>
					</div>

					<div class="grid grid-cols-2 gap-3">
						<UFormField :label="t('ayan.ride.seatsAvailable')" name="seats" required eager-validation>
							<UInputNumber v-model="state.seats" :min="1" :max="10" size="lg" class="w-full" />
						</UFormField>
						<UFormField :label="t('ayan.ride.price')" name="price" required eager-validation>
							<UInput
								v-model="state.price"
								type="number"
								inputmode="numeric"
								placeholder="0"
								size="lg"
								class="w-full"
							>
								<template #trailing>
									<span class="text-sm text-gray-400">₽</span>
								</template>
							</UInput>
						</UFormField>
					</div>

					<UFormField :label="t('ayan.ride.comment')" name="comment">
						<UTextarea
							v-model="state.comment"
							:placeholder="t('ayan.ride.commentPlaceholder')"
							:rows="3"
							autoresize
							class="w-full"
						/>
					</UFormField>

					<UButton
						type="submit"
						block
						size="xl"
						color="primary"
						:loading="submitting"
						:label="t('ayan.ride.create')"
					/>
				</div>
			</UForm>
		</div>
	</div>
</template>
