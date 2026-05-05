<script setup lang="ts">
defineProps<{
	size?: 'sm' | 'md' | 'lg'
}>()
</script>

<template>
	<div class="loading-spinner" :class="`loading-spinner--${size || `md`}`">
		<UIcon name="i-carbon-loading" class="loading-spinner__icon" />
	</div>
</template>

<style scoped>
.loading-spinner {
	display: flex;
	align-items: center;
	justify-content: center;
}

.loading-spinner--sm {
	padding: 16px;
}

.loading-spinner--md {
	padding: 32px;
}

.loading-spinner--lg {
	padding: 48px;
}

.loading-spinner__icon {
	font-size: 24px;
	color: var(--color-cyan-400);
	animation: spin 1s linear infinite;
}

.loading-spinner--sm .loading-spinner__icon {
	font-size: 16px;
}

.loading-spinner--lg .loading-spinner__icon {
	font-size: 32px;
}

@keyframes spin {
	from {
		transform: rotate(0deg);
	}
	to {
		transform: rotate(360deg);
	}
}
</style>
