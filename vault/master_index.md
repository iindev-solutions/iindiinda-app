# Master Index — iindev-vault

## Project Overview

> iindiinda — делаем сложные вещи просто. Modern solutions. Simply.

**iind.app** — слой, который соединяет людей. Один принцип для всех сервисов: заявка → отклик → договор.

Проект: [[https://github.com/iindiinda/iindiinda-app|iindiinda-app]]
Стек: Nuxt 4 (frontend) + Laravel (backend)
Статус: MVP — Ayan (попутки) в разработке, остальные 3 параллельно

---

## Structure

### Raw Data (`/raw`)
- [[raw/audit-2026-04-18]] — полный аудит проекта (баги, мисматчи, архитектура)

### Wiki (`/wiki`)

#### Architecture
- [[wiki/architecture/system-design]] — общий системный дизайн + диаграммы
- [[wiki/architecture/api-contract]] — полный API контракт ayan (6 статусов)
- [[wiki/architecture/data-models]] — TypeScript типы + модели данных
- [[wiki/architecture/auth-flow]] — авторизация (Telegram + localhost)
- [[wiki/architecture/ayan-rewrite-design]] — AYAN Vision + Concept + MVP + API endpoints (новый)
- [[wiki/architecture/browser-back-button]] — универсальный компонент BackButton (smart nav + Telegram)

#### Services
- [[wiki/services/laravel-api/overview]] — обзор Laravel API + текущий статус
- [[wiki/services/laravel-api/endpoints]] — все эндпоинты ayan + validation
- [[wiki/services/nuxt-app/overview]] — обзор Nuxt приложения + архитектура
- [[wiki/services/nuxt-app/routing]] — роутинг + навигационные флоу

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
