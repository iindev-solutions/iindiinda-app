<script setup lang="ts">
import type { FormError, FormSubmitEvent } from '@nuxt/ui'
import type { TalAvailabilityStatus, TalCategory, TalMasterCreate } from '../types/tal'

const emit = defineEmits<{ created: [] }>()

const { t } = useI18n()
const toast = useToast()
const { hapticFeedback } = useTg()
const { createMaster } = useTalMasters()

const open = defineModel<boolean>('open', { default: false })

const state = reactive({
	category: 'beauty' as TalCategory,
	service_label: '',
	description: '',
	location: '',
	availability_status: 'now' as TalAvailabilityStatus,
	available_note: '',
	price_from: ''
})

const submitting = ref(false)

const categoryOptions = computed(() => [
	{ label: t('tal.category.beauty'), value: 'beauty' as const },
	{ label: t('tal.category.home'), value: 'home' as const },
	{ label: t('tal.category.repair'), value: 'repair' as const }
])

const availabilityOptions = computed(() => [
	{ label: t('tal.availability.now'), value: 'now' as const },
	{ label: t('tal.availability.later'), value: 'later' as const },
	{ label: t('tal.availability.tomorrow'), value: 'tomorrow' as const },
	{ label: t('tal.availability.busy'), value: 'busy' as const }
])

const validate = (s: typeof state): FormError[] => {
	const errors: FormError[] = []

	if (!s.service_label.trim()) errors.push({ name: 'service_label', message: t('tal.validation.required') })
	if (!s.description.trim()) errors.push({ name: 'description', message: t('tal.validation.required') })
	if (!s.location.trim()) errors.push({ name: 'location', message: t('tal.validation.required') })

	return errors
}

function sanitizeIntegerInput(value: string | number) {
	return String(value ?? '').replace(/[^\d]/g, '')
}

function parseIntegerInput(value: string) {
	if (!value) return null
	const parsed = Number.parseInt(value, 10)
	return Number.isFinite(parsed) ? parsed : null
}

function resetForm() {
	Object.assign(state, {
		category: 'beauty',
		service_label: '',
		description: '',
		location: '',
		availability_status: 'now',
		available_note: '',
		price_from: ''
	})
}

async function onSubmit(_event: FormSubmitEvent<typeof state>) {
	submitting.value = true
	try {
		const payload: TalMasterCreate = {
			category: state.category,
			service_label: state.service_label.trim(),
			description: state.description.trim(),
			location: state.location.trim(),
			availability_status: state.availability_status,
			available_note: state.available_note.trim() || null,
			price_from: parseIntegerInput(state.price_from)
		}

		await createMaster(payload)

		hapticFeedback('notification')
		toast.add({
			title: t('tal.create.successTitle'),
			description: t('tal.create.successDesc'),
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
			description: t('tal.create.error'),
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
		:title="t('tal.create.title')"
		:description="t('tal.create.desc')"
		:ui="{ content: 'sm:max-w-sm mx-auto max-h-[88dvh] rounded-t-[20px] border border-gray-800 bg-[#111418]' }"
		side="bottom"
	>
		<template #body>
			<div class="create-sheet-body">
				<UForm class="tma-no-zoom" :state="state" :validate="validate" @submit="onSubmit">
					<div class="app-panel app-panel--soft app-form-card">
						<div class="create-sheet-grid">
							<UFormField :label="t('tal.create.category')" name="category" required eager-validation>
								<USelect v-model="state.category" :items="categoryOptions" size="lg" class="w-full" />
							</UFormField>

							<UFormField
								:label="t('tal.create.serviceLabel')"
								name="service_label"
								required
								eager-validation
							>
								<UInput
									v-model="state.service_label"
									fixed
									icon="i-lucide-scissors"
									variant="outline"
									size="lg"
									:placeholder="t('tal.create.serviceLabelPlaceholder')"
									class="w-full"
								/>
							</UFormField>

							<UFormField
								:label="t('tal.create.description')"
								name="description"
								required
								eager-validation
							>
								<UTextarea
									v-model="state.description"
									fixed
									:rows="4"
									autoresize
									:placeholder="t('tal.create.descriptionPlaceholder')"
									class="w-full"
								/>
							</UFormField>

							<UFormField :label="t('tal.create.location')" name="location" required eager-validation>
								<UInput
									v-model="state.location"
									fixed
									icon="i-lucide-map-pin"
									variant="outline"
									size="lg"
									:placeholder="t('tal.create.locationPlaceholder')"
									class="w-full"
								/>
							</UFormField>
						</div>
					</div>

					<div class="app-panel app-panel--soft app-form-card">
						<div class="create-sheet-grid">
							<UFormField
								:label="t('tal.create.availability')"
								name="availability_status"
								required
								eager-validation
							>
								<USelect
									v-model="state.availability_status"
									:items="availabilityOptions"
									size="lg"
									class="w-full"
								/>
							</UFormField>

							<UFormField :label="t('tal.create.availableNote')" name="available_note">
								<UInput
									v-model="state.available_note"
									fixed
									icon="i-lucide-clock-3"
									variant="outline"
									size="lg"
									:placeholder="t('tal.create.availableNotePlaceholder')"
									class="w-full"
								/>
							</UFormField>

							<UFormField :label="t('tal.create.priceFrom')" name="price_from">
								<UInput
									:model-value="state.price_from"
									fixed
									inputmode="numeric"
									variant="outline"
									size="lg"
									:placeholder="t('tal.create.priceFromPlaceholder')"
									class="w-full"
									@update:model-value="state.price_from = sanitizeIntegerInput($event)"
								>
									<template #trailing>
										<span class="text-sm text-gray-500">₽</span>
									</template>
								</UInput>
							</UFormField>
						</div>
					</div>

					<UButton
						type="submit"
						block
						class="tma-no-zoom-button"
						color="primary"
						size="lg"
						:loading="submitting"
						icon="i-lucide-plus"
					>
						{{ t('tal.create.button') }}
					</UButton>
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

.create-sheet-grid {
	display: flex;
	flex-direction: column;
	gap: 14px;
}
</style>
