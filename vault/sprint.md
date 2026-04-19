# Sprint — Phase 1 AYAN MVP

> Roadmap: vault/wiki/architecture/roadmap.md
> API: vault/wiki/services/ayan/api-contract.md
> Code: vault/CODE_MAP.md

---

## Phase 1: AYAN MVP

Начало: 2026-04-19
Цель: Рабочий MVP попуток (создание поездки/запроса → лента → отклик → контакт)

### Задачи

| # | Задача | Статус | Блокеры |
|---|--------|--------|---------|
| 1.1 | Backend: миграции (users, trips, requests, responses) | TODO | — |
| 1.2 | Backend: модели + контроллеры AYAN | TODO | 1.1 |
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

Пока нет

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

## Frontend MVP Complete ✅

Все фронтенд-задачи Phase 1 выполнены. Дальше — backend:

### Blocked by Backend (1.1, 1.2)

- [ ] 1.1 Backend: миграции
- [ ] 1.2 Backend: модели + контроллеры
- [ ] 1.10 Mock → Real API (зависит от 1.2)
- [ ] 1.11 QA E2E (зависит от 1.10)

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
