# SPEC.md — iindiinda-app Implementation Roadmap

> Vision Phase: COMPLETE ✅
> Implementation Phase: START

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

### Phase 0: Foundation (это мы здесь)
- [x] Vision Phase COMPLETE (5 vision docs)
- [ ] Создать SPEC.md (этот документ)
- [ ] Настроить пустую структуру frontend + backend
- [ ] Зафиксировать архитектуру в коде

### Phase 1: AYAN MVP (попутки)
- [ ] Проектирование API (endpoints, contracts, models)
- [ ] Backend: модели, миграции, контроллеры
- [ ] Frontend: базовая структура сервиса
- [ ] Frontend: создание поездки/запроса
- [ ] Frontend: лента поездок/запросов
- [ ] Frontend: отклик + контакт
- [ ] Integration: mock → real API

### Phase 2: Auth & Platform Foundation
- [ ] Telegram WebApp SDK integration
- [ ] Auth flow (initData → JWT)
- [ ] Единый профиль пользователя
- [ ] i18n (ru + sah)

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

### AYAN (Бардыбыт) — Попутки

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

**Статусы:**
- Trip: open, closed
- Request: open, closed

**NO на MVP:**
- Карты, геокодинг
- Трекинг, push-уведомления
- Рейтинги, отзывы
- Оплата внутри платформы

---

### UUS (Уус) — Услуги

**MVP функционал:**
1. Создать заявку (категория, описание, место, время, бюджет)
2. Лента заявок с фильтрами
3. Откликнуться (лимит 3–5)
4. Выбрать исполнителя → контакт

**Сущности:**
```
Task {
  id, author_id, category, description,
  location, datetime, budget, urgent, status, created_at
}

TaskResponse {
  id, task_id, executor_id, message, selected, created_at
}
```

**NO на MVP:**
- Профили исполнителей
- Рейтинги, отзывы
- Безопасные сделки

---

### TAL (Тал) — Запись

**MVP функционал:**
1. Мастер: показать статус (свободен/занят)
2. Клиент: список мастеров по категории
3. Записаться → контакт
4. Запрос (fallback если никто не свободен)

**Сущности:**
```
Master {
  id, user_id, category, status, created_at
}

Booking {
  id, master_id, client_id, message, created_at
}

MasterRequest {
  id, client_id, category, description, created_at
}
```

**Статусы мастера:**
- available_now (🟢)
- available_later (🕓)
- available_tomorrow (📅)
- busy (🔴)

---

### AGAL (Аҕал) — Доставка

**MVP функционал:**
1. Перевозчик: создать маршрут (куда, когда, что может взять)
2. Отправитель: создать запрос (куда, что, срочность)
3. Лента маршрутов/запросов
4. Откликнуться (лимит 3–5) → контакт

**Сущности:**
```
Route {
  id, carrier_id, from_city, to_city,
  date, time, capacity, price, status, created_at
}

DeliveryRequest {
  id, sender_id, to_city, description,
  size, fragile, urgent, budget, status, created_at
}
```

**NO на MVP:**
- Страховка, отслеживание
- Проверка перевозчиков

---

## Technical Architecture

### Frontend Structure

```
frontend/
├── app/
│   ├── composables/       # useTg, useAuth, useAPI, useTaxiAPI
│   ├── components/        # BackButton, Layout, common
│   ├── pages/
│   │   ├── index.vue      # Hub (все 4 сервиса)
│   │   └── ui.vue         # UI components demo
│   ├── layouts/           # default.vue
│   └── types/             # interfaces
├── services/
│   ├── ayan/app/pages/    # /ayan/*
│   ├── uus/app/pages/     # /uus/*
│   ├── tal/app/pages/     # /tal/*
│   └── agal/app/pages/    # /agal/*
└── i18n/locales/          # ru.json, sah.json
```

**Nuxt 4 Extends:** services/ayan, services/uus, services/tal, services/agal
**Каждый сервис — Nuxt layer**

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

**Почему:** Уже есть частичный код (с багами), но главное — это входная точка в экосистему.

**Задачи Phase 1:**
1. Спроектировать API contract (эндпоинты, модели)
2. Создать пустой backend (Laravel scaffold)
3. Написать backend: User, Trip, Request, Response
4. Написать frontend: чистая структура AYAN
5. Интегрировать: mock → real API
6. QA: все flow работают

### Phase 2: Auth (в любой момент)

**Привязка к Telegram:** initData validation → JWT → Sanctum

### Phase 3-5: Остальные сервисы

**После AYAN:** UUS → TAL → AGAL (или параллельно если команда позволяет)

---

## TODOs — Immediate Actions

### Для выполнения:

- [ ] Создать пустой backend Laravel (composer create-project)
- [ ] Спроектировать API contract для AYAN
- [ ] Написать миграции: users, trips, requests, responses
- [ ] Написать контроллеры AYAN
- [ ] Почистить frontend/services/ayan (удалить старый баговый код)
- [ ] Написать чистый frontend для AYAN
- [ ] Подключить реальный API

### После AYAN:

- [ ] Auth: Telegram SDK → JWT
- [ ] UUS API + frontend
- [ ] TAL API + frontend
- [ ] AGAL API + frontend

---

## Progress Tracking

| Дата | Фаза | Статус |
|------|------|--------|
| 2026-04-18 | Vision Phase | ✅ Complete |
| 2026-04-18 | SPEC.md created | 🔄 In progress |

---

## Files Created During Implementation

```
vault/raw/
├── SPEC.md                    # этот документ
└── ayan-api-contract.md       # API endpoints для AYAN (создать)

vault/wiki/architecture/
├── iind-app-vision.md         # платформа
├── ayan-vision.md             # AYAN vision
├── uus-vision.md              # UUS vision
├── tal-vision.md              # TAL vision
├── agal-vision.md             # AGAL vision
└── system-design.md           # архитектура
```

---

**Следующий шаг:** Спроектировать AYAN API contract → создать `ayan-api-contract.md` в `/raw`