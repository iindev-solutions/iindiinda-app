<script setup lang="ts">
import type { Service, Master, TimeSlot } from '../composables/useTalStore'
import { useTalStore } from '../composables/useTalStore'

const { state, selectService, selectMaster, selectDate, selectTimeSlot, resetSelection } = useTalStore()

const mockServices: Service[] = [
	{ id: '1', name: 'Стрижка', duration: 30, price: 1500 },
	{ id: '2', name: 'Окрашивание', duration: 120, price: 4500 },
	{ id: '3', name: 'Маникюр', duration: 60, price: 2000 }
]

const mockMasters: Master[] = [
	{
		id: '1',
		name: 'Анна Иванова',
		specialization: 'Парикмахер',
		rating: 4.9,
		reviewCount: 127
	},
	{
		id: '2',
		name: 'Мария Петрова',
		specialization: 'Колорист',
		rating: 4.8,
		reviewCount: 89
	},
	{
		id: '3',
		name: 'Елена Сидорова',
		specialization: 'Мастер маникюра',
		rating: 5.0,
		reviewCount: 203
	}
]

const generateTimeSlots = (date: string): TimeSlot[] => {
	const slots: TimeSlot[] = []
	const hours = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00']

	hours.forEach((time, index) => {
		slots.push({
			id: `${date}-${index}`,
			time,
			date,
			available: Math.random() > 0.3
		})
	})

	return slots
}

const selectedDate = ref('2026-02-20')
const availableSlots = ref<TimeSlot[]>(generateTimeSlots(selectedDate.value))

const handleServiceSelect = (service: Service) => {
	selectService(service)
}

const handleMasterSelect = (master: Master) => {
	selectMaster(master)
}

const handleDateChange = (date: string) => {
	selectedDate.value = date
	selectDate(date)
	availableSlots.value = generateTimeSlots(date)
}

const handleSlotSelect = (slot: TimeSlot) => {
	if (slot.available) {
		selectTimeSlot(slot)
	}
}

const handleConfirm = () => {
	console.log('Booking confirmed:', {
		service: state.value.selectedService,
		master: state.value.selectedMaster,
		date: state.value.selectedDate,
		slot: state.value.selectedTimeSlot
	})
}

const handleReset = () => {
	resetSelection()
	selectedDate.value = '2026-02-20'
	availableSlots.value = generateTimeSlots(selectedDate.value)
}
</script>

<template>
	<div class="showcase-container">
		<div class="showcase-header">
			<h1 class="showcase-title">IIND.TAL Showcase</h1>
			<p class="showcase-subtitle">Booking Service Visual States</p>
			<UButton variant="outline" size="sm" @click="handleReset">Reset Selection</UButton>
		</div>

		<div class="showcase-grid">
			<section class="showcase-section">
				<div class="section-header">
					<UIcon name="i-lucide-scissors" class="section-icon" />
					<h2 class="section-title">1. Service Selection</h2>
				</div>

				<div class="services-grid">
					<button
						v-for="service in mockServices"
						:key="service.id"
						class="service-card"
						:class="{ 'service-card-selected': state.selectedService?.id === service.id }"
						@click="handleServiceSelect(service)"
					>
						<div class="service-info">
							<h3 class="service-name">{{ service.name }}</h3>
							<p class="service-duration">{{ service.duration }} мин</p>
						</div>
						<div class="service-price">{{ service.price }} ₽</div>
					</button>
				</div>
			</section>

			<section class="showcase-section">
				<div class="section-header">
					<UIcon name="i-lucide-user" class="section-icon" />
					<h2 class="section-title">2. Master Selection</h2>
				</div>

				<div class="masters-list">
					<button
						v-for="master in mockMasters"
						:key="master.id"
						class="master-card"
						:class="{ 'master-card-selected': state.selectedMaster?.id === master.id }"
						:disabled="!state.selectedService"
						@click="handleMasterSelect(master)"
					>
						<UAvatar
							:text="
								master.name
									.split(' ')
									.map((n) => n[0])
									.join('')
							"
							size="lg"
						/>
						<div class="master-info">
							<h3 class="master-name">{{ master.name }}</h3>
							<p class="master-spec">{{ master.specialization }}</p>
							<div class="master-rating">
								<UIcon name="i-lucide-star" class="rating-icon" />
								<span>{{ master.rating }}</span>
								<span class="rating-count">({{ master.reviewCount }})</span>
							</div>
						</div>
					</button>
				</div>
			</section>

			<section class="showcase-section">
				<div class="section-header">
					<UIcon name="i-lucide-calendar" class="section-icon" />
					<h2 class="section-title">3. Date & Time Selection</h2>
				</div>

				<div class="date-picker">
					<UInput
						:model-value="selectedDate"
						type="date"
						:disabled="!state.selectedMaster"
						@update:model-value="handleDateChange"
					/>
				</div>

				<div class="slots-grid">
					<button
						v-for="slot in availableSlots"
						:key="slot.id"
						class="slot-button"
						:class="{
							'slot-available': slot.available,
							'slot-unavailable': !slot.available,
							'slot-selected': state.selectedTimeSlot?.id === slot.id
						}"
						:disabled="!slot.available || !state.selectedMaster"
						@click="handleSlotSelect(slot)"
					>
						{{ slot.time }}
					</button>
				</div>
			</section>

			<section class="showcase-section">
				<div class="section-header">
					<UIcon name="i-lucide-check-circle" class="section-icon" />
					<h2 class="section-title">4. Confirmation</h2>
				</div>

				<UCard v-if="state.selectedService && state.selectedMaster && state.selectedTimeSlot">
					<div class="confirmation-content">
						<div class="confirmation-row">
							<span class="confirmation-label">Услуга:</span>
							<span class="confirmation-value">{{ state.selectedService.name }}</span>
						</div>
						<div class="confirmation-row">
							<span class="confirmation-label">Мастер:</span>
							<span class="confirmation-value">{{ state.selectedMaster.name }}</span>
						</div>
						<div class="confirmation-row">
							<span class="confirmation-label">Дата:</span>
							<span class="confirmation-value">{{ state.selectedDate }}</span>
						</div>
						<div class="confirmation-row">
							<span class="confirmation-label">Время:</span>
							<span class="confirmation-value">{{ state.selectedTimeSlot.time }}</span>
						</div>
						<div class="confirmation-row">
							<span class="confirmation-label">Длительность:</span>
							<span class="confirmation-value">{{ state.selectedService.duration }} мин</span>
						</div>
						<div class="confirmation-row confirmation-total">
							<span class="confirmation-label">Итого:</span>
							<span class="confirmation-value">{{ state.selectedService.price }} ₽</span>
						</div>
					</div>

					<template #footer>
						<UButton color="primary" block @click="handleConfirm">Подтвердить бронирование</UButton>
					</template>
				</UCard>

				<div v-else class="empty-state">
					<UIcon name="i-lucide-calendar-check" class="empty-icon" />
					<p class="empty-text">Выберите услугу, мастера и время для бронирования</p>
				</div>
			</section>
		</div>
	</div>
</template>

<style scoped>
.showcase-container {
	min-height: 100vh;
	background-color: var(--color-background);
	padding: 24px 16px;
}

.showcase-header {
	text-align: center;
	margin-bottom: 32px;
}

.showcase-title {
	font-size: 28px;
	font-weight: 600;
	color: var(--color-cyan-50);
	margin-bottom: 8px;
	letter-spacing: var(--letter-spacing-heading);
}

.showcase-subtitle {
	font-size: 16px;
	color: var(--color-cyan-200);
	margin-bottom: 16px;
}

.showcase-grid {
	max-width: 1200px;
	margin: 0 auto;
	display: grid;
	gap: 32px;
}

.showcase-section {
	background-color: var(--color-surface);
	border-radius: var(--border-radius);
	padding: 24px;
	border: var(--border-width) solid var(--color-cyan-900);
}

.section-header {
	display: flex;
	align-items: center;
	gap: 12px;
	margin-bottom: 20px;
}

.section-icon {
	width: 24px;
	height: 24px;
	color: var(--color-cyan-500);
}

.section-title {
	font-size: 20px;
	font-weight: 600;
	color: var(--color-cyan-50);
}

.services-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
	gap: 12px;
}

.service-card {
	background-color: var(--color-cyan-950);
	border: var(--border-width) solid var(--color-cyan-800);
	border-radius: var(--border-radius);
	padding: 16px;
	cursor: pointer;
	transition: all var(--transition-duration) var(--transition-timing);
	text-align: left;
}

.service-card:hover {
	border-color: var(--color-cyan-600);
	background-color: var(--color-cyan-900);
}

.service-card-selected {
	border-color: var(--color-cyan-500);
	background-color: var(--color-cyan-900);
}

.service-info {
	margin-bottom: 12px;
}

.service-name {
	font-size: 16px;
	font-weight: 500;
	color: var(--color-cyan-50);
	margin-bottom: 4px;
}

.service-duration {
	font-size: 13px;
	color: var(--color-cyan-300);
}

.service-price {
	font-size: 18px;
	font-weight: 600;
	color: var(--color-cyan-400);
}

.masters-list {
	display: grid;
	gap: 12px;
}

.master-card {
	display: flex;
	align-items: center;
	gap: 16px;
	background-color: var(--color-cyan-950);
	border: var(--border-width) solid var(--color-cyan-800);
	border-radius: var(--border-radius);
	padding: 16px;
	cursor: pointer;
	transition: all var(--transition-duration) var(--transition-timing);
	text-align: left;
}

.master-card:hover:not(:disabled) {
	border-color: var(--color-cyan-600);
	background-color: var(--color-cyan-900);
}

.master-card:disabled {
	opacity: 0.5;
	cursor: not-allowed;
}

.master-card-selected {
	border-color: var(--color-cyan-500);
	background-color: var(--color-cyan-900);
}

.master-info {
	flex: 1;
}

.master-name {
	font-size: 16px;
	font-weight: 500;
	color: var(--color-cyan-50);
	margin-bottom: 4px;
}

.master-spec {
	font-size: 13px;
	color: var(--color-cyan-300);
	margin-bottom: 8px;
}

.master-rating {
	display: flex;
	align-items: center;
	gap: 4px;
	font-size: 14px;
	color: var(--color-cyan-400);
}

.rating-icon {
	width: 14px;
	height: 14px;
}

.rating-count {
	color: var(--color-cyan-300);
	font-size: 12px;
}

.date-picker {
	margin-bottom: 20px;
}

.slots-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
	gap: 8px;
}

.slot-button {
	padding: 12px;
	border-radius: var(--border-radius);
	font-size: 14px;
	font-weight: 500;
	cursor: pointer;
	transition: all var(--transition-duration) var(--transition-timing);
	border: var(--border-width) solid transparent;
}

.slot-available {
	background-color: var(--color-cyan-950);
	border-color: var(--color-cyan-800);
	color: var(--color-cyan-50);
}

.slot-available:hover:not(:disabled) {
	border-color: var(--color-cyan-600);
	background-color: var(--color-cyan-900);
}

.slot-unavailable {
	background-color: var(--color-gray-900);
	color: var(--color-gray-600);
	cursor: not-allowed;
	opacity: 0.5;
}

.slot-selected {
	border-color: var(--color-cyan-500);
	background-color: var(--color-cyan-800);
	color: var(--color-cyan-50);
}

.slot-button:disabled {
	cursor: not-allowed;
}

.confirmation-content {
	display: flex;
	flex-direction: column;
	gap: 12px;
}

.confirmation-row {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 8px 0;
	border-bottom: var(--border-width) solid var(--color-cyan-900);
}

.confirmation-row:last-child {
	border-bottom: none;
}

.confirmation-total {
	margin-top: 8px;
	padding-top: 16px;
	border-top: var(--border-width) solid var(--color-cyan-700);
	font-size: 18px;
	font-weight: 600;
}

.confirmation-label {
	color: var(--color-cyan-300);
	font-size: 14px;
}

.confirmation-value {
	color: var(--color-cyan-50);
	font-weight: 500;
}

.confirmation-total .confirmation-value {
	color: var(--color-cyan-400);
}

.empty-state {
	text-align: center;
	padding: 48px 24px;
}

.empty-icon {
	width: 64px;
	height: 64px;
	color: var(--color-cyan-700);
	margin: 0 auto 16px;
}

.empty-text {
	color: var(--color-cyan-300);
	font-size: 14px;
}
</style>
