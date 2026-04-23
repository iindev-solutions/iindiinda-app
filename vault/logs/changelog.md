# Changelog — iindev-vault

> Формат: `YYYY-MM-DD HH:MM`. Актуальные записи. Архив: changelog-archive.md

---

## 2026-04-23 — AYAN Auth Hardening + Push

### Что сделано
- Добавлен и закоммичен hardening slice `755f7c6` `fix(ayan): enforce auth and response rules`
- `git push origin front/ayan` выполнен успешно
- Backend локально усилен:
  - signed Telegram `initData` parsing вместо простого blind parse
  - `init_data=test` только для `local/testing`
  - role/owner enforcement для AYAN create/respond/list responses
  - duplicate/closed response guards
  - single accepted response guard
- Frontend AYAN выровнен под новые backend rules:
  - role-aware create UI
  - non-owner detail pages больше не вызывают owner-only `/responses`
- `vault/wiki/services/ayan/api-contract.md` обновлён под live backend surface

### Verified
- `git push origin front/ayan` ✅
- `frontend: npm run typecheck` ✅
- `frontend: npm run lint` ✅
- `frontend: npm run test` ✅

### Blocked
- `ssh iind-vps` / `ssh root@89.22.226.34` ❌
- Симптом: SSH handshake проходит, затем сервер закрывает соединение: `Connection closed by 89.22.226.34 port 22`
- Из-за этого не выполнены:
  - `git -C /var/www/iind-app/backend pull --ff-only`
  - backend phpunit на VPS для нового hardening commit

### Next
- Восстановить SSH доступ к `iind-vps`
- На VPS сделать `git pull` в `/var/www/iind-app/backend`
- На VPS прогнать `AuthApiTest`, `AyanAuthTest`, `AyanPersistenceTest`
- После remote green обновить `vault` ещё раз и зафиксировать deploy verification commit

## 2026-04-23 — GitHub Pages Live + AYAN VPS Smoke

### Что сделано
- Повторно проверен GitHub Pages deploy для `gh-pages`
- Подтверждено, что `https://iindev-solutions.github.io/iindiinda-app/` уже live
- Прогнан direct smoke против VPS backend через реальные AYAN endpoints с двумя synthetic Telegram payload users
- Обновлены `vault/sprint.md`, `vault/resume-plan.md`, `vault/CODE_MAP.md` под новый stop point

### Verified
- `HEAD https://iindev-solutions.github.io/iindiinda-app/` → `200` ✅
- `HEAD https://iindev-solutions.github.io/iindiinda-app/ayan` → `200` ✅
- rebased asset `/iindiinda-app/assets/entry.DKuJVqy4.css` → `200` ✅
- `POST /api/auth/telegram` with synthetic `init_data` for 2 users ✅
- `POST /api/ayan/trips` ✅
- `POST /api/ayan/requests` ✅
- `POST /api/ayan/trips/{id}/responses` ✅
- `POST /api/ayan/requests/{id}/responses` ✅
- `PATCH /api/ayan/responses/{id}` accept flow ✅
- `GET /api/ayan/my/trips`, `/my/requests`, `/my/responses` ✅

### Важно
- GitHub Pages propagation/source blocker больше не актуален: deploy уже live
- Main next step сместился с deploy verification на реальный browser UI flow против VPS backend
- В generated HTML публичный ключ `devInitData` всё ещё сериализуется как пустая строка; deploy build нельзя собирать с непустым `NUXT_PUBLIC_DEV_INIT_DATA`

## 2026-04-22 — GitHub Pages Deploy Attempt

### Что сделано
- Собран static frontend output из `frontend/` через `npx nuxt build --preset github_pages`
- Выявлен deploy nuance: build с `NUXT_APP_BASE_URL=/iindiinda-app/` ломает Nuxt prerender, потому что crawler идёт в `/`, а Nitro mount'ит app под repo base path
- Рабочий временный flow: build с `NUXT_APP_BASE_URL=/`, затем rebase generated HTML/CSS под repo path `/iindiinda-app/`
- Из generated HTML убран публичный `devInitData:"test"` fallback для deploy build
- Содержимое `frontend/.output/public` опубликовано в новую ветку `gh-pages` отдельным temp-repo commit'ом `bff6aa5`

### Verified
- `npx nuxt build --preset github_pages` with `NUXT_APP_BASE_URL=/` ✅
- rebased output содержит `/iindiinda-app/assets/*` и `app.baseURL:"/iindiinda-app/"` ✅
- `git push -u origin gh-pages` ✅

### Важно
- Expected URL: `https://iindev-solutions.github.io/iindiinda-app/`
- На момент последней проверки URL ещё отвечал `404`
- Причина вне локального build pipeline: либо GitHub Pages source ещё не включён на repo, либо deploy не успел подняться

## 2026-04-22 — Frontend AYAN Real API Switch

### Что сделано
- `frontend/app/config/api.config.ts`: `USE_MOCK_API = false`
- `frontend/useAuth.ts` переведён в TMA-first поведение: browser mode больше не пытается автоматически запускать старый Telegram OAuth flow без backend support
- `frontend/nuxt.config.ts` теперь знает `public.telegramBotId`
- `.env.example` дополнен `NUXT_PUBLIC_TELEGRAM_BOT_ID`
- `vault/sprint.md`, `vault/resume-plan.md`, `vault/CODE_MAP.md` обновлены под новый stop point

### Verified
- `npm run typecheck` ✅
- `npm run lint` ✅

### Важно
- AYAN composables уже ходят в real API
- Browser auth пока intentionally урезан до TMA-only path до появления real OAuth / Telegram verification end-to-end
- Следующий шаг: пройти UI flow против VPS backend и затем закоммитить frontend integration пакет

### Follow-up
- Local frontend dev against VPS now uses `frontend/.env`:
  - `NUXT_PUBLIC_API_BASE=http://89.22.226.34/api`
  - optional `NUXT_PUBLIC_DEV_INIT_DATA=test` для browser-only smoke login без Telegram

## 2026-04-22 — VPS Backend Bring-Up + AYAN Persistence

### Что сделано
- Поднят реальный Laravel runtime для `backend/` на VPS (`/var/www/iind-app/backend`) под `Nginx + PHP-FPM + MySQL`
- Восстановлен Laravel base в `backend/`: `artisan`, `composer.json`, `bootstrap/`, `config/`, `routes/console.php`, `resources/`, `tests/`, `storage/`
- Настроен Nginx на `backend/public`, health endpoint начал отвечать по HTTP
- Установлен `laravel/sanctum`, добавлена миграция `personal_access_tokens`
- `AuthController` переведён с `mock_token_*` на реальный Sanctum token issuance
- `UserController` переведён на authenticated user вместо hardcoded mock payload
- `TripController`, `RequestController`, `ResponseController`, `MyController` переведены с sample arrays на MySQL persistence
- Добавлен `ForceJsonResponse` middleware, чтобы guest protected API давал JSON `401`, а не HTML redirect / `Route [login] not defined`
- Исправлены backend migrations под реальный Laravel/MySQL runtime:
  - `unsignedDecimal()` → `decimal()` в `users`
  - убран DB-level `CHECK` constraint из `responses`, несовместимый с текущим MySQL FK setup
- Добавлены backend feature tests:
  - `backend/tests/Feature/AuthApiTest.php`
  - `backend/tests/Feature/AyanAuthTest.php`
  - `backend/tests/Feature/AyanPersistenceTest.php`
- Обновлены `vault/sprint.md`, `vault/resume-plan.md`, `vault/CODE_MAP.md` под новый stop point

### Verified
- `./vendor/bin/phpunit tests/Feature/AuthApiTest.php tests/Feature/AyanAuthTest.php /var/www/iind-app/backend/tests/Feature/AyanPersistenceTest.php` ✅ (`6 tests, 69 assertions`)
- `curl http://89.22.226.34/api/health` ✅ (`200`)
- `curl http://89.22.226.34/api/ayan/trips` ✅ (`401` JSON guest auth)
- `POST /api/auth/telegram` → real Sanctum token ✅
- `GET /api/user` with bearer token ✅

### Важно
- Telegram `initData` verification пока ещё не production-grade: есть stub `init_data = test` + простой parse payload
- Frontend всё ещё на `USE_MOCK_API = true`, integration пакет ещё не начат
- Изменения пока не зафиксированы git commit'ом; VPS и локальный repo синхронизированы файлово, но branch ещё dirty

### Next
- Закоммитить и запушить Laravel runtime + backend fixes
- Переключить фронт `mock → real` и пройти AYAN flow против VPS backend
- Отдельным пакетом закрыть настоящую Telegram `initData` verification

## 2026-04-22 — Deep Audit + Resume Plan

### Что сделано
- Проведён глубокий аудит `vault`, frontend и backend для восстановления stop point
- Создан `vault/resume-plan.md` — единая точка входа: где остановились, что блокирует, что делать дальше
- Обновлён `vault/sprint.md` — добавлены `Resume Point`, реальные блокеры и список resume files
- Обновлён `vault/master_index.md` — добавлена ссылка на resume-plan, исправлен счётчик задач спринта
- Обновлён `vault/CODE_MAP.md` — добавлен `AppBottomNav.vue`, зафиксирован факт что backend всё ещё на old `orders` API, а `app.vue` loader overlay отключён

### Ключевой вывод
- Мы остановились после почти готового AYAN frontend на mock API
- Следующий реальный этап: заменить backend `/ayan/orders/*` на contract-aligned AYAN API (`trips`, `requests`, `responses`, `my/*`)

### Verified
- Аудит docs/code sync ✅

---

## 2026-04-22 — Vitest Setup Baseline

### Что сделано
- Завершён начатый setup `vitest`
- Добавлены scripts: `test`, `test:watch`
- Добавлен `frontend/vitest.config.ts`
- Добавлен smoke test `frontend/tests/unit/validators.test.ts`
- Обновлены `vault/resume-plan.md`, `vault/sprint.md`, `vault/CODE_MAP.md` под новый stop point
- Текущий уровень готовности: baseline для plain TS unit tests, не полный Nuxt/composable test harness

### Verified
- `npm run test` ✅
- `npm run typecheck` ✅
- `npm run lint -- tests/unit/validators.test.ts vitest.config.ts` ✅ (по факту запускает `eslint .` в frontend)

---

## 2026-04-22 — Backend AYAN Contract Skeleton

### Что сделано
- Добавлен базовый Laravel-style skeleton под новый AYAN contract
- Добавлены модели: `User`, `Trip`, `AyanRequest`, `AyanResponse`
- Добавлены миграции: `users`, `trips`, `requests`, `responses`
- Добавлены новые controllers: `TripController`, `RequestController`, `ResponseController`, `MyController`
- `backend/routes/api.php` переведён с old `/ayan/orders/*` на новый набор `trips / requests / responses / my/*`
- Исправлены namespaces/imports в `AuthController`, `UserController`, добавлен базовый `Controller.php`

### Важно
- Это пока **contract-aligned skeleton**, не подтверждённый рабочим Laravel runtime
- В текущей среде нет `php`, `composer`, `docker`, поэтому backend нельзя было прогнать или промигрировать

### Next
- Поднять реальный Laravel runtime
- Прогнать миграции
- Заменить mock payloads на persistence и реальную auth-логику

### Docs
- Добавлен `vault/wiki/services/ayan/backend-bringup.md` — пошаговый runtime checklist для первого реального запуска backend

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
