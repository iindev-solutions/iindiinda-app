# Ayan MVP & Core UI Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement the "Cyber-Minimalist" Core UI and the Ayan (Taxi) MVP service.

**Architecture:**
- **Core UI:** Global theme configuration via Nuxt UI v4 and Tailwind CSS.
- **Ayan Service:** Nuxt 4 layer (`services/ayan`) providing the taxi-specific pages and logic.
- **Integration:** Use `useAPI` and `useAuth` composables for communication with the Laravel backend.

**Tech Stack:** Nuxt 4, @nuxt/ui v4, Tailwind CSS, TypeScript.

---

## Chunk 1: Core UI & Global Theming

**Files:**
- Modify: `frontend/tma-app/app.config.ts`
- Modify: `frontend/tma-app/tailwind.config.ts` (if exists) or Nuxt config.
- Create: `frontend/tma-app/app/assets/css/main.css`

- [ ] **Step 1: Update `app.config.ts` for Nuxt UI v4 theme**
  Implement the Cyber-Minimalist palette:
  - Primary: `cyan`
  - Gray: `slate` or `gray`
  - Custom colors for Level 0 (`#0a0c0e`) and Level 1 (`#161a1d`).

- [ ] **Step 2: Set global CSS variables for backgrounds and borders**
  In `main.css`:
  ```css
  :root {
    --bg-level-0: #0a0c0e;
    --bg-level-1: #161a1d;
    --border-color: #1f2937;
  }
  body {
    background-color: var(--bg-level-0);
    color: white;
  }
  ```

- [ ] **Step 3: Configure global border radius**
  Update Tailwind config or use global CSS to ensure `rounded-2xl` is the default for cards and buttons.

- [ ] **Step 4: Verify theme with a screenshot**
  Run `npm run dev` and verify that the background is `#0a0c0e` and primary elements are Cyan.

- [ ] **Step 5: Commit**
  `git add . && git commit -m "style: implement core cyber-minimalist theme"`

---

## Chunk 2: Ayan - Passenger Flow

**Files:**
- Create: `services/ayan/pages/create.vue`
- Create: `services/ayan/pages/my-order.vue`
- Modify: `frontend/tma-app/app/types/api.ts` (Verify types)

- [ ] **Step 1: Implement `create.vue` (Request Creation)**
  - Use `UForm` from Nuxt UI.
  - Fields: `pickup`, `destination`, `price`.
  - Action: `useAPI().post('/ayan/orders', data)`.
  - Add `useTg().hapticFeedback('impact')` on submit.

- [ ] **Step 2: Implement `my-order.vue` (Order Tracking)**
  - State handling: `searching` $\rightarrow$ `matched` $\rightarrow$ `arrived` $\rightarrow$ `on-trip` $\rightarrow$ `completed`.
  - Add polling logic: `setInterval` every 5s to call `GET /ayan/orders/me`.
  - Add "Cancel Order" button with confirmation.

- [ ] **Step 3: Implement transition/navigation**
  Ensure `create.vue` redirects to `my-order.vue` after successful submission.

- [ ] **Step 4: Verify Passenger flow with mock API**
  Test the UI transitions and form submission.

- [ ] **Step 5: Commit**
  `git add . && git commit -m "feat(ayan): implement passenger request and tracking flow"`

---

## Chunk 3: Ayan - Driver Flow

**Files:**
- Create: `services/ayan/pages/driver.vue`
- Create: `services/ayan/pages/orders.vue`
- Create: `services/ayan/pages/active-ride.vue`

- [ ] **Step 1: Implement `driver.vue` (Dashboard)**
  - Add availability toggle (switch).
  - Action: Call `POST /api/user/switch-role` with `{role: 'driver'}`.

- [ ] **Step 2: Implement `orders.vue` (Available Orders)**
  - Use `UList` or custom cards with `bg-level-1`.
  - Display: Pickup $\rightarrow$ Destination, Price.
  - Action: "Accept Order" $\rightarrow$ `POST /api/ayan/orders/{id}/accept`.

- [ ] **Step 3: Implement `active-ride.vue` (Ride Management)**
  - Action buttons: "I've Arrived", "Start Trip", "Complete Trip".
  - Each button calls the corresponding API endpoint.

- [ ] **Step 4: Verify Driver flow with mock API**
  Test the role switching and order acceptance lifecycle.

- [ ] **Step 5: Commit**
  `git add . && git commit -m "feat(ayan): implement driver dashboard and ride lifecycle"`

---

## Chunk 4: i18n & Final Polish

**Files:**
- Modify: `frontend/tma-app/i18n/locales/ru.json`
- Modify: `frontend/tma-app/i18n/locales/sah.json`
- Modify: All Ayan pages.

- [ ] **Step 1: Add Ayan translation keys**
  Define keys for all labels, placeholders, and buttons in both `ru.json` and `sah.json`.

- [ ] **Step 2: Replace hardcoded text**
  Use `t('ayan.xxx')` in all Ayan pages.

- [ ] **Step 3: Final UI Polish**
  - Ensure all buttons use `rounded-2xl`.
  - Verify haptic feedback on all primary actions.
  - Check dark mode consistency.

- [ ] **Step 4: Run typecheck and lint**
  Run `npm run typecheck` and `npm run lint`. Fix any errors.

- [ ] **Step 5: Final Commit**
  `git add . && git commit -m "polish(ayan): implement i18n and final UI refinements"`
