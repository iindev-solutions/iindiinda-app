<script setup lang="ts">
import { getAyanLegalLinks } from '~/utils/legal'

const props = withDefaults(
	defineProps<{
		compact?: boolean
		showIntro?: boolean
	}>(),
	{
		compact: false,
		showIntro: true
	}
)

const { t } = useI18n()
const links = getAyanLegalLinks()
</script>

<template>
	<div :class="props.compact ? 'space-y-2' : 'space-y-3'">
		<p v-if="props.showIntro" class="text-xs leading-relaxed text-gray-500">
			{{ t('legal.intro') }}
		</p>
		<div class="flex flex-wrap gap-2">
			<UButton
				v-for="link in links"
				:key="link.key"
				:to="link.to"
				color="neutral"
				variant="outline"
				:size="props.compact ? 'xs' : 'sm'"
			>
				{{ t(`legal.links.${link.key}`) }}
			</UButton>
		</div>
	</div>
</template>
