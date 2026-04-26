<script setup lang="ts">
import type { FormError, FormSubmitEvent } from '@nuxt/ui'
import { getLocalTimeZone, today } from '@internationalized/date'
import type { AgalRequestCreate, AgalRouteCreate } from '../types/agal'

import { getAgalCreateMode } from '../utils/role'

const emit = defineEmits<{ created: [] }>()

const { t } = useI18n()
const toast = useToast()
const { hapticFeedback, isInTelegram } = useTg()
const { user } = useAuth()
const { createRoute } = useAgalRoutes()
const { createRequest } = useAgalRequests()

const open = defineModel<boolean>('open', { default: false })

const state = reactive({
	from_address: '',
	to_address: '',
	date: '',
	time: '',
	size_label: 'small' as 'document' | 'small' | 'medium' | 'large',
	weight_kg_max: '',
	accepted_items: '',
	restricted_items: '',
	price: '',
	notes: '',
	weight_kg: '',
	contents_summary: '',
	fragility: 'normal' as 'normal' | 'fragile',
	documents_required: false,
	budget: ''
})

const submitting = ref(false)

const createMode = computed<'route' | 'request' | null>(() => getAgalCreateMode(user.value?.role))

const sizeOptions = computed(() => [
	{ label: t('agal.size.document'), value: 'document' as const },
	{ label: t('agal.size.small'), value: 'small' as const },
	{ label: t('agal.size.medium'), value: 'medium' as const },
	{ label: t('agal.size.large'), value: 'large' as const }
])

const fragilityOptions = computed(() => [
	{ label: t('agal.fragility.normal'), value: 'normal' as const },
	{ label: t('agal.fragility.fragile'), value: 'fragile' as const }
])

const todayDate = computed(() => today(getLocalTimeZone()).toString())

watch(
	createMode,
	(mode) => {
		if (mode === 'request' && state.size_label === 'small') {
			state.size_label = 'document'
		}
		if (mode === 'route' && state.size_label === 'document') {
			state.size_label = 'small'
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
		size_label: createMode.value === 'request' ? 'document' : 'small',
		weight_kg_max: '',
		accepted_items: '',
		restricted_items: '',
		price: '',
		notes: '',
		weight_kg: '',
		contents_summary: '',
		fragility: 'normal',
		documents_required: false,
		budget: ''
	})
}

function trimToNull(value: string) {
	const trimmed = value.trim()
	return trimmed ? trimmed : null
}

function sanitizeIntegerInput(value: string) {
	return value.replace(/[^\d]/g, '')
}

function sanitizeDecimalInput(value: string) {
	const normalized = value.replace(',', '.').replace(/[^\d.]/g, '')
	const [whole = '', ...rest] = normalized.split('.')
	const fractional = rest.join('')
	return fractional ? `${whole}.${fractional}` : whole
}

function parseIntegerInput(value: string) {
	if (!value) return null
	const parsed = Number.parseInt(value, 10)
	return Number.isFinite(parsed) ? parsed : null
}

function parseDecimalInput(value: string) {
	if (!value) return null
	const parsed = Number.parseFloat(value)
	return Number.isFinite(parsed) ? parsed : null
}

function handleIntegerInput(field: 'price' | 'budget', value: string | number) {
	state[field] = sanitizeIntegerInput(String(value ?? ''))
}

function handleDecimalInput(field: 'weight_kg_max' | 'weight_kg', value: string | number) {
	state[field] = sanitizeDecimalInput(String(value ?? ''))
}

const validate = (s: typeof state): FormError[] => {
	const errors: FormError[] = []

	if (!s.from_address.trim()) errors.push({ name: 'from_address', message: t('agal.validation.required') })
	if (!s.to_address.trim()) errors.push({ name: 'to_address', message: t('agal.validation.required') })
	if (!s.date) errors.push({ name: 'date', message: t('agal.validation.required') })
	if (s.date && s.date < todayDate.value) errors.push({ name: 'date', message: t('agal.validation.dateFuture') })

	if (createMode.value === 'route') {
		if (parseDecimalInput(s.weight_kg_max) !== null && (parseDecimalInput(s.weight_kg_max) ?? 0) <= 0) {
			errors.push({ name: 'weight_kg_max', message: t('agal.validation.positiveWeight') })
		}
		if (parseIntegerInput(s.price) !== null && (parseIntegerInput(s.price) ?? 0) < 0) {
			errors.push({ name: 'price', message: t('agal.validation.minPrice') })
		}
	}

	if (createMode.value === 'request') {
		if (!s.contents_summary.trim()) {
			errors.push({ name: 'contents_summary', message: t('agal.validation.required') })
		}
		if (parseDecimalInput(s.weight_kg) !== null && (parseDecimalInput(s.weight_kg) ?? 0) <= 0) {
			errors.push({ name: 'weight_kg', message: t('agal.validation.positiveWeight') })
		}
		if (parseIntegerInput(s.budget) !== null && (parseIntegerInput(s.budget) ?? 0) < 0) {
			errors.push({ name: 'budget', message: t('agal.validation.minPrice') })
		}
	}

	return errors
}

async function onSubmit(_event: FormSubmitEvent<typeof state>) {
	if (!createMode.value) return

	submitting.value = true
	try {
		if (createMode.value === 'route') {
			const payload: AgalRouteCreate = {
				from_address: state.from_address.trim(),
				to_address: state.to_address.trim(),
				date: state.date,
				time: trimToNull(state.time),
				size_label: state.size_label,
				weight_kg_max: parseDecimalInput(state.weight_kg_max),
				accepted_items: trimToNull(state.accepted_items),
				restricted_items: trimToNull(state.restricted_items),
				price: parseIntegerInput(state.price),
				notes: trimToNull(state.notes)
			}
			await createRoute(payload)
		} else {
			const payload: AgalRequestCreate = {
				from_address: state.from_address.trim(),
				to_address: state.to_address.trim(),
				date: state.date,
				time: trimToNull(state.time),
				size_label: state.size_label,
				weight_kg: parseDecimalInput(state.weight_kg),
				contents_summary: state.contents_summary.trim(),
				fragility: state.fragility,
				documents_required: state.documents_required,
				budget: parseIntegerInput(state.budget),
				notes: trimToNull(state.notes)
			}
			await createRequest(payload)
		}

		hapticFeedback('notification')
		toast.add({
			title: createMode.value === 'route' ? t('agal.route.create') : t('agal.request.create'),
			description: t('agal.create.success'),
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
			description: t('agal.create.error'),
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
		:title="createMode === 'route' ? t('agal.route.create') : t('agal.request.create')"
		:description="createMode === 'route' ? t('agal.route.createDesc') : t('agal.request.createDesc')"
		:transition="!isInTelegram"
		:ui="{ content: 'sm:max-w-sm mx-auto max-h-[85dvh] rounded-t-2xl' }"
		side="bottom"
	>
		<template #body>
			<UForm class="tma-no-zoom" :state="state" :validate="validate" @submit="onSubmit">
				<div class="space-y-4">
					<UFormField :label="t('agal.create.from')" name="from_address" required eager-validation>
						<UInput
							v-model="state.from_address"
							fixed
							:placeholder="t('agal.create.from')"
							icon="i-lucide-circle-dot"
							variant="outline"
							size="lg"
							class="w-full"
						/>
					</UFormField>

					<UFormField :label="t('agal.create.to')" name="to_address" required eager-validation>
						<UInput
							v-model="state.to_address"
							fixed
							:placeholder="t('agal.create.to')"
							icon="i-lucide-map-pin"
							variant="outline"
							size="lg"
							class="w-full"
						/>
					</UFormField>

					<div class="grid grid-cols-2 gap-3">
						<UFormField :label="t('agal.create.date')" name="date" required eager-validation>
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
						<UFormField :label="t('agal.create.time')" name="time">
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

					<UFormField :label="t('agal.create.size')" name="size_label" required eager-validation>
						<USelect v-model="state.size_label" :items="sizeOptions" size="lg" class="w-full" />
					</UFormField>

					<template v-if="createMode === 'route'">
						<div class="grid grid-cols-2 gap-3">
							<UFormField :label="t('agal.route.weightMax')" name="weight_kg_max" eager-validation>
								<UInput
									:model-value="state.weight_kg_max"
									fixed
									inputmode="decimal"
									placeholder="0"
									size="lg"
									class="w-full"
									@update:model-value="handleDecimalInput('weight_kg_max', $event)"
								>
									<template #trailing>
										<span class="text-sm text-gray-500">кг</span>
									</template>
								</UInput>
							</UFormField>
							<UFormField :label="t('agal.route.price')" name="price" eager-validation>
								<UInput
									:model-value="state.price"
									fixed
									inputmode="numeric"
									placeholder="0"
									size="lg"
									class="w-full"
									@update:model-value="handleIntegerInput('price', $event)"
								>
									<template #trailing>
										<span class="text-sm text-gray-500">₽</span>
									</template>
								</UInput>
							</UFormField>
						</div>

						<UFormField :label="t('agal.route.acceptedItems')" name="accepted_items">
							<UTextarea
								v-model="state.accepted_items"
								fixed
								:placeholder="t('agal.route.acceptedItemsPlaceholder')"
								:rows="2"
								autoresize
								class="w-full"
							/>
						</UFormField>

						<UFormField :label="t('agal.route.restrictedItems')" name="restricted_items">
							<UTextarea
								v-model="state.restricted_items"
								fixed
								:placeholder="t('agal.route.restrictedItemsPlaceholder')"
								:rows="2"
								autoresize
								class="w-full"
							/>
						</UFormField>
					</template>

					<template v-else>
						<div class="grid grid-cols-2 gap-3">
							<UFormField :label="t('agal.request.weight')" name="weight_kg" eager-validation>
								<UInput
									:model-value="state.weight_kg"
									fixed
									inputmode="decimal"
									placeholder="0"
									size="lg"
									class="w-full"
									@update:model-value="handleDecimalInput('weight_kg', $event)"
								>
									<template #trailing>
										<span class="text-sm text-gray-500">кг</span>
									</template>
								</UInput>
							</UFormField>
							<UFormField :label="t('agal.request.budget')" name="budget" eager-validation>
								<UInput
									:model-value="state.budget"
									fixed
									inputmode="numeric"
									placeholder="0"
									size="lg"
									class="w-full"
									@update:model-value="handleIntegerInput('budget', $event)"
								>
									<template #trailing>
										<span class="text-sm text-gray-500">₽</span>
									</template>
								</UInput>
							</UFormField>
						</div>

						<UFormField :label="t('agal.request.contentsSummary')" name="contents_summary" required eager-validation>
							<UTextarea
								v-model="state.contents_summary"
								fixed
								:placeholder="t('agal.request.contentsSummaryPlaceholder')"
								:rows="2"
								autoresize
								class="w-full"
							/>
						</UFormField>

						<div class="grid grid-cols-2 gap-3">
							<UFormField :label="t('agal.request.fragilityLabel')" name="fragility">
								<USelect v-model="state.fragility" :items="fragilityOptions" size="lg" class="w-full" />
							</UFormField>
							<UFormField :label="t('agal.request.documentsRequired')" name="documents_required">
								<div class="flex min-h-11 items-center justify-between rounded-xl border border-gray-800 px-3">
									<span class="text-sm text-cyan-50">{{ t('agal.request.documentsRequiredSwitch') }}</span>
									<USwitch v-model="state.documents_required" />
								</div>
							</UFormField>
						</div>
					</template>

					<UFormField :label="t('agal.notes')" name="notes">
						<UTextarea
							v-model="state.notes"
							fixed
							:placeholder="t('agal.notesPlaceholder')"
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
						:label="createMode === 'route' ? t('agal.route.create') : t('agal.request.create')"
					/>
				</div>
			</UForm>
		</template>
	</USlideover>
</template>
