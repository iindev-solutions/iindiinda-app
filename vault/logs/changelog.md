# Changelog — iindev-vault
add time and date

## 2026-04-18 — Vault Population

### Created Structure
- `vault/raw/` — сырые данные
- `vault/wiki/architecture/` — системный дизайн
- `vault/wiki/services/laravel-api/` — Laravel API docs
- `vault/wiki/services/nuxt-app/` — Nuxt app docs
- `vault/logs/` — история изменений

### Created Files
- `vault/CLAUDE.md` — AI правила
- `vault/master_index.md` — карта базы знаний

### Added Raw Data
- `vault/raw/audit-2026-04-18.md` — полный аудит (10 критических багов, 10 архитектурных проблем)

### Added Wiki — Architecture
- `vault/wiki/architecture/system-design.md` — системный дизайн + диаграммы
- `vault/wiki/architecture/api-contract.md` — API контракт ayan (6 статусов, все эндпоинты)
- `vault/wiki/architecture/data-models.md` — TypeScript типы (TaxiOrder, User, ITaxiAPI)
- `vault/wiki/architecture/auth-flow.md` — авторизация Telegram + localhost
- `vault/wiki/architecture/ayan-rewrite-design.md` — полный дизайн переписывания (4 фазы)

### Added Wiki — Services
- `vault/wiki/services/laravel-api/overview.md` — обновлён с реальным содержанием
- `vault/wiki/services/laravel-api/endpoints.md` — все эндпоинты + validation rules + статус-машина
- `vault/wiki/services/nuxt-app/overview.md` — обновлён с архитектурой composables
- `vault/wiki/services/nuxt-app/routing.md` — роутинг + навигационные флоу (пассажир/водитель)

### Updated
- `vault/master_index.md` — все новые статьи + роадмап

## 2026-04-18 — AYAN Vision Updated with Monetization

### Updated
- `vault/wiki/architecture/ayan-rewrite-design.md` — добавлены секции:
  - **Потенциал и монетизация** — 4 модели: платное размещение, подписка, платные отклики, реклама
  - **Роль в экосистеме** — AYAN как трафик-генератор, не revenue-генератор
  - **Сравнение AYAN vs UUS** — таблица по частоте, контролю оплаты, монетизации, LTV
  - **Потенциал дохода** — оценка 100-300 поездок/день
  - **Обновлён Decision Log** — добавлены решения про роль в экосистеме и монетизацию

## 2026-04-18 — UUS Vision Document

### Added
- `vault/wiki/architecture/uus-vision.md` — полный vision-документ для сервиса UUS (Уус)
  - Updated terminology: "исполнитель" вместо "мастер" (шире аудитория)
  - 4 категории задач: 🏠 Дом, 🔧 Ремонт, 📦 Доставка, 🌿 Другое
  - Key mechanics: лимит откликов (3–5), выбор исполнителя, авто-закрытие заявки
  - 3 сценария: разовая задача, постоянный исполнитель, удалённая/офисная задача
  - Trust & Safety минимум (без усложнения)
  - Потенциал и монетизация: плата за отклик, подписка, премиум-размещение
  - MVP scope: создать заявку → лента → отклик → выбор → контакт

### Next Steps
- [ ] Создать API endpoints документ для UUS
- [ ] Создать data models для UUS (аналогично AYAN)
- [ ] Update routing.md с навигационными флоу UUS
- [ ] Начать Phase 0: Foundation для UUS
- [ ] Добавить сырые данные в /raw при необходимости

## 2026-04-18 — Browser Back Button Component Implementation

### Added
- `vault/wiki/architecture/browser-back-button.md` — дизайн-документ универсальной кнопки "Назад"
- `frontend/app/components/BackButton.vue` — универсальный компонент кнопки "Назад"
  - Smart navigation logic (same section detection)
  - Telegram BackButton integration (auto-hides UI button in TMA)
  - Props: fallbackRoute, beforeNavigate, onNavigate, label, forceUi, uiClass
  - Uses `navigateTo()` per AGENTS.md rules
  - @nuxt/ui v4 UButton styling (cyan, ghost variant)
- i18n keys added: `backButton.label`, `backButton.ariaLabel` (ru, sah)

### Features
- **Same Section Detection**: если предыдущий роут в той же секции (/ayan/*) — использует router.back()
- **Smart Fallback**: если нет истории — navigateTo(fallbackRoute || '/')
- **Hooks**: beforeNavigate (can cancel), onNavigate (custom logic)
- **Telegram Mode**: в TMA показывает нативный BackButton, UI кнопка скрывается автоматически
- **Debug Mode**: forceUi=true показывает UI кнопку даже в Telegram
