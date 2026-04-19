# Changelog — iindev-vault

> Формат: `YYYY-MM-DD HH:MM`. Актуальные записи. Архив: changelog-archive.md

---

## 2026-04-19 — AYAN Slideover + Color Fix

### Slideover: Merge Create Forms

**Проблема:** Две отдельные страницы (`create-trip.vue`, `create-request.vue`) с почти идентичным кодом. Две кнопки на ленте. Навигация на отдельную страницу = задержка.

**Решение:**
- Создан `AyanCreateSlideover.vue` — единый bottom-slideover с pill-табами (Поездка/Запрос)
- `side="bottom"` + `rounded-t-2xl` + `max-h-[85dvh]` — мобильный sheet
- Общие поля: откуда, куда, дата, время
- `formType === 'trip'` → места + цена + комментарий
- `formType === 'request'` → комментарий (description)
- После сабмита → slideover закрывается, форма сбрасывается
- Одна кнопка на ленте вместо двух → открывает slideover
- Удалены `create-trip.vue`, `create-request.vue`

### Color Fix: cyan/gray → primary/neutral

**Проблема:** `color="cyan"` / `color="gray"` — не валидные Nuxt UI v4 prop values. TS ошибки + красная ui.vue страница.

**Решение:**
- `color="cyan"` → `color="primary"` (primary=cyan в app.config)
- `color="gray"` → `color="neutral"` (neutral=gray в app.config)
- `color="cyan"` (rejected badge) → `color="error"` (семантически верно)
- `color="cyan"` (progress) → `color="success"` (семантически верно)
- Затронуто: `BackButton.vue`, `ErrorMessage.vue`, `ui.vue`
- **typecheck + lint: 0 ошибок** (впервые чисто)

### i18n
- Добавлены `ayan.create.ride/request/from/to/date/time` (ru + sah)

### Verified
- typecheck ✅ lint ✅

---

## 2026-04-19 — Forms, Validation, Performance

### Forms: Error State + Layout (create-request, create-trip)

**Проблема:** UFormField не показывал error-состояние (красный ring) на инпутах. Причина — `ui.theme.colors: ['cyan', 'gray']` в nuxt.config.ts ограничивала палитру Nuxt UI, убирая `error`/`warning`/`success`/`info`/`secondary` цвета. FormField передаёт `color="error"` инпуту, но без этих цветов в теме — ring не применялся.

**Фикс:**
- Убрана ограниченная палитра `ui.theme.colors` из `nuxt.config.ts` (закомментирована)
- Удалён дубликат `frontend/app.config.ts` (конфликтовал с `frontend/app/app.config.ts`)
- Все UI-оверрайды в одном файле: `frontend/app/app.config.ts` (colors: primary=cyan, neutral=gray)
- Формы: `eager-validation` на обязательных полях — ошибка видна сразу после первого взаимодействия
- Формы: `class="w-full"` на UInput/UTextarea/UInputNumber — инпуты растягиваются на всю ширину
- Формы: `:label` на UFormField — подписи полей вместо placeholder-only
- Формы: `FormError` + `FormSubmitEvent` типы из `@nuxt/ui`
- Формы: дата/время и места/цена — `grid grid-cols-2 gap-3`
- i18n: добавлены `commentPlaceholder`, `time` ключи (ru + sah)

### Performance: Nuxt 4 Best Practices

**Что сделано:**

1. **`useLoadingIndicator().isLoading`** → overlay спиннер в `app.vue` с `backdrop-blur-sm` + `<Transition name="loader-fade">`. Показывается при навигации между страницами, пока данные грузятся.

2. **`useLazyAsyncData`** вместо `await useAsyncData` на всех AYAN страницах (`index.vue`, `trip/[id].vue`, `request/[id].vue`). Навигация мгновенная, данные подгружаются после рендера.

3. **`{ deep: false }`** в `useLazyAsyncData` на `index.vue` — списки не глубоко реактивные (экономия на proxy).

4. **`definePageMeta({ lazy: true })`** на AYAN дочерних страницах — бандлы страниц подгружаются lazy, не блокируют переход.

5. **`prefetchOn: { visibility: true, interaction: true }`** в `experimental.defaults.nuxtLink` — NuxtLink префетчит при видимости/взаимодействии, не грузит всё заранее.

6. **`pageTransition` убран** — конфликтует с `lazy: true` (Vue warning: "non-element root node"). Overlay loader обеспечивает визуальный фидбек вместо page transition.

**Слои загрузки теперь:**
- `spa-loader.html` — первый холодный рендер (пока JS бандл грузится)
- `NuxtLoadingIndicator` — тонкая полоска сверху при навигации
- `useLoadingIndicator().isLoading` → overlay спиннер (полноэкранный)
- `useLazyAsyncData` — данные не блокируют навигацию
- `lazy: true` — бандлы подгружаются параллельно

### CSS
- `main.css`: `.loader-fade-enter/leave` — 200ms fade для overlay
- `main.css`: `.page-enter/leave` удалены (pageTransition убран)

### TS (pre-existing)
- `color="cyan"` / `color="gray"` TS errors в BackButton, ErrorMessage, ui.vue — Nuxt UI не включает кастомные цвета в union type. Рантайм работает. TODO: исправить типы.

### Verified
- lint ✅ (typecheck: pre-existing cyan/gray TS errors)

---

## 2026-04-19 — Task 1.3: Frontend AYAN Structure ✅

### Added
- `services/ayan/app/types/ayan.ts` — типы AyanTrip, AyanRequest, AyanResponse, DTO (по API контракту)
- `services/ayan/app/config/ayanMock.ts` — mock генерация trips/requests/responses + useState store для поиска по ID
- `services/ayan/app/composables/useAyanTrips.ts` — CRUD поездок через useAPI (fetchTrips, fetchTrip, createTrip, updateTrip)
- `services/ayan/app/composables/useAyanRequests.ts` — CRUD запросов (fetchRequests, fetchRequest, createRequest)
- `services/ayan/app/composables/useAyanResponses.ts` — отклики (fetch/create/cancel)
- `services/ayan/app/composables/useAyanMy.ts` — мои данные
- `services/ayan/app/pages/ayan.vue` — parent wrapper
- `services/ayan/app/pages/ayan/index.vue` — лента поездок/запросов/мои с табами
- `services/ayan/app/pages/ayan/create-trip.vue` — форма создания поездки
- `services/ayan/app/pages/ayan/create-request.vue` — форма создания запроса
- `services/ayan/app/pages/ayan/trip/[id].vue` — детали поездки + отклик
- `services/ayan/app/pages/ayan/request/[id].vue` — детали запроса + отклик
- i18n: `ayan.validation.*`, `ayan.status.*`, `ayan.responses` (ru + sah)

### Changed (audit fixes)
- index.vue: UTabs `model-value` static → `:model-value="activeTab"` (reactive)
- index.vue: `onMounted` → `useAsyncData` (AGENTS.md rule)
- index.vue: добавлена вкладка "Мои" через `useAyanMy`
- useAyanTrips: `fetchTrip(id)` mock теперь ищет по ID в useState store, не генерирует рандом
- useAyanRequests: добавлен `fetchRequest(id)` — 详情 страница больше не грузит все запросы
- useAyanTrips: `updateTrip` mock сохраняет данные существующей поездки
- trip/[id].vue, request/[id].vue: `onMounted` → `useAsyncData`
- trip/[id].vue, request/[id].vue: hardcoded "Отклики" → `t('ayan.responses')`
- Types: `AyanTripCreate.comment`, `AyanRequestCreate.description`/`time` → `string` (не `null`)
- i18n: `ayan.respond.messagePlaceholder` → нейтральное "Напишите сообщение..." (не "водителю")
- useAPI.ts: добавлен `patch` метод, убраны старые AYAN orders mock handlers
- mockData.ts: удалён мёртвый код (AyaniOrder, generateMockOrders, дублирующиеся константы)

### Design decisions
- Подход C: AYAN composables в services/ayan, используют корневой useAPI для HTTP
- Типы строго по API контракту (trips/requests/responses, не orders)
- Nuxt UI: UForm+UFormField+UInput+UInputNumber+UTextarea+UCard+UTabs+UButton
- Mock store: useState для стабильных ID при детальном просмотре

### Verified
- typecheck ✅ lint ✅

---

## 2026-04-19 14:00 — Vault Audit & Restructure

### Проблема
3 дублирующих AI конфига (vault/CLAUDE.md, vault/AGENTS.md, vault/.opencode/agents.md). Нет инвентаря кода. WikiLinks — шум для ИИ. Церемониальный workflow.

### Изменения
- **Удалены** vault/CLAUDE.md, vault/AGENTS.md, vault/.opencode/agents.md
- **Создан** vault/CODE_MAP.md — полный инвентарь кода (composables, components, pages, types, utils, config, plugins, middleware, layouts, service layers, backend, API status)
- **Обновлён** root AGENTS.md — единый конфиг, упрощённый workflow (sprint → CODE_MAP → wiki → код), без церемоний
- **Обновлён** vault/master_index.md — WikiLinks → обычные пути, добавлен CODE_MAP
- **Обновлён** vault/sprint.md — WikiLinks убраны, статусы: TODO/IN_PROGRESS/DONE/BLOCKED
- **Создан** vault/logs/changelog-archive.md — старые записи перенесены

### Результат
Один AGENTS.md = все правила. CODE_MAP.md = где что в коде. ИИ читает ~50 строк конфига вместо 3 файлов.

---

## 2026-04-19 — Vault Cleanup & Sprint Setup

### Deleted (from /raw — Phase 0 отработан)
- `vault/raw/foundation-audit.md`, `foundation-spec.md`, `foundation-phase-0-spec.md`, `SPEC.md`, `ayan-api-contract.md`

### Moved (raw → wiki)
- `raw/SPEC.md` → `wiki/architecture/roadmap.md`
- `raw/ayan-api-contract.md` → `wiki/services/ayan/api-contract.md`

### Created
- `vault/sprint.md` — Phase 1 AYAN MVP, 9 задач
- `vault/wiki/services/ayan/` — директория

---

## 2026-04-19 — Foundation Phase 0 Complete ✅

### Added
- useAuth.ts — TMA initData + OAuth, unified login
- auth.ts middleware — route protection
- init.ts plugin — Telegram SDK + auto-login
- auth/callback.vue — OAuth callback
- useGlobalError.ts — global error state
- error-handler.ts — global handler
- validators.ts — 8 validators
- forms.ts — form types
- useStorage.ts — localStorage wrapper
- useNetwork.ts — online/offline
- ui.ts — UI types
- sah.json — Yakut language

### Verified
- typecheck ✅ lint ✅

---

## 2026-04-19 — Foundation Phase 0 Spec

- vault/raw/foundation-phase-0-spec.md — спецификация Phase 0
- vault/wiki/architecture/auth-flow.md — дизайн авторизации
- 10 критичных проблем найдено, план реализации (0.7–0.10)
