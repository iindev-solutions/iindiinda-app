# Master Index — iindev-vault

## Project Overview

> iindiinda — делаем сложные вещи просто.

**iind.app** — платформа из 4 сервисов, где люди решают повседневные задачи через других людей. Бесплатно для заказчиков. Платят исполнители.

Проект: https://github.com/iindiinda/iindiinda-app
Стек: Nuxt 4 (frontend) + Laravel (backend)
Статус: **Phase 1 — AYAN MVP** (Foundation COMPLETE)

### Roadmap

**Phase 0:** Foundation ✅ COMPLETE
**Phase 1:** AYAN MVP 🔄 CURRENT — sprint.md
**Phase 2:** Auth & Platform Foundation
**Phase 3:** UUS MVP
**Phase 4:** TAL MVP
**Phase 5:** AGAL MVP

Full roadmap: vault/wiki/architecture/roadmap.md

---

## Active Tasks

**Sprint:** vault/sprint.md — Phase 1 AYAN MVP (9 задач)

---

## Key Docs

### Code Inventory
- **vault/CODE_MAP.md** — инвентарь кода: composables, components, pages, API, статус реализации

### Wiki (vault/wiki/)

#### Architecture
**Platform:**
- vault/wiki/architecture/iind-app-vision.md — главный vision iind.app
- vault/wiki/architecture/system-design.md — системный дизайн платформы
- vault/wiki/architecture/roadmap.md — implementation roadmap (phases, scope)
- vault/wiki/architecture/auth-flow.md — авторизация (TMA + OAuth)

**Service Visions (4/4):**
- vault/wiki/architecture/ayan-vision.md — AYAN — попутки
- vault/wiki/architecture/uus-vision.md — UUS — услуги
- vault/wiki/architecture/tal-vision.md — TAL — запись к мастерам
- vault/wiki/architecture/agal-vision.md — AGAL — доставка через людей

**UI/UX:**
- vault/wiki/architecture/browser-back-button.md — BackButton компонент

#### Services (Implementation Docs)
**AYAN:**
- vault/wiki/services/ayan/api-contract.md — API контракт (эндпоинты, модели, валидация)

### Raw (vault/raw/)

**Пустой** — отработанные спеки удалены.
Правило: raw = черновик → wiki = финал → raw чистим.

### Logs
- vault/logs/changelog.md — история изменений

---

## Active Branches
- `main` — production
- `dev` — разработка
- `front/ayan` — AYAN MVP (текущий)

## AYAN MVP Summary

**MVP:**
1. Создать поездку (откуда, куда, когда, места, цена)
2. Создать запрос (пассажир ищет попутку)
3. Лента поездок/запросов с фильтром по маршруту
4. Откликнуться → получить контакт

**NO на MVP:** Карты, геокодинг, трекинг, push, рейтинги, оплата
