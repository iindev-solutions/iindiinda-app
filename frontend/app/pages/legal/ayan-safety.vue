<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { t, tm } = useI18n()

type LegalSection = {
	title: string
	paragraphs?: string[]
	bullets?: string[]
}

const sections = computed(() => tm('legal.safety.sections') as LegalSection[])
</script>

<template>
	<div class="min-h-screen px-4 py-6">
		<div class="mx-auto max-w-[720px] space-y-6">
			<BackButton force-ui />
			<header class="space-y-2">
				<p class="text-xs font-medium uppercase tracking-[0.18em] text-gray-500">{{ t('legal.badge') }}</p>
				<h1 class="text-2xl font-semibold text-cyan-50">{{ t('legal.safety.title') }}</h1>
				<p class="text-sm leading-relaxed text-gray-400">{{ t('legal.safety.intro') }}</p>
			</header>
			<UCard v-for="section in sections" :key="section.title" variant="outline">
				<div class="space-y-3">
					<h2 class="text-lg font-medium text-cyan-50">{{ section.title }}</h2>
					<p
						v-for="paragraph in section.paragraphs || []"
						:key="paragraph"
						class="text-sm leading-relaxed text-gray-300"
					>
						{{ paragraph }}
					</p>
					<ul
						v-if="section.bullets?.length"
						class="list-disc space-y-2 pl-5 text-sm leading-relaxed text-gray-300"
					>
						<li v-for="bullet in section.bullets" :key="bullet">{{ bullet }}</li>
					</ul>
				</div>
			</UCard>
		</div>
	</div>
</template>
