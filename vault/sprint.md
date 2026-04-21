# Sprint — Phase 1 AYAN MVP

> Roadmap: vault/wiki/architecture/roadmap.md
> API: vault/wiki/services/ayan/api-contract.md
> Code: vault/CODE_MAP.md

---

## Phase 1: AYAN MVP

Начало: 2026-04-19
Цель: Рабочий MVP попуток (создание поездки/запроса → лента → отклик → контакт)

### Resume Point — 2026-04-22

- **Где остановились:** frontend AYAN практически готов, но всё ещё работает через `mock API`
- **Главный блокер:** backend до сих пор на старом `/ayan/orders/*`, а frontend уже живёт в контракте `trips / requests / responses / my/*`
- **Последнее завершённое локальное действие:** `vitest` setup поднят как baseline для plain TS smoke tests (`frontend/vitest.config.ts`, `test`, `test:watch`, smoke test)
- **Продолжать с:** `vault/resume-plan.md`

### Current Reality

- Frontend Phase 1 = **mock-ready**, не `real API ready`
- Backend Phase 1 = **blocked by missing real implementation**, не только миграции, но и несовпадение доменной модели
- Frontend testing base = **baseline ready** (`vitest` smoke path работает для plain TS unit tests, не для Nuxt composables)
- Переключение `mock → real` начинать только после замены backend `orders` API на новый AYAN contract

### Задачи

| # | Задача | Статус | Блокеры |
|---|--------|--------|---------|
| 1.1 | Backend: миграции (users, trips, requests, responses) | IN_PROGRESS | нет runtime verification |
| 1.2 | Backend: модели + контроллеры AYAN | IN_PROGRESS | 1.1, нет runtime verification |
| 1.3 | Frontend: структура AYAN (pages, composables, types) | DONE | — |
| 1.4 | Frontend: создание поездки/запроса (единый slideover + pill-табы) | DONE* | — |
| 1.5 | Frontend: лента поездок/запросов + табы + empty state | DONE* | — |
| 1.6 | Frontend: performance (lazy, useLazyAsyncData, loader overlay, prefetch) | DONE | — |
| 1.7 | Frontend: отклик + контакт (status, accept/reject, telegram link) | DONE | — |
| 1.8 | Frontend: фильтры по маршруту/дате | DONE | — |
| 1.9 | Frontend: Nuxt UI colors fix (cyan→primary, gray→neutral) | DONE | — |
| 1.10 | Integration: mock → real API | TODO | 1.2 |
| 1.11 | QA: все flow работают E2E | TODO | 1.10 |

\* 1.4 и 1.5 — работают на mock API. При переключении на real API нужно проверить валидацию ответов.

Статусы: `TODO` `IN_PROGRESS` `DONE` `BLOCKED`

### Блокеры

- Backend всё ещё использует старый mock `Ayan\\OrderController` и маршруты `/ayan/orders/*`
- Backend не совпадает с `vault/wiki/services/ayan/api-contract.md`
- В текущей среде нет `php`, `composer`, `docker` — backend нельзя прогнать локально, только готовить статический Laravel-код
- `1.10 Mock → Real API` и `1.11 QA E2E` нельзя нормально начать до завершения `1.1` и `1.2`

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

- [ ] 1.1 Backend: миграции
- [ ] 1.2 Backend: модели + контроллеры
- [ ] 1.10 Mock → Real API (зависит от 1.2)
- [ ] 1.11 QA E2E (зависит от 1.10)

### Resume Files

- `vault/resume-plan.md`
- `vault/wiki/services/ayan/api-contract.md`
- `vault/wiki/services/ayan/backend-bringup.md`
- `backend/app/Http/Controllers/Ayan/TripController.php`
- `backend/app/Http/Controllers/Ayan/RequestController.php`
- `backend/app/Http/Controllers/Ayan/ResponseController.php`
- `backend/app/Http/Controllers/Ayan/MyController.php`
- `backend/routes/api.php`
- `backend/app/Http/Controllers/Ayan/OrderController.php`
- `backend/app/Models/User.php`
- `backend/app/Models/Trip.php`
- `backend/app/Models/AyanRequest.php`
- `backend/app/Models/AyanResponse.php`
- `backend/database/migrations/2026_04_22_000001_create_users_table.php`
- `backend/database/migrations/2026_04_22_000002_create_trips_table.php`
- `backend/database/migrations/2026_04_22_000003_create_requests_table.php`
- `backend/database/migrations/2026_04_22_000004_create_responses_table.php`
- `frontend/vitest.config.ts`
- `frontend/tests/unit/validators.test.ts`

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
