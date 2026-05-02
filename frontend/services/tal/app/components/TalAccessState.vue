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
	if (props.state === 'auth-error') return t('tal.access.authFailedTitle')
	if (props.state === 'telegram-required') return t('tal.access.telegramOnlyTitle')
	return t('tal.access.loadingTitle')
})

const description = computed(() => {
	if (props.state === 'auth-error') return t('tal.access.authFailedDesc')
	if (props.state === 'telegram-required') return t('tal.access.telegramOnlyDesc')
	return t('tal.access.loadingDesc')
})

function reloadPage() {
	window.location.reload()
}
</script>

<template>
	<div class="app-panel access-state">
		<div class="access-state__icon">
			<UIcon :name="icon" :class="state === 'loading' ? 'size-7 animate-spin' : 'size-7'" />
		</div>

		<h2 class="access-state__title">{{ title }}</h2>
		<p class="access-state__description">{{ description }}</p>

		<div class="access-state__actions">
			<UButton v-if="state === 'auth-error'" color="primary" icon="i-lucide-refresh-ccw" @click="reloadPage">
				{{ t('common.retry') }}
			</UButton>
			<UButton v-else-if="state === 'telegram-required'" color="neutral" variant="outline" to="/">
				{{ t('common.close') }}
			</UButton>
		</div>
	</div>
</template>

<style scoped>
.access-state {
	padding: 28px 22px;
	text-align: center;
}

.access-state__icon {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 64px;
	height: 64px;
	margin: 0 auto;
	border-radius: 20px;
	background: rgb(94 218 198 / 0.12);
	border: 1px solid rgb(94 218 198 / 0.16);
	color: rgb(var(--color-cyan-300));
}

.access-state__title {
	margin: 16px 0 0;
	font-size: 20px;
	font-weight: 600;
	line-height: 1.3;
	color: var(--text-primary);
}

.access-state__description {
	margin: 10px 0 0;
	font-size: 14px;
	line-height: 1.65;
	color: var(--text-secondary);
}

.access-state__actions {
	margin-top: 18px;
	display: flex;
	justify-content: center;
	gap: 12px;
}
</style>
