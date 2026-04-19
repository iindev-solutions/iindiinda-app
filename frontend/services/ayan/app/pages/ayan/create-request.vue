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
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<BackButton />

			<header class="mb-6">
				<h1 class="mb-1 text-xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('ayan.request.create') }}
				</h1>
				<p class="text-sm text-gray-400">
					{{ t('ayan.request.createDesc') }}
				</p>
			</header>

			<UForm :state="state" :validate="validate" @submit="onSubmit">
				<div class="space-y-4">
					<UFormField :label="t('ayan.request.from')" name="from_address" required>
						<UInput
							v-model="state.from_address"
							:placeholder="t('ayan.request.from')"
							icon="i-lucide-map-pin"
						/>
					</UFormField>

					<UFormField :label="t('ayan.request.to')" name="to_address" required>
						<UInput
							v-model="state.to_address"
							:placeholder="t('ayan.request.to')"
							icon="i-lucide-map-pin"
						/>
					</UFormField>

					<UFormField :label="t('ayan.request.date')" name="date" required>
						<UInput v-model="state.date" type="date" icon="i-lucide-calendar" />
					</UFormField>

					<UFormField :label="t('ayan.ride.time')" name="time">
						<UInput v-model="state.time" type="time" icon="i-lucide-clock" />
					</UFormField>

					<UFormField :label="t('ayan.request.comment')" name="description">
						<UTextarea
							v-model="state.description"
							:placeholder="t('ayan.request.comment')"
							:rows="2"
							autoresize
						/>
					</UFormField>

					<UButton
						type="submit"
						block
						size="lg"
						color="primary"
						:loading="submitting"
						:label="t('ayan.request.create')"
					/>
				</div>
			</UForm>
		</div>
	</div>
</template>
