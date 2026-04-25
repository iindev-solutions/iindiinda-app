<script setup lang="ts">
type LegalSection = {
	title: string
	paragraphs?: string[]
	bullets?: string[]
}

const { rt } = useI18n()

defineProps<{
	badge: string
	title: string
	intro: string
	sections: LegalSection[]
}>()
</script>

<template>
	<div class="min-h-screen px-4 py-6">
		<div class="mx-auto max-w-[720px] space-y-6">
			<BackButton force-ui />
			<header class="space-y-2">
				<p class="text-xs font-medium uppercase tracking-[0.18em] text-gray-500">{{ badge }}</p>
				<h1 class="text-2xl font-semibold text-cyan-50">{{ title }}</h1>
				<p class="text-sm leading-relaxed text-gray-400">{{ intro }}</p>
			</header>
			<UCard v-for="section in sections" :key="rt(section.title)" variant="outline">
				<div class="space-y-3">
					<h2 class="text-lg font-medium text-cyan-50">{{ rt(section.title) }}</h2>
					<p
						v-for="paragraph in section.paragraphs || []"
						:key="rt(paragraph)"
						class="text-sm leading-relaxed text-gray-300"
					>
						{{ rt(paragraph) }}
					</p>
					<ul
						v-if="section.bullets?.length"
						class="list-disc space-y-2 pl-5 text-sm leading-relaxed text-gray-300"
					>
						<li v-for="bullet in section.bullets" :key="rt(bullet)">{{ rt(bullet) }}</li>
					</ul>
				</div>
			</UCard>
		</div>
	</div>
</template>
