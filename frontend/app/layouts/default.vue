<template>
	<div class="tma-layout">
		<div class="tma-layout__ambient tma-layout__ambient--top" />
		<div class="tma-layout__ambient tma-layout__ambient--side" />
		<main class="tma-main l-wrapper">
			<slot />
		</main>
		<AppBottomNav />
	</div>
</template>

<style>
.tma-layout {
	position: relative;
	min-height: 100dvh;
	background: transparent;
	isolation: isolate;
}

.tma-layout__ambient {
	position: fixed;
	pointer-events: none;
	filter: blur(64px);
	opacity: 0.8;
	z-index: -1;
}

.tma-layout__ambient--top {
	top: -120px;
	left: 50%;
	width: 360px;
	height: 220px;
	transform: translateX(-50%);
	background: rgb(94 218 198 / 0.12);
}

.tma-layout__ambient--side {
	right: -120px;
	top: 160px;
	width: 240px;
	height: 240px;
	background: rgb(94 218 198 / 0.08);
}

.tma-main {
	padding-bottom: calc(92px + env(safe-area-inset-bottom, 0px));
}
</style>
