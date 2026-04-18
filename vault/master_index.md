# Master Index — iindev-vault

## Project Overview

> iindiinda — делаем сложные вещи просто. Modern solutions. Simply.

**iind.app** — платформа из 4 сервисов, где люди решают повседневные задачи через других людей. Бесплатно для заказчиков. Платят исполнители.

Проект: [[https://github.com/iindiinda/iindiinda-app|iindiinda-app]]
Стек: Nuxt 4 (frontend) + Laravel (backend)
Статус: **Implementation Phase** — Vision COMPLETE, roadmap создан

### Roadmap

**Phase 0:** Foundation 🔄 IN PROGRESS
- 0.1: Очистка старого кода (delete buggy AYAN, useTaxiAPI, useMockAPI)
- 0.2: Composables (useAuth, useUtils)
- 0.3: Components (AppHeader, ServiceCard, LoadingSpinner, EmptyState, ErrorMessage)
- 0.4: Types & i18n (clean api.ts, full i18n keys)
- 0.5: Layout & Pages (update default.vue, index.vue)
- 0.6: Mock APIs (auth, user base mocks)

**Phase 1:** AYAN MVP 🔜 Next
**Phase 2:** Auth & Platform Foundation
**Phase 3:** UUS MVP
**Phase 4:** TAL MVP
**Phase 5:** AGAL MVP

---

## Structure

### Wiki (`/wiki`)

#### Architecture (Vision Only)
**Platform Vision:**
- [[wiki/architecture/iind-app-vision]] — **Главный vision iind.app** (национальный контекст, экосистема, цели)
- [[wiki/architecture/system-design]] — общий системный дизайн платформы

**Service Visions (4/4 complete):**
- [[wiki/architecture/ayan-vision]] — AYAN — попутки
- [[wiki/architecture/uus-vision]] — UUS — услуги
- [[wiki/architecture/tal-vision]] — TAL — запись к мастерам
- [[wiki/architecture/agal-vision]] — AGAL — доставка через людей

**UI/UX:**
- [[wiki/architecture/browser-back-button]] — универсальный компонент BackButton

*Implementation docs (API, models, code) — created after vision phase*

#### Vision Phase — COMPLETE ✅
All 5 vision documents finalized (platform + 4 services). Ready for implementation phase.

### Logs
- [[logs/changelog]] — история изменений базы знаний

---

## Quick Links
- AGENTS.md проекта: `C:\Users\slavk\Desktop\git\iindiinda-app\AGENTS.md`
- Obsidian проекта: `C:\Users\slavk\Desktop\my-data\zettel\slava-obsidian-new\base\iindev.md`

## Active Branches
- `main` — production
- `dev` — разработка
- `front/taxi` — legacy (старый taxi-код, не поддерживается)
- `front/ayan` — новый AYAN (доска попуток, чистый старт)

## Roadmap Summary — AYAN (доска попуток)

**MVP:**
1. Создать поездку (откуда, куда, когда, места, цена)
2. Создать запрос (пассажир ищет попутку)
3. Список поездок/запросов с фильтром по маршруту
4. Откликнуться → получить контакт

**No:**
- Статусы, трекинг, карты, авто-подбор

**After MVP (if needed):**
- Геокодинг + карты
- Регулярные маршруты
- Чат между участниками
- Рейтинги
