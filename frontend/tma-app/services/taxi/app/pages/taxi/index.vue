<script setup lang="ts">
const router = useRouter()

const goBack = () => {
	router.push('/')
}
</script>

<template>
	<div class="taxi-page">
		<div class="taxi-header">
			<UButton 
				icon="i-heroicons-arrow-left" 
				color="gray" 
				variant="ghost"
				@click="goBack"
			/>
			<h1>🚕 Такси</h1>
		</div>

		<div class="taxi-content">
			<UCard>
				<template #header>
					<h3>Добро пожаловать в сервис такси</h3>
				</template>
				<p>Здесь будет функционал заказа такси</p>
				
				<div class="mt-4">
					<UButton @click="$router.push('/taxi/booking')">
						Заказать такси
					</UButton>
				</div>
			</UCard>
		</div>
	</div>
</template>

<style scoped>
.taxi-page {
	min-height: 100vh;
	padding: 20px;
}

.taxi-header {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 24px;
}

.taxi-header h1 {
	font-size: 24px;
	font-weight: 700;
}

.taxi-content {
	max-width: 600px;
	margin: 0 auto;
}
</style>
