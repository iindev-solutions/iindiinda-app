<script setup lang="ts">
import type { FormError, FormSubmitEvent } from '@nuxt/ui'
import type { CalendarDate } from '@internationalized/date'
import { getLocalTimeZone, parseDate, today } from '@internationalized/date'
import type { AyanTripCreate, AyanRequestCreate } from '../types/ayan'

import { parsePriceInput, sanitizePriceInput } from '../utils/create-form'
import { getAyanCreateMode } from '../utils/role'

const emit = defineEmits<{ created: [] }>()

const { t } = useI18n()
const toast = useToast()
const { hapticFeedback } = useTg()
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
const datePickerOpen = ref(false)

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
	}
	return errors
}

const todayDate = computed(() => today(getLocalTimeZone()).toString())
const minCalendarDate = computed(() => parseDate(todayDate.value))

const calendarValue = computed<CalendarDate | undefined>({
	get() {
		return state.date ? parseDate(state.date) : undefined
	},
	set(value) {
		state.date = value ? value.toString() : ''
		datePickerOpen.value = false
	}
})

const displayDate = computed(() => state.date || t('ayan.create.datePlaceholder'))

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
				comment: state.comment || undefined
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
		:ui="{ content: 'sm:max-w-sm mx-auto max-h-[85dvh] rounded-t-2xl' }"
		side="bottom"
	>
		<template #body>
			<UTabs
				v-if="typeOptions.length > 1"
				:items="typeOptions"
				:model-value="formType"
				variant="pill"
				size="sm"
				class="mb-5"
				@update:model-value="handleTypeChange"
			/>

			<UForm class="tma-no-zoom" :state="state" :validate="validate" @submit="onSubmit">
				<div class="space-y-4">
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
							<UPopover v-model:open="datePickerOpen">
								<UButton
									color="neutral"
									variant="outline"
									size="lg"
									icon="i-lucide-calendar"
									class="tma-no-zoom-button w-full justify-between"
								>
									<span class="truncate">{{ displayDate }}</span>
								</UButton>
								<template #content>
									<UCalendar v-model="calendarValue" :min-value="minCalendarDate" class="tma-no-zoom-calendar" />
								</template>
							</UPopover>
						</UFormField>
						<UFormField
							:label="t('ayan.create.time')"
							name="time"
							:required="formType === 'trip'"
							eager-validation
						>
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

					<template v-if="formType === 'trip'">
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

						<UFormField :label="t('ayan.ride.comment')" name="comment">
							<UTextarea
								v-model="state.comment"
								fixed
								:placeholder="t('ayan.ride.commentPlaceholder')"
								:rows="3"
								autoresize
								class="w-full"
							/>
						</UFormField>
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

					<UButton
						type="submit"
						block
						size="xl"
						color="primary"
						:loading="submitting"
						:label="formType === 'trip' ? t('ayan.ride.create') : t('ayan.request.create')"
					/>
				</div>
			</UForm>
		</template>
	</USlideover>
</template>
