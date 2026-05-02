# AYAN Legal Pack Implementation Plan

> **For agentic workers:** REQUIRED: Use superpowers:subagent-driven-development (if subagents available) or superpowers:executing-plans to implement this plan. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a minimum Russian legal pack for AYAN and surface its links in user-facing app areas.

**Architecture:** Keep the implementation static and simple. Add one small reusable legal-links helper/component, three legal pages under Nuxt routes, and light integration into home/AYAN screens. Avoid checkbox gating or backend persistence in this slice.

**Tech Stack:** Nuxt 4, Vue 3, @nuxt/ui v4, vue-i18n, Vitest

---

### Task 1: Legal Link Helper

**Files:**
- Create: `frontend/app/utils/legal.ts`
- Create: `frontend/tests/unit/legal.test.ts`

- [ ] **Step 1: Write the failing test**
- [ ] **Step 2: Run test to verify it fails**
- [ ] **Step 3: Add minimal helper implementation**
- [ ] **Step 4: Run test to verify it passes**

### Task 2: Legal Pages And Links

**Files:**
- Create: `frontend/app/components/AppLegalLinks.vue`
- Create: `frontend/app/pages/legal/ayan-terms.vue`
- Create: `frontend/app/pages/legal/privacy.vue`
- Create: `frontend/app/pages/legal/ayan-safety.vue`
- Modify: `frontend/app/pages/index.vue`
- Modify: `frontend/services/ayan/app/components/AyanAccessState.vue`
- Modify: `frontend/services/ayan/app/pages/ayan/index.vue`
- Modify: `frontend/i18n/locales/ru.json`
- Modify: `frontend/i18n/locales/sah.json`

- [ ] **Step 1: Add user-facing Russian legal content to i18n**
- [ ] **Step 2: Build legal pages from that content**
- [ ] **Step 3: Add reusable legal links component**
- [ ] **Step 4: Integrate links into home and AYAN surfaces**

### Task 3: Verification

**Files:**
- Modify: none unless verification reveals issues

- [ ] **Step 1: Run `npm run test`**
- [ ] **Step 2: Run `npm run lint`**
- [ ] **Step 3: Run `npm run typecheck`**
- [ ] **Step 4: Run `npx nuxt build --preset github_pages`**
