<template>
	<div class="app-title">
		<span class="app-title__mark">iind</span>
		<span class="app-title__suffix">.app</span>
	</div>
</template>

<style scoped>
.app-title {
	display: inline-flex;
	align-items: flex-end;
	gap: 4px;
	font-weight: 700;
	letter-spacing: -0.04em;
	line-height: 0.95;
}

.app-title__mark {
	font-size: clamp(40px, 11vw, 68px);
	color: var(--color-cyan-500);
}

.app-title__suffix {
	font-size: clamp(22px, 6vw, 36px);
	color: var(--text-secondary);
	margin-bottom: 4px;
}
</style>
