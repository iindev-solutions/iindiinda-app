<script setup lang="ts">
import type { FormError, FormSubmitEvent } from '@nuxt/ui'
import { getLocalTimeZone, today } from '@internationalized/date'
import type { UusBudgetType, UusCategory, UusDesiredWhen, UusTaskCreate, UusUrgency } from '../types/uus'

const emit = defineEmits<{ created: [] }>()

const { t } = useI18n()
const toast = useToast()
const { hapticFeedback } = useTg()
const { createTask } = useUusTasks()

const open = defineModel<boolean>('open', { default: false })

const state = reactive({
	category: 'home' as UusCategory,
	description: '',
	location: '',
	desired_when: 'today' as UusDesiredWhen,
	date: '',
	budget: '',
	budget_type: 'fixed' as UusBudgetType,
	urgency: 'normal' as UusUrgency
})

const submitting = ref(false)
const todayDate = computed(() => today(getLocalTimeZone()).toString())
const responseLimitPreview = computed(() => (state.urgency === 'urgent' ? 3 : 5))

const categoryOptions = computed(() => [
	{ label: t('uus.category.home'), value: 'home' as const },
	{ label: t('uus.category.repair'), value: 'repair' as const },
	{ label: t('uus.category.delivery'), value: 'delivery' as const },
	{ label: t('uus.category.other'), value: 'other' as const }
])

const desiredWhenOptions = computed(() => [
	{ label: t('uus.when.today'), value: 'today' as const },
	{ label: t('uus.when.tomorrow'), value: 'tomorrow' as const },
	{ label: t('uus.when.date'), value: 'date' as const },
	{ label: t('uus.when.flexible'), value: 'flexible' as const }
])

const budgetTypeOptions = computed(() => [
	{ label: t('uus.budgetType.fixed'), value: 'fixed' as const },
	{ label: t('uus.budgetType.negotiable'), value: 'negotiable' as const }
])

const urgencyOptions = computed(() => [
	{ label: t('uus.urgency.normal'), value: 'normal' as const },
	{ label: t('uus.urgency.urgent'), value: 'urgent' as const }
])

const validate = (s: typeof state): FormError[] => {
	const errors: FormError[] = []

	if (!s.description.trim()) errors.push({ name: 'description', message: t('uus.validation.required') })
	if (!s.location.trim()) errors.push({ name: 'location', message: t('uus.validation.required') })
	if (s.desired_when === 'date' && !s.date) errors.push({ name: 'date', message: t('uus.validation.required') })
	if (s.date && s.date < todayDate.value) errors.push({ name: 'date', message: t('uus.validation.dateFuture') })

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
		category: 'home',
		description: '',
		location: '',
		desired_when: 'today',
		date: '',
		budget: '',
		budget_type: 'fixed',
		urgency: 'normal'
	})
}

async function onSubmit(_event: FormSubmitEvent<typeof state>) {
	submitting.value = true
	try {
		const payload: UusTaskCreate = {
			category: state.category,
			description: state.description.trim(),
			location: state.location.trim(),
			desired_when: state.desired_when,
			date: state.desired_when === 'date' ? state.date : null,
			budget: parseIntegerInput(state.budget),
			budget_type: state.budget_type,
			urgency: state.urgency
		}

		await createTask(payload)

		hapticFeedback('notification')
		toast.add({
			title: t('uus.create.successTitle'),
			description: t('uus.create.successDesc'),
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
			description: t('uus.create.error'),
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
		:title="t('uus.create.title')"
		:description="t('uus.create.desc')"
		:ui="{ content: 'sm:max-w-sm mx-auto max-h-[88dvh] rounded-t-[20px] border border-gray-800 bg-[#111418]' }"
		side="bottom"
	>
		<template #body>
			<div class="create-sheet-body">
				<UForm class="tma-no-zoom" :state="state" :validate="validate" @submit="onSubmit">
					<div class="app-panel app-panel--soft app-form-card">
						<div class="create-sheet-grid">
							<UFormField :label="t('uus.create.category')" name="category" required eager-validation>
								<USelect v-model="state.category" :items="categoryOptions" size="lg" class="w-full" />
							</UFormField>

							<UFormField
								:label="t('uus.create.description')"
								name="description"
								required
								eager-validation
							>
								<UTextarea
									v-model="state.description"
									fixed
									:rows="4"
									autoresize
									:placeholder="t('uus.create.descriptionPlaceholder')"
									class="w-full"
								/>
							</UFormField>

							<UFormField :label="t('uus.create.location')" name="location" required eager-validation>
								<UInput
									v-model="state.location"
									fixed
									icon="i-lucide-map-pin"
									variant="outline"
									size="lg"
									:placeholder="t('uus.create.locationPlaceholder')"
									class="w-full"
								/>
							</UFormField>
						</div>
					</div>

					<div class="app-panel app-panel--soft app-form-card">
						<div class="create-sheet-grid">
							<UFormField :label="t('uus.create.when')" name="desired_when" required eager-validation>
								<USelect
									v-model="state.desired_when"
									:items="desiredWhenOptions"
									size="lg"
									class="w-full"
								/>
							</UFormField>

							<UFormField
								v-if="state.desired_when === 'date'"
								:label="t('uus.create.date')"
								name="date"
								required
								eager-validation
							>
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

							<div class="grid grid-cols-2 gap-3">
								<UFormField :label="t('uus.create.budget')" name="budget">
									<UInput
										:model-value="state.budget"
										fixed
										inputmode="numeric"
										variant="outline"
										size="lg"
										:placeholder="t('uus.create.budgetPlaceholder')"
										class="w-full"
										@update:model-value="state.budget = sanitizeIntegerInput($event)"
									>
										<template #trailing>
											<span class="text-sm text-gray-500">₽</span>
										</template>
									</UInput>
								</UFormField>

								<UFormField
									:label="t('uus.create.budgetType')"
									name="budget_type"
									required
									eager-validation
								>
									<USelect
										v-model="state.budget_type"
										:items="budgetTypeOptions"
										size="lg"
										class="w-full"
									/>
								</UFormField>
							</div>

							<UFormField :label="t('uus.create.urgency')" name="urgency" required eager-validation>
								<USelect v-model="state.urgency" :items="urgencyOptions" size="lg" class="w-full" />
							</UFormField>
						</div>
					</div>

					<div class="app-panel app-panel--soft app-form-card create-sheet-summary">
						<div>
							<p class="app-section-title mb-2">{{ t('uus.task.responseLimit') }}</p>
							<p class="create-sheet-summary__value">
								{{ t('uus.responseLimit', { count: responseLimitPreview }) }}
							</p>
						</div>
						<UBadge :color="state.urgency === 'urgent' ? 'error' : 'primary'" variant="subtle" size="sm">
							{{ t(`uus.urgency.${state.urgency}`) }}
						</UBadge>
					</div>

					<UButton type="submit" block size="xl" color="primary" :loading="submitting">
						{{ t('uus.create.submit') }}
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
	padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 8px);
}

.create-sheet-grid {
	display: flex;
	flex-direction: column;
	gap: 14px;
}

.create-sheet-summary {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
}

.create-sheet-summary__value {
	font-size: 14px;
	font-weight: 600;
	line-height: 1.45;
	color: var(--text-primary);
}
</style>
