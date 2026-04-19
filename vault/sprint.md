# Sprint — Phase 1 AYAN MVP

> Roadmap: vault/wiki/architecture/roadmap.md
> API: vault/wiki/services/ayan/api-contract.md
> Code: vault/CODE_MAP.md

---

## Phase 1: AYAN MVP

Начало: 2026-04-19
Цель: Рабочий MVP попуток (создание поездки/запроса → лента → отклик → контакт)

### Задачи

| # | Задача | Статус | Блокеры |
|---|--------|--------|---------|
| 1.1 | Backend: миграции (users, trips, requests, responses) | TODO | — |
| 1.2 | Backend: модели + контроллеры AYAN | TODO | 1.1 |
| 1.3 | Frontend: структура AYAN (pages, composables, types) | DONE | — |
| 1.4 | Frontend: создание поездки (форма + API + validation + w-full + labels) | DONE* | — |
| 1.5 | Frontend: создание запроса (форма + API + validation + w-full + labels) | DONE* | — |
| 1.6 | Frontend: лента поездок/запросов + фильтры | IN_PROGRESS | — |
| 1.7 | Frontend: отклик + контакт | IN_PROGRESS | — |
| 1.8 | Frontend: performance (lazy, useLazyAsyncData, loader overlay, prefetch) | DONE | — |
| 1.9 | Integration: mock → real API | TODO | 1.2 |
| 1.10 | QA: все flow работают E2E | TODO | 1.9 |

\* 1.4 и 1.5 — формы и composables работают на mock API. При переключении на real API нужно проверить валидацию ответов.

Статусы: `TODO` `IN_PROGRESS` `DONE` `BLOCKED`

### Блокеры

Пока нет

### Решения

- **Ограниченная палитра `ui.theme.colors`** — убрана. Вызывала отсутствие error/success/warning цветов в Nuxt UI компонентах (FormField не мог передать `color="error"` инпуту)
- **`pageTransition` убран** — конфликтует с `lazy: true` страницами (Suspense рендерит fragment, Transition требует один root). Overlay loader заменяет.
- **`useLazyAsyncData`** вместо `useAsyncData` — навигация мгновенная, данные грузятся после. `await` блокировал рендер страницы.
- **`deep: false`** для списков — нет нужды в глубокой реактивности для API-ответов, экономит proxy overhead.
- **Дубликат `app.config.ts`** — удалён корневой `frontend/app.config.ts`, всё в `frontend/app/app.config.ts` (тот, что реально импортируется билдом)

---

## Next Steps (priority order)

### 1.6 Лента — фильтры по маршруту
- [ ] Добавить UInput поиска (откуда/куда) поверх ленты
- [ ] Фильтр по дате (сегодня/завтра/выбрать)
- [ ] Пустой результат → EmptyState с подсказкой

### 1.7 Отклик + контакт
- [ ] trip/[id].vue — форма отклика (сообщение + отправка)
- [ ] request/[id].vue — форма отклика
- [ ] После принятия отклика → показать контакт (telegram username)
- [ ] Статусы отклика: pending → accepted/rejected

### 1.8 TS: cyan/gray типы
- [ ] BackButton.vue, ErrorMessage.vue — color="cyan" → color="primary" или тип-окаст
- [ ] ui.vue (dev page) — аналогично

### 1.9 Mock → Real API
- [ ] `api.config.ts` → `USE_MOCK_API = false`
- [ ] Проверить все composables на реальном API
- [ ] Обработка 401, 403, 404, 500

### 1.10 QA E2E
- [ ] Создание поездки → появляется в ленте
- [ ] Создание запроса → появляется в ленте
- [ ] Отклик → статус меняется → контакт доступен
- [ ] Мои поездки/запросы → отображаются
- [ ] Ошибки валидации на формах
- [ ] Навигация между сервисами — нет пустого экрана

- Telegram initData validation (real, не mock)
- JWT → Sanctum
- Единый профиль
- i18n 完善

---

## Done: Phase 0 — Foundation ✅

2026-04-18 — 2026-04-19

- 0.1 Очистка старого кода ✅
- 0.2 Composables (useAuth, useUtils) ✅
- 0.3 Components (7 шт) ✅
- 0.4 Types & i18n ✅
- 0.5 Layout & Pages ✅
- 0.6 Mock APIs ✅
- 0.7 Auth & Security ✅
- 0.8 Error & Validation ✅
- 0.9 Storage & Network ✅
- 0.10 i18n & Polish ✅
