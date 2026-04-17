<script setup lang="ts">
import type { FormSubmitEvent } from '@nuxt/ui'

// Page metadata
definePageMeta({
	layout: 'default'
})

// Composables
const { t } = useI18n()
const { hapticFeedback, showBackButton, hideBackButton, onBackButtonClicked } = useTg()
const { post } = useTaxiAPI() // Используем Taxi API (мок или реальный)
const toast = useToast()
const router = useRouter()

// Form state
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

// Form submission
async function onSubmit(event: FormSubmitEvent<OrderForm>) {
	isSubmitting.value = true
	hapticFeedback('impact')

	try {
		await post('/ayan/orders', {
			pickup: event.data.pickup,
			destination: event.data.destination,
			price: event.data.price
		})

		toast.add({
			title: t('ayan.create.success'),
			color: 'cyan'
		})

		// Navigate to order tracking page
		router.push('/ayan/my-order')
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

// Validation
function validatePrice(value: number | null) {
	if (!value || value <= 0) {
		return t('ayan.create.validation.priceRequired')
	}
	return true
}

// Initialize back button
onMounted(() => {
	showBackButton()
	onBackButtonClicked(() => {
		router.push('/ayan')
	})
})

onUnmounted(() => {
	hideBackButton()
})
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<!-- Header -->
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

			<!-- Form -->
			<UForm :state="form" class="space-y-4" @submit="onSubmit">
				<!-- Pickup Location -->
				<UFormField name="pickup" :label="t('ayan.create.fields.pickup')" required>
					<UInput
						v-model="form.pickup"
						:placeholder="t('ayan.create.placeholders.pickup')"
						size="lg"
						class="w-full"
						required
					/>
				</UFormField>

				<!-- Destination -->
				<UFormField name="destination" :label="t('ayan.create.fields.destination')" required>
					<UInput
						v-model="form.destination"
						:placeholder="t('ayan.create.placeholders.destination')"
						size="lg"
						class="w-full"
						required
					/>
				</UFormField>

				<!-- Price -->
				<UFormField name="price" :label="t('ayan.create.fields.price')" required :validate="validatePrice">
					<UInput
						v-model.number="form.price"
						:placeholder="t('ayan.create.placeholders.price')"
						type="number"
						size="lg"
						class="w-full"
						min="1"
					>
						<template #trailing>
							<span class="text-gray-400">₽</span>
						</template>
					</UInput>
				</UFormField>

				<!-- Submit Button -->
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
