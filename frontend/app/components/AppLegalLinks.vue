<script setup lang="ts">
import type { LegalScope } from '~/utils/legal'
import { getLegalLinksByScope } from '~/utils/legal'

const props = withDefaults(
	defineProps<{
		compact?: boolean
		showIntro?: boolean
		scope?: LegalScope
	}>(),
	{
		compact: false,
		showIntro: true,
		scope: 'platform'
	}
)

const { t } = useI18n()
const links = computed(() => getLegalLinksByScope(props.scope, t))
const introKey = computed(() => `legal.intros.${props.scope}`)
</script>

<template>
	<div :class="props.compact ? 'space-y-2' : 'space-y-3'">
		<p v-if="props.showIntro" class="text-xs leading-relaxed text-gray-500">
			{{ t(introKey) }}
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
				{{ link.label }}
			</UButton>
		</div>
	</div>
</template>
