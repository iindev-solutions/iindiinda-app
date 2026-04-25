<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { t } = useI18n()

const aboutExamples = computed(() => [
	{
		title: t('serviceAbout.agal.examples.carrier.title'),
		description: t('serviceAbout.agal.examples.carrier.description')
	},
	{
		title: t('serviceAbout.agal.examples.sender.title'),
		description: t('serviceAbout.agal.examples.sender.description')
	}
])
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
					{{ t('servicePages.agal.badge') }}
				</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('servicePages.agal.title') }}
				</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					{{ t('servicePages.agal.intro') }}
				</p>
			</header>

			<div class="mb-8">
				<AppServiceAbout
					:label="t('serviceAbout.label')"
					:description="t('serviceAbout.agal.description')"
					:examples-title="t('serviceAbout.examplesTitle')"
					:examples="aboutExamples"
				/>
			</div>

			<div class="space-y-3">
				<UButton block size="lg" color="primary" disabled>{{ t('servicePages.agal.cta') }}</UButton>
				<p class="text-center text-xs text-gray-500">{{ t('servicePages.agal.status') }}</p>
			</div>
		</div>
	</div>
</template>
