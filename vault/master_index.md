# Master Index — iindev-vault

## Project Overview

> iindiinda — делаем сложные вещи просто. Modern solutions. Simply.
> **Сделано сахалар для сахалар.**

**iind.app** — платформа из 4 сервисов, где саха люди решают повседневные задачи через других людей. Бесплатно для заказчиков. Платят исполнители.

Проект: [[https://github.com/iindiinda/iindiinda-app|iindiinda-app]]
Стек: Nuxt 4 (frontend) + Laravel (backend)
Статус: MVP — Ayan (попутки) в разработке, остальные 3 параллельно

---

## Structure

### Raw Data (`/raw`)
- [[raw/audit-2026-04-18]] — полный аудит проекта (баги, мисматчи, архитектура)

### Wiki (`/wiki`)

#### Architecture (Vision Only)
**Platform Vision:**
- [[wiki/architecture/iind-app-vision]] — **Главный vision iind.app** (национальный контекст, экосистема, цели)
- [[wiki/architecture/system-design]] — общий системный дизайн платформы

**Service Visions (4/4 complete):**
- [[wiki/architecture/ayan-vision]] — AYAN — попутки
- [[wiki/architecture/uus-vision]] — UUS (Уус) — услуги
- [[wiki/architecture/tal-vision]] — TAL (Тал) — запись к мастерам
- [[wiki/architecture/agal-vision]] — AGAL (Аҕал) — доставка через людей

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
- `front/ayan` — активная разработка (попутки)
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
