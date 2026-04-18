# Changelog — iindev-vault

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
- **Telegram Mode**: в TMA показывает нативный BackButton, UI кнопка скрывается
- **Debug Mode**: forceUi=true показывает UI кнопку даже в Telegram

### Next Steps
- [ ] Update routing.md with BackButton usage examples
- [ ] Review с командой
- [ ] Начать Phase 0: Foundation
- [ ] Добавить сырые данные в /raw при необходимости
