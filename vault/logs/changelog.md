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

### Next Steps
- [ ] Review дизайн с командой
- [ ] Начать Phase 0: Foundation
- [ ] Добавить сырые данные в /raw при необходимости
