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
| 1.3 | Frontend: структура AYAN (pages, composables, types) | TODO | — |
| 1.4 | Frontend: создание поездки (форма + API) | TODO | 1.2, 1.3 |
| 1.5 | Frontend: создание запроса (форма + API) | TODO | 1.2, 1.3 |
| 1.6 | Frontend: лента поездок/запросов + фильтры | TODO | 1.2, 1.3 |
| 1.7 | Frontend: отклик + контакт | TODO | 1.2, 1.6 |
| 1.8 | Integration: mock → real API | TODO | 1.2–1.7 |
| 1.9 | QA: все flow работают E2E | TODO | 1.8 |

Статусы: `TODO` `IN_PROGRESS` `DONE` `BLOCKED`

### Блокеры

Пока нет

### Решения

Лог решений текущего спринта

---

## Next: Phase 2 — Auth & Platform

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
