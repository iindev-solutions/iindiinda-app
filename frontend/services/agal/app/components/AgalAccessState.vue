<script setup lang="ts">
import type { AyanAccessState } from '~/utils/auth'

const props = defineProps<{
	state: AyanAccessState
}>()

const { t } = useI18n()

const icon = computed(() => {
	if (props.state === 'auth-error') return 'i-lucide-shield-alert'
	if (props.state === 'telegram-required') return 'i-lucide-smartphone'
	return 'i-lucide-loader-circle'
})

const title = computed(() => {
	if (props.state === 'auth-error') return t('agal.access.authFailedTitle')
	if (props.state === 'telegram-required') return t('agal.access.telegramOnlyTitle')
	return t('agal.access.loadingTitle')
})

const description = computed(() => {
	if (props.state === 'auth-error') return t('agal.access.authFailedDesc')
	if (props.state === 'telegram-required') return t('agal.access.telegramOnlyDesc')
	return t('agal.access.loadingDesc')
})

function reloadPage() {
	window.location.reload()
}
</script>

<template>
	<div class="rounded-3xl border border-gray-800 bg-gray-900/70 p-6 text-center shadow-sm shadow-black/20">
		<div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-gray-800 text-cyan-400">
			<UIcon :name="icon" :class="state === 'loading' ? 'size-7 animate-spin' : 'size-7'" />
		</div>

		<h2 class="mt-4 text-lg font-semibold text-cyan-50">
			{{ title }}
		</h2>
		<p class="mt-2 text-sm leading-relaxed text-gray-400">
			{{ description }}
		</p>

		<div class="mt-5 flex justify-center gap-3">
			<UButton v-if="state === 'auth-error'" color="primary" icon="i-lucide-refresh-ccw" @click="reloadPage">
				{{ t('common.retry') }}
			</UButton>
			<UButton v-else-if="state === 'telegram-required'" color="neutral" variant="outline" to="/">
				{{ t('common.close') }}
			</UButton>
		</div>
	</div>
</template>
