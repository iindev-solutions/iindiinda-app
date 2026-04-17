<script setup lang="ts">
// Page metadata
definePageMeta({
	layout: 'default'
})

// Composables
const { t } = useI18n()
const { hapticFeedback } = useTg()
const { post } = useAPI()
const router = useRouter()

// State
const isAvailable = ref(false)
const isSwitching = ref(false)

// Switch to driver role and enable availability
async function toggleAvailability() {
	isSwitching.value = true
	hapticFeedback('impact')

	try {
		await post('/user/switch-role', { role: 'driver' })
		isAvailable.value = !isAvailable.value

		if (isAvailable.value) {
			// Navigate to orders page when becoming available
			router.push('/ayan/orders')
		}
	} catch (error) {
		console.error('Failed to switch role:', error)
		isAvailable.value = false
	} finally {
		isSwitching.value = false
	}
}
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<!-- Header -->
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
					{{ t('ayan.driver.header.subtitle') }}
				</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('ayan.driver.title') }}
				</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					{{ t('ayan.driver.description') }}
				</p>
			</header>

			<!-- Availability Toggle -->
			<UCard class="mb-6 rounded-2xl border-gray-800 bg-level-1">
				<div class="flex items-center justify-between">
					<div>
						<h3 class="font-medium text-white">
							{{ t('ayan.driver.availability.title') }}
						</h3>
						<p class="text-sm text-gray-400">
							{{
								isAvailable
									? t('ayan.driver.availability.online')
									: t('ayan.driver.availability.offline')
							}}
						</p>
					</div>
					<USwitch v-model="isAvailable" :loading="isSwitching" @update:model-value="toggleAvailability" />
				</div>
			</UCard>

			<!-- Stats Cards -->
			<div class="mb-6 grid grid-cols-2 gap-4">
				<UCard class="rounded-2xl border-gray-800 bg-level-1">
					<div class="text-center">
						<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
							{{ t('ayan.driver.stats.completed') }}
						</div>
						<div class="text-2xl font-semibold text-white">0</div>
					</div>
				</UCard>
				<UCard class="rounded-2xl border-gray-800 bg-level-1">
					<div class="text-center">
						<div class="mb-1 text-xs uppercase tracking-wider text-gray-500">
							{{ t('ayan.driver.stats.rating') }}
						</div>
						<div class="text-2xl font-semibold text-cyan-400">5.0</div>
					</div>
				</UCard>
			</div>

			<!-- Instructions -->
			<UCard class="rounded-2xl border-cyan-500/20 bg-level-1">
				<div class="space-y-4">
					<h3 class="font-medium text-white">
						{{ t('ayan.driver.instructions.title') }}
					</h3>
					<div class="space-y-3 text-sm text-gray-400">
						<div class="flex items-start gap-3">
							<div
								class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-xs text-cyan-400"
							>
								1
							</div>
							<span>{{ t('ayan.driver.instructions.step1') }}</span>
						</div>
						<div class="flex items-start gap-3">
							<div
								class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-xs text-cyan-400"
							>
								2
							</div>
							<span>{{ t('ayan.driver.instructions.step2') }}</span>
						</div>
						<div class="flex items-start gap-3">
							<div
								class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-cyan-500/20 text-xs text-cyan-400"
							>
								3
							</div>
							<span>{{ t('ayan.driver.instructions.step3') }}</span>
						</div>
					</div>
				</div>
			</UCard>
		</div>
	</div>
</template>
