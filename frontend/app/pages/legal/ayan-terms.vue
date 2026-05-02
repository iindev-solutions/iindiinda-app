<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { t, tm } = useI18n()

type LegalSection = {
	title: string
	paragraphs?: string[]
	bullets?: string[]
}

const sections = computed(() => tm('legal.ayanRules.sections') as LegalSection[])
</script>

<template>
	<LegalDocumentPage
		:badge="t('legal.badge')"
		:title="t('legal.ayanRules.title')"
		:intro="t('legal.ayanRules.intro')"
		:sections="sections"
	/>
</template>
