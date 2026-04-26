<script setup lang="ts">
import type { FormError, FormSubmitEvent } from '@nuxt/ui'
import { getLocalTimeZone, today } from '@internationalized/date'
import type { AyanTripCreate, AyanRequestCreate } from '../types/ayan'

import { parsePriceInput, sanitizePriceInput } from '../utils/create-form'
import { getAyanCreateMode } from '../utils/role'

const emit = defineEmits<{ created: [] }>()

const { t } = useI18n()
const toast = useToast()
const { hapticFeedback, isInTelegram } = useTg()
const { user } = useAuth()
const { createTrip } = useAyanTrips()
const { createRequest } = useAyanRequests()

const open = defineModel<boolean>('open', { default: false })

const formType = ref<'trip' | 'request'>('trip')

const state = reactive({
	from_address: '',
	to_address: '',
	date: '',
	time: '',
	seats: 1,
	price: '',
	comment: '',
	description: ''
})

const submitting = ref(false)

const allowedFormType = computed<'trip' | 'request' | null>(() => {
	return getAyanCreateMode(user.value?.role)
})

const typeOptions = computed(() => [
	...(allowedFormType.value !== 'request'
		? [{ label: t('ayan.create.ride'), value: 'trip' as const, icon: 'i-lucide-car' }]
		: []),
	...(allowedFormType.value !== 'trip'
		? [{ label: t('ayan.create.request'), value: 'request' as const, icon: 'i-lucide-map-pin' }]
		: [])
])

watch(
	[allowedFormType, open],
	([type, isOpen]) => {
		if (type) {
			formType.value = type
			return
		}

		if (isOpen) {
			formType.value = 'trip'
		}
	},
	{ immediate: true }
)

function resetForm() {
	Object.assign(state, {
		from_address: '',
		to_address: '',
		date: '',
		time: '',
		seats: 1,
		price: '',
		comment: '',
		description: ''
	})
}

function handleTypeChange(val: string | number) {
	formType.value = val as 'trip' | 'request'
	hapticFeedback('impact')
}

const validate = (s: typeof state): FormError[] => {
	const errors: FormError[] = []
	if (!s.from_address) errors.push({ name: 'from_address', message: t('ayan.validation.required') })
	if (!s.to_address) errors.push({ name: 'to_address', message: t('ayan.validation.required') })
	if (!s.date) errors.push({ name: 'date', message: t('ayan.validation.required') })
	if (s.date && s.date < todayDate.value) errors.push({ name: 'date', message: t('ayan.validation.dateFuture') })
	if (formType.value === 'trip') {
		if (!s.time) errors.push({ name: 'time', message: t('ayan.validation.required') })
		if (s.seats < 1) errors.push({ name: 'seats', message: t('ayan.validation.minSeats') })
		if (parsePriceInput(s.price) < 0) errors.push({ name: 'price', message: t('ayan.validation.minPrice') })
		if (!s.comment.trim()) errors.push({ name: 'comment', message: t('ayan.validation.required') })
	}
	return errors
}

const todayDate = computed(() => today(getLocalTimeZone()).toString())

function handlePriceInput(value: string | number) {
	state.price = sanitizePriceInput(String(value ?? ''))
}

async function onSubmit(_event: FormSubmitEvent<typeof state>) {
	submitting.value = true
	try {
		if (formType.value === 'trip') {
			const payload: AyanTripCreate = {
				from_address: state.from_address,
				to_address: state.to_address,
				date: state.date,
				time: state.time,
				seats: state.seats,
				price: parsePriceInput(state.price),
				comment: state.comment.trim()
			}
			await createTrip(payload)
		} else {
			const payload: AyanRequestCreate = {
				from_address: state.from_address,
				to_address: state.to_address,
				date: state.date,
				time: state.time || undefined,
				description: state.description || undefined
			}
			await createRequest(payload)
		}
		hapticFeedback('notification')
		toast.add({
			title: formType.value === 'trip' ? t('ayan.ride.create') : t('ayan.request.create'),
			description: t('ayan.create.success'),
			color: 'success',
			icon: 'i-lucide-check-circle',
			duration: 3000
		})
		open.value = false
		resetForm()
		emit('created')
	} catch {
		hapticFeedback('impact')
		toast.add({
			title: t('common.error'),
			description: t('ayan.create.error'),
			color: 'error',
			icon: 'i-lucide-x-circle',
			duration: 4000
		})
	} finally {
		submitting.value = false
	}
}
</script>

<template>
	<USlideover
		v-model:open="open"
		:title="formType === 'trip' ? t('ayan.ride.create') : t('ayan.request.create')"
		:description="formType === 'trip' ? t('ayan.ride.createDesc') : t('ayan.request.createDesc')"
		:transition="!isInTelegram"
		:ui="{ content: 'sm:max-w-sm mx-auto max-h-[88dvh] rounded-t-[20px] border border-gray-800 bg-[#111418]' }"
		side="bottom"
	>
		<template #body>
			<div class="create-sheet-body">
				<div v-if="typeOptions.length > 1" class="app-panel app-panel--soft app-form-card create-sheet-tabs">
					<UTabs
						:items="typeOptions"
						:model-value="formType"
						variant="pill"
						size="sm"
						@update:model-value="handleTypeChange"
					/>
				</div>

				<UForm class="tma-no-zoom" :state="state" :validate="validate" @submit="onSubmit">
					<div class="app-panel app-panel--soft app-form-card">
						<div class="create-sheet-grid">
							<UFormField :label="t('ayan.create.from')" name="from_address" required eager-validation>
								<UInput
									v-model="state.from_address"
									fixed
									:placeholder="t('ayan.create.from')"
									icon="i-lucide-circle-dot"
									variant="outline"
									size="lg"
									class="w-full"
								/>
							</UFormField>

							<UFormField :label="t('ayan.create.to')" name="to_address" required eager-validation>
								<UInput
									v-model="state.to_address"
									fixed
									:placeholder="t('ayan.create.to')"
									icon="i-lucide-map-pin"
									variant="outline"
									size="lg"
									class="w-full"
								/>
							</UFormField>

							<div class="grid grid-cols-2 gap-3">
								<UFormField :label="t('ayan.create.date')" name="date" required eager-validation>
									<UInput
										v-model="state.date"
										fixed
										type="date"
										:min="todayDate"
										icon="i-lucide-calendar"
										variant="outline"
										size="lg"
										class="w-full"
									/>
								</UFormField>
								<UFormField :label="t('ayan.create.time')" name="time" :required="formType === 'trip'" eager-validation>
									<UInput
										v-model="state.time"
										fixed
										type="time"
										icon="i-lucide-clock"
										variant="outline"
										size="lg"
										placeholder="--:--"
										class="w-full"
									/>
								</UFormField>
							</div>
						</div>
					</div>

					<div class="app-panel app-panel--soft app-form-card">
						<template v-if="formType === 'trip'">
							<div class="create-sheet-grid">
								<div class="grid grid-cols-2 gap-3">
									<UFormField :label="t('ayan.ride.seatsAvailable')" name="seats" required eager-validation>
										<UInputNumber v-model="state.seats" fixed :min="1" :max="10" size="lg" class="w-full" />
									</UFormField>
									<UFormField :label="t('ayan.ride.price')" name="price" required eager-validation>
										<UInput
											:model-value="state.price"
											inputmode="numeric"
											fixed
											placeholder="0"
											size="lg"
											class="w-full"
											@update:model-value="handlePriceInput"
										>
											<template #trailing>
												<span class="text-sm text-gray-500">₽</span>
											</template>
										</UInput>
									</UFormField>
								</div>

								<UFormField :label="t('ayan.ride.comment')" name="comment" required eager-validation>
									<UTextarea
										v-model="state.comment"
										fixed
										:placeholder="t('ayan.ride.commentPlaceholder')"
										:rows="3"
										autoresize
										class="w-full"
									/>
								</UFormField>
							</div>
						</template>

						<template v-else>
							<UFormField :label="t('ayan.request.comment')" name="description">
								<UTextarea
									v-model="state.description"
									fixed
									:placeholder="t('ayan.request.commentPlaceholder')"
									:rows="3"
									autoresize
									class="w-full"
								/>
							</UFormField>
						</template>
					</div>

					<UButton
						type="submit"
						block
						size="xl"
						color="primary"
						:loading="submitting"
						:label="formType === 'trip' ? t('ayan.ride.create') : t('ayan.request.create')"
					/>
				</UForm>
			</div>
		</template>
	</USlideover>
</template>

<style scoped>
.create-sheet-body {
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.create-sheet-tabs {
	padding: 10px;
}

.create-sheet-grid {
	display: flex;
	flex-direction: column;
	gap: 14px;
}
</style>
