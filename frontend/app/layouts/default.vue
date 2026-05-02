<template>
	<div class="tma-layout">
		<main class="tma-main l-wrapper">
			<slot />
		</main>
		<AppBottomNav />
	</div>
</template>

<style>
.tma-layout {
	min-height: 100dvh;
	background: transparent;
}

.tma-main {
	padding-bottom: calc(88px + env(safe-area-inset-bottom, 0px));
}
</style>
