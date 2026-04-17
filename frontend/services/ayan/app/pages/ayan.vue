<script setup lang="ts">
// Page metadata
definePageMeta({
	layout: 'default'
})

// Composables
const { t } = useI18n()
const { hapticFeedback } = useTg()
const router = useRouter()

// Navigation handlers
function goToCreateOrder() {
	hapticFeedback('impact')
	router.push('/ayan/create')
}

function goToDriverMode() {
	hapticFeedback('impact')
	router.push('/ayan/driver')
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
					{{ t('ayan.name') }}
				</h1>
				<p class="text-sm leading-relaxed text-gray-300">{{ t('ayan.desc') }}</p>
			</header>

			<section class="mb-8 space-y-3">
				<div class="rounded-2xl border border-cyan-500/15 bg-[#2a3a42]/30 p-5">
					<div class="mb-3 flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<UIcon name="i-lucide-circle-arrow-up" class="text-cyan-400" />
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">{{ t('ayan.step1') }}</h3>
							<p class="text-xs text-gray-400">{{ t('ayan.step1desc') }}</p>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5">
					<div class="mb-3 flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<UIcon name="i-lucide-users" class="text-cyan-400" />
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">{{ t('ayan.step2') }}</h3>
							<p class="text-xs text-gray-400">{{ t('ayan.step2desc') }}</p>
						</div>
					</div>
				</div>

				<div class="rounded-2xl border border-white/10 bg-[#2a3a42]/30 p-5">
					<div class="mb-3 flex items-center gap-3">
						<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#0f1113]/60">
							<UIcon name="i-lucide-check-circle" class="text-cyan-400" />
						</div>
						<div>
							<h3 class="text-sm font-medium text-[#eff3f5]">{{ t('ayan.step3') }}</h3>
							<p class="text-xs text-gray-400">{{ t('ayan.step3desc') }}</p>
						</div>
					</div>
				</div>
			</section>

			<div class="space-y-3">
				<UButton block size="lg" color="primary" @click="goToCreateOrder">
					{{ t('ayan.createOrder') }}
				</UButton>

				<UButton block size="lg" variant="outline" color="gray" @click="goToDriverMode">
					<UIcon name="i-lucide-car" class="mr-2 h-5 w-5" />
					{{ t('ayan.driver.title') }}
				</UButton>

				<p class="text-center text-xs text-gray-500">{{ t('app.launchSoon') }}</p>
			</div>
		</div>
	</div>
</template>
