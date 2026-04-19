<template>
	<div class="tma-layout">
		<main class="tma-main">
			<slot />
		</main>
		<AppBottomNav />
	</div>
</template>

<style>
.tma-layout {
	min-height: 100dvh;
	background: var(--bg-level-0);
}
.tma-main {
	padding-bottom: calc(52px + env(safe-area-inset-bottom, 0px));
}
</style>
