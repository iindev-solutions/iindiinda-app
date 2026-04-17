# IIND.TAL - Booking Service

**TAL** (from Sakha "Tal" - choose/select) is an online appointment booking system for beauty salons, barbers, and medical services with visual time-slot calendar.

## Features

- Service selection with pricing
- Master/specialist selection with ratings
- Visual calendar with available time slots
- Booking confirmation flow
- Real-time availability updates

## Architecture

### State Management

State is managed using native `useState` composables in `useTalStore.ts`:

```typescript
interface TalState {
	selectedService: Service | null
	selectedMaster: Master | null
	selectedDate: string | null
	selectedTimeSlot: TimeSlot | null
	availableSlots: TimeSlot[]
	masters: Master[]
	services: Service[]
	currentBooking: Booking | null
	isLoading: boolean
}
```

### API Endpoints

All API calls use the `useAPI` wrapper from `app/composables/useAPI.ts` to ensure Telegram InitData headers are attached.

#### 1. Fetch Services

```typescript
GET /api/tal/services

Response: Service[]
{
  id: string
  name: string
  duration: number  // minutes
  price: number     // rubles
}
```

#### 2. Fetch Masters

```typescript
GET /api/tal/masters?serviceId={serviceId}

Response: Master[]
{
  id: string
  name: string
  avatar?: string
  specialization: string
  rating: number
  reviewCount: number
}
```

#### 3. Fetch Available Slots

```typescript
GET /api/tal/slots?masterId={masterId}&date={YYYY-MM-DD}

Response: TimeSlot[]
{
  id: string
  time: string      // HH:MM format
  date: string      // YYYY-MM-DD
  available: boolean
}
```

#### 4. Create Booking

```typescript
POST /api/tal/bookings

Request Body:
{
  serviceId: string
  masterId: string
  timeSlotId: string
  date: string      // YYYY-MM-DD
}

Response: Booking
{
  serviceId: string
  masterId: string
  timeSlotId: string
  date: string
  time: string
  status: 'pending' | 'confirmed' | 'cancelled'
}
```

#### 5. Cancel Booking

```typescript
DELETE /api/tal/bookings/{bookingId}

Response: void
```

## Design System

All components use CSS variables from `app/assets/css/main.css`:

- Primary accent: `--color-cyan-500` (#5edac6)
- Background: `--color-background` (#0a0c0e)
- Surface: `--color-surface` (#0f1113)
- Text: `--color-cyan-50` (#eff3f5)
- Borders: `--color-cyan-800`, `--color-cyan-900`

## Pages

### `/tal-showcase`

Visual showcase demonstrating all booking flow states:

1. Service selection
2. Master selection
3. Date & time slot selection
4. Booking confirmation

## Usage Example

```vue
<script setup lang="ts">
import { useTalStore } from '../composables/useTalStore'
import { useTalAPI } from '../composables/useTalAPI'

const store = useTalStore()
const api = useTalAPI()

// Load services
const services = await api.fetchServices()

// Select service
store.selectService(services[0])

// Load masters for selected service
const masters = await api.fetchMasters(store.state.value.selectedService.id)

// Select master
store.selectMaster(masters[0])

// Load available slots
const slots = await api.fetchAvailableSlots(store.state.value.selectedMaster.id, '2026-02-20')

// Select slot
store.selectTimeSlot(slots[0])

// Create booking
const booking = await api.createBooking({
	serviceId: store.state.value.selectedService.id,
	masterId: store.state.value.selectedMaster.id,
	timeSlotId: store.state.value.selectedTimeSlot.id,
	date: store.state.value.selectedDate
})
</script>
```

## Localization

All UI strings must use `$t()` helper for Sakha and Russian translations.

Example keys needed in `locales/ru.json` and `locales/sakha.json`:

```json
{
	"tal": {
		"title": "Бронирование",
		"selectService": "Выберите услугу",
		"selectMaster": "Выберите мастера",
		"selectTime": "Выберите время",
		"confirm": "Подтвердить",
		"cancel": "Отменить",
		"duration": "Длительность",
		"price": "Цена",
		"rating": "Рейтинг",
		"reviews": "отзывов"
	}
}
```
