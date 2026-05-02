# AYAN Entry Polish Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restore reliable detail-page back navigation, replace the trip price stepper with a normal price field, and use a Nuxt UI calendar that blocks past dates.

**Architecture:** Keep the change minimal and local to AYAN UI. Add small pure helpers for price input sanitizing/parsing and back-button display logic so the risky behavior is testable without full component tests. Then wire those helpers into the existing slideover and detail pages.

**Tech Stack:** Nuxt 4, Vue 3, @nuxt/ui v4, Vitest

---

### Task 1: Testable Helpers

**Files:**
- Create: `frontend/services/ayan/app/utils/create-form.ts`
- Create: `frontend/app/utils/back-button.ts`
- Test: `frontend/tests/unit/ayanCreateForm.test.ts`
- Test: `frontend/tests/unit/backButton.test.ts`

- [ ] **Step 1: Write the failing tests**
- [ ] **Step 2: Run tests to verify they fail**
- [ ] **Step 3: Add minimal helper implementations**
- [ ] **Step 4: Run tests to verify they pass**

### Task 2: AYAN Create Form UI

**Files:**
- Modify: `frontend/services/ayan/app/components/AyanCreateSlideover.vue`
- Test: `frontend/tests/unit/ayanCreateForm.test.ts`

- [ ] **Step 1: Replace price stepper with text input using helper parsing**
- [ ] **Step 2: Replace native date input with `UCalendar` popover and block past dates**
- [ ] **Step 3: Keep payload/validation behavior minimal and correct**
- [ ] **Step 4: Re-run tests and lint/typecheck**

### Task 3: Detail Page Back Navigation

**Files:**
- Modify: `frontend/app/components/BackButton.vue`
- Modify: `frontend/services/ayan/app/pages/ayan/trip/[id].vue`
- Modify: `frontend/services/ayan/app/pages/ayan/request/[id].vue`
- Test: `frontend/tests/unit/backButton.test.ts`

- [ ] **Step 1: Keep Telegram back button and allow always-visible UI back button**
- [ ] **Step 2: Force visible back button on AYAN detail pages**
- [ ] **Step 3: Re-run tests and lint/typecheck**

### Task 4: Verification

**Files:**
- Modify: none unless verification reveals issues

- [ ] **Step 1: Run `npm run test`**
- [ ] **Step 2: Run `npm run lint`**
- [ ] **Step 3: Run `npm run typecheck`**
- [ ] **Step 4: Run `npx nuxt build --preset github_pages`**
