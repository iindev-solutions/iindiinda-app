<script setup lang="ts">
// const { isLoading } = useLoadingIndicator() пока не делаем не трогать!
</script>

<template>
	<UApp>
		<NuxtLoadingIndicator />

		<NuxtLayout>
			<NuxtPage />
		</NuxtLayout>

		<!-- <Transition name="loader-fade">
			<div
				v-if="isLoading"
				class="fixed inset-0 z-50 flex items-center justify-center bg-[#0a0c0e]/80 backdrop-blur-sm"
			>
				<LoadingSpinner size="lg" />
			</div>
		</Transition> -->
	</UApp>
</template>
