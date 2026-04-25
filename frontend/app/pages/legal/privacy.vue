<script setup lang="ts">
definePageMeta({ layout: 'default' })

const { t, tm } = useI18n()

type LegalSection = {
	title: string
	paragraphs?: string[]
	bullets?: string[]
}

const sections = computed(() => tm('legal.privacy.sections') as LegalSection[])
</script>

<template>
	<LegalDocumentPage
		:badge="t('legal.badge')"
		:title="t('legal.privacy.title')"
		:intro="t('legal.privacy.intro')"
		:sections="sections"
	/>
</template>
