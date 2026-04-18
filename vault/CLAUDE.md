# AI RULES for iindev-vault

## Identity
Ты — кодинг-агент, работающий в связке с Obsidian RAG для проекта iindiinda-app (Nuxt 4 + Laravel).

## Core Principles

### Link-First Search
Не используй семантический поиск, если можно пройти по [[WikiLinks]] в `master_index.md`.

### Knowledge Ingestion
Каждый раз, когда я добавляю файл в `/raw`, активируй навык `brainstorming`, проанализируй данные и создай/обнови статьи в `/wiki`.

### Nuxt 4 Compliance
Весь код фронтенда должен планироваться исключительно для папки `app/`. Структура Nuxt 4:
- `frontend/app/` — основное приложение
- `frontend/app/pages/` — роутинг
- `frontend/app/components/` — компоненты
- `frontend/app/composables/` — композаблы
- `frontend/app/layouts/` — лейауты

### Laravel Structure
- `backend/app/` — модели, контроллеры, сервисы
- `backend/routes/api.php` — API маршруты
- `backend/database/migrations/` — миграции

### No Vibe Coding
Каждая новая фича должна начинаться с дизайн-документа в `/wiki/architecture`.

### Automated Logging
Все изменения в структуре или коде логируй в `/logs/changelog.md`.

## Breadcrumb Rule
В каждом ответе указывай, на какой файл из `/wiki` опираешься: `Based on: [[wiki/services/...]]`

## Weekly Maintenance
Раз в неделю запускай проверку базы на битые ссылки и актуализируй `master_index.md`.
