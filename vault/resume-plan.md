# Resume Plan — 2026-04-22

> Цель: быстро восстановить контекст, понять где мы остановились, и продолжить Phase 1 без повторного аудита.

---

## Stop Point

- Текущая ветка: `front/ayan`
- Локальная ветка: `ahead 1` относительно `origin/front/ayan`
- Незакоммиченные изменения: `frontend/package.json`, `frontend/package-lock.json`, `frontend/vitest.config.ts`, `frontend/tests/unit/validators.test.ts`, обновления в `vault/*`
- Последнее завершённое действие: `vitest` setup доведён до рабочего **baseline** состояния (`test`, `test:watch`, `vitest.config.ts`, plain TS smoke test)

## Где мы остановились

### Frontend

- AYAN на фронте собран и работает **на mock API**
- Готово: лента поездок/запросов, фильтры, единый `AyanCreateSlideover`, детали поездки/запроса, отклики, принятие/отклонение, Telegram contact link
- Основной stop point фронта: UI почти доведён, но реальная интеграция ещё не начата

### Backend

- Backend Phase 1 фактически не начат как real API
- В репо всё ещё старый mock-контур `/ayan/orders/*`
- Текущий backend не совпадает с новым контрактом AYAN: `trips / requests / responses / my/*`
- Миграций, моделей и реальной persistence-логики под AYAN пока нет
- После текущего хода уже добавлен contract-aligned Laravel skeleton, но он ещё не проверен рантаймом

---

## Главные выводы аудита

### Что реально готово

1. Frontend AYAN MVP готов в mock-режиме
2. Vault в целом отражает направление работ, но не фиксирует точку остановки
3. API контракт AYAN уже сместился на правильную модель: `trips`, `requests`, `responses`

### Что блокирует следующий этап

1. Backend всё ещё на старом `OrderController` и маршрутах `/ayan/orders/*`
2. `frontend/app/config/api.config.ts` всё ещё держит `USE_MOCK_API = true`
3. `useAuth.ts` ждёт `config.public.telegramBotId`, но этот runtime config не описан в `frontend/nuxt.config.ts`
4. В `frontend/app/app.vue` overlay loader сейчас закомментирован, хотя в changelog он описан как активный
5. Базовый `vitest` setup уже есть, но он покрывает только plain TS smoke tests, не Nuxt/composable runtime
6. В текущей среде нет `php`, `composer`, `docker`, поэтому backend нельзя прогнать локально

### Технический долг, который всплыл во время аудита

1. `frontend/app/components/AppBottomNav.vue` не отражён в `vault/CODE_MAP.md`
2. `vault/master_index.md` и старые записи changelog держали старый счётчик задач спринта
3. Часть wiki-страниц (`roadmap`, `system-design`, `browser-back-button`) устарела относительно текущего кода

---

## Приоритетный план

### P0. Зафиксировать точку продолжения

- Использовать этот файл как главный handoff для Phase 1
- Перед началом новой задачи читать:
  - `vault/sprint.md`
  - `vault/resume-plan.md`
  - `vault/wiki/services/ayan/api-contract.md`
  - `vault/wiki/services/ayan/backend-bringup.md`

### P1. Довести backend до контракта AYAN

**Цель:** убрать старый `orders`-слой и дать фронту реальный API под текущие composables.

**Текущий статус:** contract-aligned routes/controllers/models/migrations уже добавлены как skeleton. Следующий шаг — прогнать это в реальном Laravel runtime и заменить mock-логику на persistence.

**Начинать с файлов:**
- `backend/routes/api.php`
- `backend/app/Http/Controllers/Ayan/OrderController.php`
- `vault/wiki/services/ayan/api-contract.md`

**Что делать:**
1. Поднять рабочий Laravel runtime с `php` + `composer`
2. Прогнать миграции `users`, `trips`, `requests`, `responses`
3. Прогнать и дочистить модели под новый контракт
4. Довести новые endpoints вместо old `orders` API:
   - `GET/POST /ayan/trips`
   - `GET/POST /ayan/requests`
   - `GET/POST /ayan/*/{id}/responses`
   - `PATCH/DELETE /ayan/responses/{id}`
   - `GET /ayan/my/trips`
   - `GET /ayan/my/requests`
   - `GET /ayan/my/responses`
5. Вернуть ответ в формате, который ждёт фронт: `{ success, data }`
6. Удалить legacy `Ayan/OrderController.php`, когда новый runtime слой будет подтверждён

### P2. Переключить AYAN с mock на real API

**Цель:** после backend-ready пройти весь MVP flow без генераторов mock-данных.

**Файлы:**
- `frontend/app/config/api.config.ts`
- `frontend/services/ayan/app/composables/useAyanTrips.ts`
- `frontend/services/ayan/app/composables/useAyanRequests.ts`
- `frontend/services/ayan/app/composables/useAyanResponses.ts`
- `frontend/services/ayan/app/composables/useAyanMy.ts`

**Что делать:**
1. Переключить `USE_MOCK_API = false`
2. Проверить shape всех ответов против `services/ayan/app/types/ayan.ts`
3. Пройти сценарии:
   - create trip
   - create request
   - feed filters
   - detail page
   - create response
   - accept/reject response
   - contact link
   - my trips / my requests / my responses

### P3. Закрыть auth-конфигурацию

**Цель:** убрать расхождение между `useAuth.ts` и runtime config.

**Файлы:**
- `frontend/app/composables/useAuth.ts`
- `frontend/nuxt.config.ts`
- `.env.example`

**Что делать:**
1. Решить, нужен ли browser OAuth прямо сейчас для MVP
2. Если нужен: добавить `NUXT_PUBLIC_TELEGRAM_BOT_ID` в env и runtime config
3. Если не нужен: временно упростить flow и явно задокументировать TMA-only режим

### P4. Закончить тестовый контур

**Цель:** развить текущий baseline `vitest` до полезного regression layer.

**Текущий статус:** базовый setup уже готов для plain TS unit tests; Nuxt/composable harness ещё не настроен.

**Файлы:**
- `frontend/package.json`
- `frontend/package-lock.json`
- будущий `frontend/vitest.config.ts`
- будущие тесты AYAN composables / utils

**Что делать:**
1. Оставить текущий базовый setup как foundation
2. Следующим пакетом расширить plain TS coverage для `formatters` / `validators`
3. Отдельной задачей решить, нужен ли Nuxt-aware test harness для composables AYAN
4. Держать smoke suite быстрой, чтобы она была обязательной перед backend integration

### P5. После backend integration сделать короткий vault cleanup

**Что обновить:**
1. `vault/wiki/architecture/roadmap.md`
2. `vault/wiki/architecture/system-design.md`
3. `vault/wiki/architecture/browser-back-button.md`
4. Удалить мёртвые `[[wikilinks]]` и старые ссылки на `raw/*`

---

## Рекомендуемый следующий практический шаг

Если продолжать прямо с текущей точки, следующий нормальный шаг такой:

1. Подтвердить, что backend будет приводиться к новому контракту, а не развиваться вокруг `orders`
2. Реализовать backend Phase `1.1` + `1.2`
3. Только после этого включать real API на фронте

## Короткая версия в одну строку

Мы остановились после почти готового AYAN frontend на mock API; следующий реальный этап — заменить backend old `/ayan/orders/*` на новый contract-aligned API и только потом переключать фронт на real.
