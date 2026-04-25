<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { t } = useI18n()

const aboutExamples = computed(() => [
	{
		title: t('serviceAbout.tal.examples.now.title'),
		description: t('serviceAbout.tal.examples.now.description')
	},
	{
		title: t('serviceAbout.tal.examples.request.title'),
		description: t('serviceAbout.tal.examples.request.description')
	}
])
</script>

<template>
	<div class="min-h-screen px-4 py-6 pb-8">
		<div class="mx-auto max-w-[480px]">
			<header class="mb-8 pt-2">
				<div class="mb-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">
					{{ t('servicePages.tal.badge') }}
				</div>
				<h1 class="mb-2 text-2xl font-medium tracking-tight text-[#eff3f5]">
					{{ t('servicePages.tal.title') }}
				</h1>
				<p class="text-sm leading-relaxed text-gray-300">
					{{ t('servicePages.tal.intro') }}
				</p>
			</header>

			<div class="mb-8">
				<AppServiceAbout
					:label="t('serviceAbout.label')"
					:description="t('serviceAbout.tal.description')"
					:examples-title="t('serviceAbout.examplesTitle')"
					:examples="aboutExamples"
				/>
			</div>

			<div class="space-y-3">
				<NuxtLink to="/tal-showcase">
					<UButton block size="lg" variant="outline">{{ t('servicePages.tal.cta') }}</UButton>
				</NuxtLink>
				<p class="text-center text-xs text-gray-500">{{ t('servicePages.tal.status') }}</p>
			</div>
		</div>
	</div>
</template>
