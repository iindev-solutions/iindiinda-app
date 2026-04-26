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
	font-size: clamp(42px, 12vw, 72px);
	color: rgb(var(--color-cyan-400));
	text-shadow: 0 0 32px rgb(94 218 198 / 0.18);
}

.app-title__suffix {
	font-size: clamp(24px, 7vw, 42px);
	color: rgb(125 141 149 / 0.88);
	margin-bottom: 4px;
}
</style>
