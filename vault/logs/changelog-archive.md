# Changelog — iindev-vault (Archive)

> Старые записи. ИИ не читает. Только по запросу.

---

## 2026-04-18 — Browser Back Button Component Implementation

### Added
- `vault/wiki/architecture/browser-back-button.md` — дизайн-документ
- `frontend/app/components/BackButton.vue` — универсальный BackButton
- i18n keys: `backButton.label`, `backButton.ariaLabel` (ru, sah)

---

## 2026-04-18 — Vision Documents Refactored

- `vault/wiki/architecture/ayan-vision.md` — чистый vision без кода
- Удалён `ayan-rewrite-design.md` (технические детали)
- `uus-vision.md` — добавлен Decision Log

---

## 2026-04-18 — AGAL Vision Document Created

- `vault/wiki/architecture/tal-vision.md` — TAL vision

---

## 2026-04-18 — Main iind.app Vision Document Created

- `vault/wiki/architecture/iind-app-vision.md` — главный vision-документ
- 5 продуктовых принципов, национальный контекст, монетизация, Decision Log
- Vision Phase COMPLETE ✅

---

## 2026-04-18 — Vault Cleanup: Technical Docs Removed

Удалено 7 технических файлов из wiki (Vision phase ≠ Implementation phase).
Оставлены vision-only: system-design, ayan-vision, uus-vision, browser-back-button.
Удалены: api-contract, data-models, auth-flow, nuxt-app/*, laravel-api/*.

---

## 2026-04-18 — Vault Audit Cleanup

- Удалён `vault/raw/audit-2026-04-18.md`
- Обновлён system-design.md (битые ссылки)

---

## 2026-04-18 — Foundation Phase Start + Audit

- Создан `vault/raw/SPEC.md` — roadmap
- Создан `vault/raw/ayan-api-contract.md` — API contract AYAN
- Phase: Vision ✅ → Implementation 🔄

---

## 2026-04-18 — Phase 0.1: Foundation Cleanup ✅

- Удалены useTaxiAPI, useMockAPI
- Добавлен `services/ayan/nuxt.config.ts`
- Обновлён `nuxt.config.ts` (extends ayan)
- typecheck ✅ lint ✅

---

## 2026-04-18 — Visions Balanced

- iind-app-vision.md — сокращён
- ayan-vision.md — оптимальный баланс (проблема+решение, цены, economics, конкуренты)
- Убраны якутские названия, лишние таблицы

---

## 2026-04-18 — Vault Cleanup Round 2

- ayan-vision.md — дополнен (User Stories, Types, Decision Log)
- system-design.md — убраны якутские названия
- master_index.md — убраны raw ссылки

---

## 2026-04-18 — Backend Controllers (Foundation)

- AuthController.php — /auth/telegram (mock)
- UserController.php — /user/me, switch-role (mock)
- Ayan/OrderController.php — AYAN MVP endpoints (mock)

---

## 2026-04-18 — Production-like Mock Data Layer

- mockData.ts — MOCK_USERS, CITY_ROUTES, INTERCITY_ROUTES, generateMockOrders
- useAPI.ts — mock/real toggle via USE_MOCK_API

---

## 2026-04-18 — Pages + Utils Foundation

- formatters.ts — formatPrice, formatDate, formatTime, formatRelativeTime, etc
- Обновлён default.vue, index.vue
