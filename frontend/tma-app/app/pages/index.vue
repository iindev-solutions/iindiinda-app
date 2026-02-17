<template>
	<div class="home-page">
		<div class="services-container">
			<h1 class="title">Выберите сервис</h1>

			<div class="services-grid">
				<UCard class="service-card" @click="navigateTo('/taxi')">
					<template #header>
						<div class="service-icon">🚕</div>
					</template>
					<div class="service-content">
						<h3>Такси</h3>
						<p>Быстрый заказ такси в вашем городе</p>
					</div>
				</UCard>
				<UCard class="service-card">
					<nuxt-link to="/otpravka">
						<template #header>
							<div class="service-icon">👨</div>
						</template>
						<div class="service-content">
							<h3>отправка</h3>
						</div>
					</nuxt-link>
				</UCard>

				<UCard class="service-card service-card--disabled">
					<template #header>
						<div class="service-icon">👨‍🔧</div>
					</template>
					<div class="service-content">
						<h3>Мастера</h3>
						<p>Скоро появится</p>
					</div>
				</UCard>
			</div>
		</div>
	</div>
</template>

<style scoped>
.home-page {
	min-height: 100vh;
	padding: 20px;
	display: flex;
	align-items: center;
	justify-content: center;
}

.services-container {
	width: 100%;
	max-width: 600px;
}

.title {
	font-size: 28px;
	font-weight: 700;
	margin-bottom: 32px;
	text-align: center;
}

.services-grid {
	display: grid;
	gap: 16px;
}

.service-card {
	cursor: pointer;
	transition:
		transform 0.2s ease,
		box-shadow 0.2s ease;
}

.service-card:active {
	transform: scale(0.98);
}

.service-card--disabled {
	opacity: 0.6;
	cursor: not-allowed;
}

.service-icon {
	font-size: 48px;
	text-align: center;
}

.service-content {
	text-align: center;
}

.service-content h3 {
	font-size: 20px;
	font-weight: 600;
	margin-bottom: 8px;
}

.service-content p {
	font-size: 14px;
	color: var(--color-gray-500);
}
</style>
