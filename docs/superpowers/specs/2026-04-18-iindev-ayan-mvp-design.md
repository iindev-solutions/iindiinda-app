# Design Spec: iindev Ayan (Taxi) MVP & Core UI

**Date:** 2026-04-18
**Status:** Draft (Approved by User)
**Priority:** Highest

## 1. Overview
Implementation of the "Ayan" (Taxi) service MVP within the iindiinda platform. The service uses a passenger-proposed pricing model. This project also establishes the "Core Design System" for all future services.

## 2. Core Design System (The "Anti-Terrible" Guide)

### 2.1 Visual Identity
- **Style:** Cyber-Minimalist / Premium Telegram Native.
- **Primary Color:** `#5edac6` (Cyan).
- **Color Palette:**
  - Background (Level 0): `#0a0c0e`
  - Surface (Level 1): `#161a1d`
  - Borders: `#1f2937` (Gray-800)
- **Typography:** 
  - Headings: Geist Sans
  - Body: Inter
- **Corner Radius:** `rounded-2xl` (16px) for all primary containers.

### 2.2 Component Guidelines (Nuxt UI v4)
- **Buttons:**
  - Primary: Solid Cyan, dark text.
  - Secondary: Outline/Ghost Cyan.
- **Inputs:** Surface background, cyan focus glow.
- **Cards:** Bordered surfaces, no heavy shadows.
- **Navigation:** Bottom tab bar with `useTg().hapticFeedback()`.

## 3. Ayan (Taxi) MVP Specification

### 3.1 Passenger Flow
- **Screen: Request Creation (`/ayan/create`)**
  - Fields: Pickup Location, Destination, Price.
  - Action: "Find Driver" $\rightarrow$ API `POST /api/ayan/orders`.
- **Screen: Order Tracking (`/ayan/my-order`)**
  - States: 
    - `Searching`: Pulsing cyan animation. (Expires after 10 mins).
    - `Matched`: Display driver details (name, rating, ETA).
    - `Arrived`: Notification that driver is at pickup point.
    - `OnTrip`: Real-time status that trip has started.
    - `Completed`: Summary of trip and payment confirmation.
  - Action: "Cancel Order" $\rightarrow$ API `POST /api/ayan/orders/{id}/cancel`.

### 3.2 Driver Flow
- **Screen: Driver Dashboard (`/ayan/driver`)**
  - Toggle: Availability switch.
  - Action: Switch role to `driver` via API `POST /api/user/switch-role`.
- **Screen: Available Orders (`/ayan/orders`)**
  - View: List of active passenger requests.
  - Card: Pickup $\rightarrow$ Destination + Price.
  - Action: "Accept Order" $\rightarrow$ API `POST /api/ayan/orders/{id}/accept`.
- **Screen: Active Ride (`/ayan/active-ride`)**
  - Actions: 
    - "I've Arrived" $\rightarrow$ API `POST /api/ayan/orders/{id}/arrive`.
    - "Start Trip" $\rightarrow$ API `POST /api/ayan/orders/{id}/start`.
    - "Complete Trip" $\rightarrow$ API `POST /api/ayan/orders/{id}/complete`.

### 3.3 Technical Implementation

#### Backend Integration (Laravel)
- **Auth:** All requests must include `X-Telegram-Init-Data` or `Authorization: Bearer {token}`.
- **API Endpoints:**
  - `POST /api/auth/telegram`: Initial authentication.
  - `POST /api/user/switch-role`: Change between passenger and driver.
  - `POST /api/ayan/orders`: Create taxi request.
  - `GET /api/ayan/orders`: List available orders (for drivers).
  - `POST /api/ayan/orders/{id}/accept`: Accept order.
  - `POST /api/ayan/orders/{id}/arrive`: Mark as arrived at pickup.
  - `POST /api/ayan/orders/{id}/start`: Start the trip.
  - `POST /api/ayan/orders/{id}/complete`: Complete the trip and confirm payment.
  - `POST /api/ayan/orders/{id}/cancel`: Cancel order.

#### Frontend Implementation (Nuxt 4)
- **Layer:** `services/ayan`.
- **State Management:** Use composables (`useAuth`, `useAPI`) for global state.
- **Updates:** Polling mechanism (3-5s) for order status updates.
- **i18n:** Replace all hardcoded strings with `t('ayan.key')` using `ru.json` and `sah.json`.

## 4. Success Criteria
- User can successfully authenticate via Telegram.
- Passenger can create an order with a price.
- Driver can see the order and accept it.
- UI follows the new "Cyber-Minimalist" guide consistently.
- All screens are translated into Russian and Yakut.
