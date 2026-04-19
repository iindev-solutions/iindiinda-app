# iindiinda-app Implementation Roadmap

> Vision Phase: COMPLETE ✅
> Foundation Phase 0: COMPLETE ✅
> Current: Phase 1 — AYAN MVP

---

## Overview

**Цель:** Построить платформу iind.app — Telegram Mini App с 4 сервисами.
**Принцип:** Один паттерн для всех: заявка → отклик → договор.

**Стек:**
- Frontend: Nuxt 4 + Vue 3 + TypeScript (SPA-only)
- UI: @nuxt/ui v4 (Cyan theme, dark only)
- Backend: Laravel + MySQL
- Auth: Telegram WebApp SDK → JWT
- i18n: ru + sah

---

## Roadmap — Phases

### Phase 0: Foundation ✅ COMPLETE
- [x] Vision Phase COMPLETE (5 vision docs)
- [x] Спецификация (SPEC, foundation-spec, foundation-phase-0-spec)
- [x] Настроить структуру frontend + backend
- [x] Зафиксировать архитектуру в коде
- [x] Очистка старого кода (0.1)
- [x] Composables — useAuth, useUtils (0.2)
- [x] Components — AppHeader, ServiceCard, LoadingSpinner, EmptyState, ErrorMessage (0.3)
- [x] Types & i18n — api.ts, ru/sah.json (0.4)
- [x] Layout & Pages — default.vue, index.vue (0.5)
- [x] Mock APIs — auth, user mocks (0.6)
- [x] Auth & Security — useAuth enhanced, middleware, plugin (0.7)
- [x] Error & Validation — useGlobalError, validators, forms.ts (0.8)
- [x] Storage & Network — useStorage, useNetwork, ui.ts (0.9)
- [x] i18n & Polish — sah.json, typecheck + lint pass (0.10)

### Phase 1: AYAN MVP (попутки) 🔜 CURRENT
- [ ] API contract финализация (endpoints, models) — [[wiki/services/ayan/api-contract]]
- [ ] Backend: модели, миграции, контроллеры
- [ ] Frontend: базовая структура сервиса
- [ ] Frontend: создание поездки/запроса
- [ ] Frontend: лента поездок/запросов
- [ ] Frontend: отклик + контакт
- [ ] Integration: mock → real API

### Phase 2: Auth & Platform Foundation
- [ ] Telegram WebApp SDK integration (real validation)
- [ ] Auth flow (initData → JWT → Sanctum)
- [ ] Единый профиль пользователя
- [ ] i18n 完善 (ru + sah)

### Phase 3: UUS MVP (услуги)
- [ ] API design
- [ ] Backend implementation
- [ ] Frontend: заявки + отклики

### Phase 4: TAL MVP (запись)
- [ ] API design
- [ ] Backend implementation
- [ ] Frontend: статусы мастера + запись

### Phase 5: AGAL MVP (доставка)
- [ ] API design
- [ ] Backend implementation
- [ ] Frontend: маршруты + запросы

---

## Services — MVP Scope

### AYAN — Попутки

**MVP функционал:**
1. Создать поездку (водитель: откуда, куда, когда, места, цена)
2. Создать запрос (пассажир: откуда, куда, когда)
3. Лента поездок/запросов с фильтром по маршруту
4. Откликнуться → получить контакт

**Сущности:**
```
Trip {
  id, driver_id, from_address, to_address,
  date, time, seats, price, status, created_at
}

Request {
  id, passenger_id, from_address, to_address,
  date, time, description, status, created_at
}

Response {
  id, trip_id/request_id, user_id, message, created_at
}
```

**Статусы:** Trip: open, closed | Request: open, closed

**NO на MVP:** Карты, геокодинг, трекинг, push, рейтинги, оплата

---

### UUS — Услуги

**MVP:** Создать заявку → лента с фильтрами → отклик (лимит 3–5) → выбор исполнителя → контакт

**Сущности:** Task, TaskResponse

**NO:** Профили исполнителей, рейтинги, безопасные сделки

---

### TAL — Запись

**MVP:** Мастер: статус (свободен/занят) → Клиент: список по категории → Запись → контакт → Запрос (fallback)

**Статусы мастера:** 🟢 сейчас / 🕓 позже / 📅 завтра / 🔴 занят

**Сущности:** Master, Booking, MasterRequest

---

### AGAL — Доставка

**MVP:** Перевозчик: маршрут → Отправитель: запрос → Лента → Отклик (лимит 3–5) → контакт

**Сущности:** Route, DeliveryRequest

**NO:** Страховка, отслеживание, проверка перевозчиков

---

## Technical Architecture

### Frontend Structure

```
frontend/
├── app/
│   ├── composables/       # useTg, useAuth, useAPI, useGlobalError, useStorage, useNetwork
│   ├── components/        # BackButton, AppHeader, ServiceCard, LoadingSpinner, EmptyState, ErrorMessage
│   ├── pages/             # index.vue (Hub), ui.vue (demo)
│   ├── layouts/           # default.vue
│   ├── types/             # api.ts, forms.ts, ui.ts, telegram.d.ts
│   ├── utils/             # formatters.ts, validators.ts
│   ├── middleware/         # auth.ts
│   ├── plugins/           # init.ts, error-handler.ts
│   └── config/            # api.config.ts, mockData.ts
├── services/
│   ├── ayan/app/          # /ayan/*
│   ├── uus/app/           # /uus/*
│   ├── tal/app/           # /tal/*
│   └── agal/app/          # /agal/*
└── i18n/locales/          # ru.json, sah.json
```

### Backend Structure

```
backend/
├── app/
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php
│   │   └── Ayan/
│   ├── Models/
│   └── Services/
├── database/migrations/
├── routes/api.php
└── composer.json
```

### API Contract Pattern

```
GET    /ayan/trips         # список поездок
POST   /ayan/trips         # создать поездку
GET    /ayan/requests      # список запросов
POST   /ayan/requests      # создать запрос
POST   /ayan/responses     # откликнуться

GET    /uus/tasks
POST   /uus/tasks
POST   /uus/responses

GET    /tal/masters
POST   /tal/status
POST   /tal/bookings

GET    /agal/routes
POST   /agal/routes
GET    /agal/requests
POST   /agal/requests
```

---

## Priority & Order

### Phase 1: AYAN MVP (приоритет)

**Почему:** Входная точка в экосистему. Самый понятный сервис. Есть частичный код.

**Задачи Phase 1:**
1. Финализировать API contract → [[wiki/services/ayan/api-contract]]
2. Создать backend: User, Trip, Request, Response (Laravel)
3. Написать frontend: чистая структура AYAN
4. Интегрировать: mock → real API
5. QA: все flow работают

### Phase 2: Auth (в любой момент)
**Привязка к Telegram:** initData validation → JWT → Sanctum

### Phase 3-5: Остальные сервисы
**После AYAN:** UUS → TAL → AGAL

---

## Progress Tracking

| Дата | Фаза | Статус |
|------|------|--------|
| 2026-04-18 | Vision Phase | ✅ Complete |
| 2026-04-19 | Phase 0: Foundation | ✅ Complete |
| 2026-04-19 | Phase 1: AYAN MVP | 🔄 Start |
