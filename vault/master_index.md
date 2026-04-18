# Master Index — iindev-vault

## Project Overview
Проект: [[https://github.com/iindiinda/iindiinda-app|iindiinda-app]]
Стек: Nuxt 4 (frontend) + Laravel (backend)
Статус: MVP — Ayan (такси) в разработке

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
- [[wiki/architecture/ayan-rewrite-design]] — дизайн переписывания ayan

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

## Roadmap Summary
1. Phase 0: Foundation (types, ITaxiAPI, useAuth, useTg, usePolling)
2. Phase 1: Passenger flow (create, my-order, complete)
3. Phase 2: Driver flow (driver, orders, active-ride)
4. Phase 3: Shared components & polish
5. Backend: Controllers, migrations, new endpoints
