<script setup lang="ts">
defineProps<{
	message: string
	retry?: () => void
}>()
</script>

<template>
	<div class="error-message">
		<div class="error-message__icon">
			<UIcon name="i-carbon-warning" />
		</div>
		<p class="error-message__text">{{ message }}</p>
		<UButton v-if="retry" variant="outline" color="cyan" size="sm" @click="retry">
			{{ $t('common.retry') }}
		</UButton>
	</div>
</template>

<style scoped>
.error-message {
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 24px;
	text-align: center;
}

.error-message__icon {
	font-size: 32px;
	color: rgb(var(--color-red-400));
	margin-bottom: 12px;
}

.error-message__text {
	font-size: 14px;
	color: rgb(var(--color-gray-300));
	margin: 0 0 16px;
}
</style>
