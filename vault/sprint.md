# Sprint — Phase 1 AYAN MVP

> Roadmap: vault/wiki/architecture/roadmap.md
> API: vault/wiki/services/ayan/api-contract.md
> Code: vault/CODE_MAP.md

---

## Phase 1: AYAN MVP

Начало: 2026-04-19
Цель: Рабочий MVP попуток (создание поездки/запроса → лента → отклик → контакт)

### Resume Point — 2026-04-23

- **Где остановились:** локально и в `origin/front/ayan` уже есть hardening commit `755f7c6` с real Telegram signed `initData` parsing, AYAN role/owner rules и frontend alignment под owner-only responses
- **Главный блокер:** с текущей машины нет живого SSH session на `iind-vps` — сервер закрывает соединение сразу после handshake, поэтому `git pull` и backend phpunit на VPS пока не прогнаны на новом commit
- **Последнее завершённое действие:** `git push origin front/ayan` ✅, frontend verification локально зелёный: `typecheck`, `lint`, `vitest`
- **Продолжать с:** `vault/resume-plan.md`

### Current Reality

- Frontend Phase 1 = **real API wired for AYAN**, но browser auth flow ещё intentionally урезан до TMA-first режима
- Backend Phase 1 = **runtime-ready on VPS**: Laravel base восстановлен, Sanctum стоит, AYAN `trips / requests / responses / my/*` работают через MySQL persistence
- Local hardening patch already pushed to branch: signed Telegram auth check + role/owner enforcement + frontend AYAN role alignment
- GitHub Pages frontend = **live** on `https://iindev-solutions.github.io/iindiinda-app/`
- Direct AYAN API smoke against VPS = **green** for auth + create/list/respond/accept + `my/*`
- Frontend testing base = **baseline ready** (`vitest` smoke path работает для plain TS unit tests, не для Nuxt composables)
- Следующий реальный этап = восстановить SSH доступ к `iind-vps`, сделать `git pull`, прогнать backend feature tests и только потом продолжать следующий AYAN slice

### Задачи

| # | Задача | Статус | Блокеры |
|---|--------|--------|---------|
| 1.1 | Backend: миграции (users, trips, requests, responses) | DONE | — |
| 1.2 | Backend: модели + контроллеры AYAN | DONE* | real Telegram initData verification ещё stub |
| 1.3 | Frontend: структура AYAN (pages, composables, types) | DONE | — |
| 1.4 | Frontend: создание поездки/запроса (единый slideover + pill-табы) | DONE* | — |
| 1.5 | Frontend: лента поездок/запросов + табы + empty state | DONE* | — |
| 1.6 | Frontend: performance (lazy, useLazyAsyncData, loader overlay, prefetch) | DONE | — |
| 1.7 | Frontend: отклик + контакт (status, accept/reject, telegram link) | DONE | — |
| 1.8 | Frontend: фильтры по маршруту/дате | DONE | — |
| 1.9 | Frontend: Nuxt UI colors fix (cyan→primary, gray→neutral) | DONE | — |
| 1.10 | Integration: mock → real API | IN_PROGRESS | browser auth / real Telegram verification |
| 1.11 | QA: все flow работают E2E | TODO | 1.10 |

\* 1.4 и 1.5 — работают на mock API. При переключении на real API нужно проверить валидацию ответов.

Статусы: `TODO` `IN_PROGRESS` `DONE` `BLOCKED`

### Блокеры

- В текущей среде нет `php`, `composer`, `docker` — backend нельзя прогнать локально, только через VPS
- `ssh iind-vps` сейчас обрывается сразу после handshake (`Connection closed by 89.22.226.34 port 22`), поэтому deploy verification временно заблокирован
- Browser auth intentionally disabled until real OAuth / Telegram verification exists end-to-end
- `POST /api/auth/telegram` уже переведён на signed `initData` parsing, но production verification надо подтвердить phpunit/HTTP smoke на VPS
- `1.11 QA E2E` нельзя закрыть до реального frontend integration и проверки full flow

### Решения

- **Ограниченная палитра `ui.theme.colors`** — убрана. Вызывала отсутствие error/success/warning цветов
- **`pageTransition` убран** — конфликтует с `lazy: true` (Suspense fragment vs Transition один root)
- **`useLazyAsyncData`** вместо `useAsyncData` — навигация мгновенная, данные грузятся после
- **`deep: false`** для списков — shallow reactivity, экономит proxy overhead
- **Дубликат `app.config.ts`** — удалён корневой, всё в `frontend/app/app.config.ts`
- **Два create-страницы → один slideover** — AyanCreateSlideover с pill-табами (Поездка/Запрос), `side="bottom"`, `rounded-t-2xl`
- **Два кнопки → одна** — "Создать поездку" открывает slideover, тип выбирается внутри
- **`color="cyan"` → `color="primary"`** — везде, Nuxt UI v4 не поддерживает кастомные цвета в prop. primary=cyan, neutral=gray в app.config

---

## Frontend AYAN Mock-Ready ✅

Все основные фронтенд-задачи Phase 1 выполнены **в mock-режиме**. Дальше — backend и реальная интеграция:

### Blocked by Backend (1.1, 1.2)

- [x] 1.1 Backend: миграции
- [x] 1.2 Backend: модели + контроллеры
- [ ] 1.10 Mock → Real API (AYAN composables switched; full UI flow verification pending)
- [ ] 1.11 QA E2E (зависит от 1.10)

### Resume Files

- `vault/resume-plan.md`
- `vault/wiki/services/ayan/api-contract.md`
- `vault/wiki/services/ayan/backend-bringup.md`
- `backend/app/Http/Controllers/Ayan/TripController.php`
- `backend/app/Http/Controllers/Ayan/RequestController.php`
- `backend/app/Http/Controllers/Ayan/ResponseController.php`
- `backend/app/Http/Controllers/Ayan/MyController.php`
- `backend/app/Http/Middleware/ForceJsonResponse.php`
- `backend/app/Http/Controllers/Ayan/Concerns/SerializesAyanData.php`
- `backend/routes/api.php`
- `backend/app/Models/User.php`
- `backend/app/Models/Trip.php`
- `backend/app/Models/AyanRequest.php`
- `backend/app/Models/AyanResponse.php`
- `backend/bootstrap/app.php`
- `backend/config/auth.php`
- `backend/database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php`
- `backend/database/migrations/2026_04_22_000001_create_users_table.php`
- `backend/database/migrations/2026_04_22_000002_create_trips_table.php`
- `backend/database/migrations/2026_04_22_000003_create_requests_table.php`
- `backend/database/migrations/2026_04_22_000004_create_responses_table.php`
- `backend/tests/Feature/AuthApiTest.php`
- `backend/tests/Feature/AyanAuthTest.php`
- `backend/tests/Feature/AyanPersistenceTest.php`
- `frontend/vitest.config.ts`
- `frontend/tests/unit/validators.test.ts`
- `frontend/app/config/api.config.ts`
- `frontend/app/composables/useAuth.ts`
- `frontend/nuxt.config.ts`
- `.env.example`

\* `1.2 DONE*`: transport/auth/runtime/persistence готовы для AYAN MVP и проверены на VPS, но `initData` verification пока не production-grade.

---

## Next Phase: Phase 2 — Auth & Platform

- Telegram initData validation (real, не mock)
- JWT → Sanctum
- Единый профиль
- i18n 完善

---

## Done: Phase 0 — Foundation ✅

2026-04-18 — 2026-04-19

- 0.1 Очистка старого кода ✅
- 0.2 Composables (useAuth, useUtils) ✅
- 0.3 Components (7 шт) ✅
- 0.4 Types & i18n ✅
- 0.5 Layout & Pages ✅
- 0.6 Mock APIs ✅
- 0.7 Auth & Security ✅
- 0.8 Error & Validation ✅
- 0.9 Storage & Network ✅
- 0.10 i18n & Polish ✅
