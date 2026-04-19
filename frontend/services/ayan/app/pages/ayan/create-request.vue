<script setup lang="ts">
import type { AyanRequestCreate } from '../../types/ayan'

const { t } = useI18n()
const { hapticFeedback } = useTg()
const { createRequest } = useAyanRequests()

const submitting = ref(false)

const state = reactive<AyanRequestCreate>({
	from_address: '',
	to_address: '',
	date: '',
	time: '',
	description: ''
})

const validate = (s: Partial<AyanRequestCreate>) => {
	const errors: { name: string; message: string }[] = []
	if (!s.from_address) errors.push({ name: 'from_address', message: t('ayan.validation.required') })
	if (!s.to_address) errors.push({ name: 'to_address', message: t('ayan.validation.required') })
	if (!s.date) errors.push({ name: 'date', message: t('ayan.validation.required') })
	return errors
}

async function onSubmit() {
	submitting.value = true
	try {
		await createRequest(state)
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

			<header class="mb-5">
				<h1 class="mb-1 text-xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('ayan.request.create') }}
				</h1>
				<p class="text-sm text-gray-400">
					{{ t('ayan.request.createDesc') }}
				</p>
			</header>

			<UForm :state="state" :validate="validate" @submit="onSubmit">
				<div class="space-y-4">
					<UFormField name="from_address" required>
						<UInput
							v-model="state.from_address"
							placeholder="Откуда"
							icon="i-lucide-circle-dot"
							variant="outline"
							size="lg"
						/>
					</UFormField>

					<UFormField name="to_address" required>
						<UInput
							v-model="state.to_address"
							placeholder="Куда"
							icon="i-lucide-map-pin"
							variant="outline"
							size="lg"
						/>
					</UFormField>

					<div class="grid grid-cols-2 gap-3">
						<UFormField name="date" required>
							<UInput
								v-model="state.date"
								type="date"
								icon="i-lucide-calendar"
								variant="outline"
								size="lg"
							/>
						</UFormField>
						<UFormField name="time">
							<UInput
								v-model="state.time"
								type="time"
								icon="i-lucide-clock"
								variant="outline"
								size="lg"
								placeholder="--:--"
							/>
						</UFormField>
					</div>

					<UFormField name="description">
						<UTextarea
							v-model="state.description"
							placeholder="Комментарий (необязательно)"
							:rows="2"
							autoresize
						/>
					</UFormField>

					<UButton
						type="submit"
						block
						size="xl"
						color="primary"
						:loading="submitting"
						:label="t('ayan.request.create')"
					/>
				</div>
			</UForm>
		</div>
	</div>
</template>
