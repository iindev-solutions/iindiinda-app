<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { t } = useI18n()

const aboutExamples = computed(() => [
	{
		title: t('serviceAbout.uus.examples.cleaning.title'),
		description: t('serviceAbout.uus.examples.cleaning.description')
	},
	{
		title: t('serviceAbout.uus.examples.repair.title'),
		description: t('serviceAbout.uus.examples.repair.description')
	}
])
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
					{{ t('servicePages.uus.badge') }}
				</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('servicePages.uus.title') }}
				</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					{{ t('servicePages.uus.intro') }}
				</p>
			</header>

			<div class="mb-8">
				<AppServiceAbout
					:label="t('serviceAbout.label')"
					:description="t('serviceAbout.uus.description')"
					:examples-title="t('serviceAbout.examplesTitle')"
					:examples="aboutExamples"
				/>
			</div>

			<div class="space-y-3">
				<UButton block size="lg" color="primary" disabled>{{ t('servicePages.uus.cta') }}</UButton>
				<p class="text-center text-xs text-gray-500">{{ t('servicePages.uus.status') }}</p>
			</div>
		</div>
	</div>
</template>
