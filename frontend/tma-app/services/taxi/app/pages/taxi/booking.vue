<script setup lang="ts">
const router = useRouter()
</script>

<template>
	<div class="booking-page">
		<div class="booking-header">
			<UButton 
				icon="i-heroicons-arrow-left" 
				color="neutral" 
				variant="ghost"
				@click="router.back()"
			/>
			<h1>Заказ такси</h1>
		</div>

		<div class="booking-content">
			<UCard>
				<p>Форма заказа такси</p>
			</UCard>
		</div>
	</div>
</template>

<style scoped>
.booking-page {
	min-height: 100vh;
	padding: 20px;
}

.booking-header {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 24px;
}

.booking-header h1 {
	font-size: 24px;
	font-weight: 700;
}

.booking-content {
	max-width: 600px;
	margin: 0 auto;
}
</style>
