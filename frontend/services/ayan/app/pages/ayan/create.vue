<script setup lang="ts">
import type { FormSubmitEvent } from '@nuxt/ui'

definePageMeta({
	layout: 'default'
})

const { t } = useI18n()
const { hapticFeedback } = useTg()
const { post } = useTaxiAPI()
const toast = useToast()

interface OrderForm {
	pickup: string
	destination: string
	price: number | null
}

const form = ref<OrderForm>({
	pickup: '',
	destination: '',
	price: null
})

const isSubmitting = ref(false)

async function onSubmit(event: FormSubmitEvent<OrderForm>) {
	isSubmitting.value = true
	hapticFeedback('impact')

	try {
		await post('/ayan/orders', {
			from_address: event.data.pickup,
			to_address: event.data.destination,
			price: event.data.price
		})

		toast.add({
			title: t('ayan.create.success'),
			color: 'cyan'
		})

		navigateTo('/ayan/my-order')
	} catch (err: any) {
		console.error('Failed to create order:', err)
		toast.add({
			title: err?.message || t('ayan.create.error'),
			color: 'gray'
		})
	} finally {
		isSubmitting.value = false
	}
}

function validatePrice(value: number | null) {
	if (!value || value < 100) {
		return t('ayan.create.validation.priceMin')
	}
	if (value > 5000) {
		return t('ayan.create.validation.priceMax')
	}
	return true
}
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
					{{ t('ayan.header.subtitle') }}
				</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('ayan.create.title') }}
				</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					{{ t('ayan.create.description') }}
				</p>
			</header>

			<UForm :state="form" class="space-y-4" @submit="onSubmit">
				<UFormField name="pickup" :label="t('ayan.create.fields.pickup')" required>
					<UInput
						v-model="form.pickup"
						:placeholder="t('ayan.create.placeholders.pickup')"
						size="lg"
						class="w-full"
						required
					/>
				</UFormField>

				<UFormField name="destination" :label="t('ayan.create.fields.destination')" required>
					<UInput
						v-model="form.destination"
						:placeholder="t('ayan.create.placeholders.destination')"
						size="lg"
						class="w-full"
						required
					/>
				</UFormField>

				<UFormField name="price" :label="t('ayan.create.fields.price')" required :validate="validatePrice">
					<UInput
						v-model.number="form.price"
						:placeholder="t('ayan.create.placeholders.price')"
						type="number"
						size="lg"
						class="w-full"
						min="100"
					>
						<template #trailing>
							<span class="text-gray-400">₽</span>
						</template>
					</UInput>
				</UFormField>

				<UButton
					type="submit"
					block
					size="lg"
					color="primary"
					:loading="isSubmitting"
					:disabled="isSubmitting || !form.pickup || !form.destination || !form.price"
					class="mt-6"
				>
					{{ t('ayan.create.submitButton') }}
				</UButton>
			</UForm>
		</div>
	</div>
</template>
