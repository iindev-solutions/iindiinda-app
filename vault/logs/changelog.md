# Changelog — iindev-vault

## 2026-04-18 — Pages + Utils Foundation

### Added
- `frontend/app/utils/formatters.ts` — utility functions
  - formatPrice(), formatDate(), formatTime(), formatRelativeTime()
  - formatDriverName(), getInitials()
  - isValidPrice(), isValidTime(), isValidAddress()
  - debounce() helper

### Updated
- `frontend/app/layouts/default.vue` — uses AppHeader component
- `frontend/app/pages/index.vue` — uses ServiceCard component, cleaner layout

---

## 2026-04-18 — Production-like Mock Data Layer

### Added
- `frontend/app/config/mockData.ts` — realistic mock data for AYAN
  - MOCK_USERS: realistic Yakutsk region names (Вася, Пётр, Иван, etc)
  - CITY_ROUTES: Марха-Порт, Якутск-Намцы, Сайсары-больница, etc
  - INTERCITY_ROUTES: Якутск-Хандыга, Якутск-Вилюйск, etc
  - generateMockOrders(): generates realistic orders with delays
  - mockApiResponses: async helpers with simulated network delay

### Updated
- `frontend/app/composables/useAPI.ts` — extended with mock support
  - Checks `USE_MOCK_API` from `api.config.ts`
  - Returns mock data when flag is true
  - Makes real HTTP requests when flag is false

### How to toggle
- `frontend/app/config/api.config.ts` → `USE_MOCK_API = true/false`
- true: realistic mock data (no backend needed)
- false: real API calls to Laravel backend

---

## 2026-04-18 — Backend Controllers (Foundation)

### Added
- `backend/app/Http/Controllers/AuthController.php` — `/auth/telegram` endpoint (mock user)
- `backend/app/Http/Controllers/UserController.php` — `/user/me`, `/user/switch-role` (mock)
- `backend/app/Http/Controllers/Ayan/OrderController.php` — all AYAN MVP endpoints (mock data)

### Notes
- All controllers return mock data for MVP
- Telegram initData validation skipped (TODO after MVP)
- Sanctum auth middleware skipped (TODO after MVP)

---

## 2026-04-18 — Vault Cleanup Round 2

### Updated
- `vault/wiki/architecture/ayan-vision.md` — дополнен (User Stories, Types объявлений, Decision Log)
- `vault/wiki/architecture/system-design.md` — убраны якутские названия из таблицы
- `vault/master_index.md` — убраны ссылки на raw (Knowledge Ingestion rule)

### Fixed
- Убрано «Бардыбыт» (оставлены AYAN, UUS, TAL, AGAL)
- Убрано «сахалар для сахалар»
- Убраны ссылки на raw из master_index

---

## 2026-04-18 — Visions Balanced

### Updated
- `vault/wiki/architecture/iind-app-vision.md` — сокращён, добавлена секция для разработки
- `vault/wiki/architecture/ayan-vision.md` — оптимальный баланс

**AYAN Vision включает:**
- Проблема + решение (конкретно)
- Два типа поездок с ценами
- Unit Economics (реалистичные)
- Монетизация (подписки + продвижение)
- Рост (3 этапа)
- Конкуренты
- Размер рынка (~66 млн/мес)

**Убрано:**
- Якутские названия («Бардыбыт», «сахалар для сахалар»)
- Слишком детальные таблицы
- Лишние секции

**Сохранено:**
- Всё что важно для разработки
- Всё что важно для инвестора
- Главный инсайт

**Ключевой месседж:**
> «Мы не создаём рынок. Мы делаем удобнее то, что уже работает через WhatsApp.»

---

## 2026-04-18 — Phase 0.1: Foundation Cleanup ✅

### Deleted
- `frontend/app/composables/useTaxiAPI.ts` — AYAN-specific, не для foundation
- `frontend/app/composables/useMockAPI.ts` — старый баговый мок

### Added
- `frontend/services/ayan/nuxt.config.ts` — чтобы AYAN был полноценным Nuxt layer

### Updated
- `frontend/nuxt.config.ts` — добавлен `./services/ayan` в extends (было только agal, tal, uus)
- `frontend/app/types/api.ts` — очищен, оставлены только базовые типы (User, AuthResponse, ApiError), сервис-специфичные типы вынесены в service layers

### Verified
- `npm run typecheck` — ✅ pass
- `npm run lint:fix` — ✅ pass

### Foundation Composables (current)
```
app/composables/
├── useTg.ts      ✅
├── useAuth.ts    ⚠️ needs work
└── useAPI.ts     ✅
```

---

## 2026-04-18 — Foundation Phase Start + Audit

### Created
- `vault/raw/SPEC.md` — полный roadmap: фазы, todos, архитектура сервисов
- `vault/raw/ayan-api-contract.md` — детальный API contract для AYAN MVP

### Updated
- `vault/master_index.md` — добавлен roadmap, обновлён статус (Implementation Phase)

### Phase Status
- Vision Phase: ✅ COMPLETE
- Implementation Phase: 🔄 START

---

## 2026-04-18 — Vault Audit Cleanup

### Deleted
- `vault/raw/audit-2026-04-18.md` — технический аудит кода (не relevant для Vision phase)

### Updated
- `vault/wiki/architecture/system-design.md` — убраны битые ссылки на удалённые docs
- `vault/master_index.md` — убрана ссылка на удалённый аудит

---

## 2026-04-18 — Vault Cleanup: Technical Docs Removed

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

## 2026-04-18 — Vault Cleanup: Technical Docs Removed

### Удалено 7 технических файлов
Принцип: Vision phase ≠ Implementation phase

**Удалены из architecture/:**
- `api-contract.md` — API endpoints, JSON contracts
- `data-models.md` — TypeScript interfaces, типы
- `auth-flow.md` — flow diagrams, middleware, implementation steps

**Удалены из services/:**
- `nuxt-app/overview.md` — file structure, composables, code conventions
- `nuxt-app/routing.md` — page paths, file-based routing, middleware
- `laravel-api/overview.md` — controllers, migrations, backend structure
- `laravel-api/endpoints.md` — REST API, state machines

**Удалены пустые директории:**
- `vault/wiki/services/nuxt-app/`
- `vault/wiki/services/laravel-api/`
- `vault/wiki/services/`

### Оставлены (Vision only)
- `system-design.md` — общий vision платформы
- `ayan-vision.md` — концепция, сценарии, монетизация
- `uus-vision.md` — концепция, сценарии, монетизация
- `browser-back-button.md` — UI/UX дизайн компонента

### Обновлено
- `master_index.md` — убраны ссылки на удалённые файлы
- `changelog.md` — this entry

### Workflow Established
1. **Vision Phase** → концепция, сценарии, механики, монетизация
2. **Implementation Phase** → код, API, модели (создаём после финализации vision)

## 2026-04-18 — Main iind.app Vision Document Created (IMPROVED)

### Added
- `vault/wiki/architecture/iind-app-vision.md` — **главный vision-документ платформы**

**Ключевые улучшения по сравнению с исходным текстом:**
- **Продуктовые принципы** (5 штук) — основа всех решений, если идея противоречит = не делаем:
  1. Простота или смерть (понятно за 3 секунды)
  2. Поток прежде монетизации (100 заявок/день → можно монетизировать)
  3. Люди договариваются сами (платформа только соединяет)
  4. Один паттерн для всех (заявка/доступность/отклик)
  5. Своё > чужое (не копируем Яндекс, делаем как у нас)
- **Национальный контекст:** "Сделано сахалар для сахалар" — продукт для своих, понятный, простой
- **Почему 4 сервиса:** покрывают 80% повседневных задач саха людей
- **Единый принцип:** один интерфейсный паттерн для всех сервисов
- **Платформенная ценность:** единый профиль, кросс-активность, общее доверие, локальный рынок
- **Монетизация:** бесплатно для заказчиков, платят исполнители
- **Конкретные цели:** 6м (5k users), 12м (20k users, 300–500k ₽/мес)
- **Decision Log** с архитектурными решениями платформы

### Updated
- Убран дублирующий раздел "Главное правило" (теперь это Продуктовые принципы)

### Vision Phase — COMPLETE ✅

| Документ | Тип | Статус |
|----------|-----|--------|
| **iind-app-vision.md** | Платформа | ✅ Complete |
| **ayan-vision.md** | Сервис (попутки) | ✅ Complete |
| **uus-vision.md** | Сервис (услуги) | ✅ Complete |
| **tal-vision.md** | Сервис (запись) | ✅ Complete |
| **agal-vision.md** | Сервис (доставка) | ✅ Complete |

**Все 5 vision-документов готовы к Implementation Phase!**

---

## 2026-04-18 — AGAL Vision Document Created (IMPROVED)

### Added
- `vault/wiki/architecture/tal-vision.md` — полный vision-документ для сервиса TAL (Тал)
  - 4 сценария: запись "сейчас", запись на время, запрос (fallback), постоянный мастер
  - 4 статуса мастера: 🟢 сейчас / 🕓 позже / 📅 завтра / 🔴 занят
  - 3 категории услуг: 💇 Красота / 🏠 Дом / 🔧 Ремонт
  - Механика: один тап для мастера, мгновенная запись для клиента
  - Монетизация: подписка мастеров, продвижение, комиссия (позже)
  - Роль: retention-драйвер экосистемы (регулярный трафик)
  - Decision Log с архитектурными решениями

## 2026-04-18 — Vision Documents Refactored (Clean Architecture)

### Refactored AYAN
- **Создано:** `vault/wiki/architecture/ayan-vision.md` — чистый vision без кода
- **Удалено:** `vault/wiki/architecture/ayan-rewrite-design.md` — содержал технические детали (API, TypeScript, архитектура)
- **Что осталось:** Vision, сценарии, механики, монетизация, Decision Log
- **Что убрано:** TypeScript interfaces, API endpoints, file structure, Legacy

### Updated UUS
- `vault/wiki/architecture/uus-vision.md` — добавлен Decision Log
- Убрано техническое упоминание "polling"

### UUS Vision Details
- Updated terminology: "исполнитель" вместо "мастер" (шире аудитория)
- 4 категории задач: 🏠 Дом, 🔧 Ремонт, 📦 Доставка, 🌿 Другое
- Key mechanics: лимит откликов (3–5), выбор исполнителя, авто-закрытие заявки
- 3 сценария: разовая задача, постоянный исполнитель, удалённая/офисная задача
- Trust & Safety минимум (без усложнения)
- Потенциал и монетизация: плата за отклик, подписка, премиум-размещение
- MVP scope: создать заявку → лента → отклик → выбор → контакт

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
