# Resume Plan — 2026-04-23

> Цель: быстро восстановить контекст, понять где мы остановились, и продолжить Phase 1 без повторного аудита.

---

## Stop Point

- Текущая ветка: `front/ayan`
- `backend` и `frontend real API switch` уже закоммичены и запушены в `origin/front/ayan`
- Текущие рабочие незакоммиченные изменения: `.env.example`, `frontend/app/composables/useAuth.ts`, `frontend/nuxt.config.ts`, `vault/CODE_MAP.md`, `vault/logs/changelog.md`, `vault/resume-plan.md`
- Локальный dev flow для теста real backend: `frontend/.env` (ignored) с `NUXT_PUBLIC_API_BASE=http://89.22.226.34/api` и `NUXT_PUBLIC_DEV_INIT_DATA=test`
- `gh-pages` ветка уже опубликована: static output запушен отдельным temp-repo commit'ом `bff6aa5`
- Expected URL: `https://iindev-solutions.github.io/iindiinda-app/`
- GitHub Pages уже live: `/` и `/ayan` отвечают `200`, rebased asset path `/iindiinda-app/assets/*` тоже отвечает `200`
- Прямой AYAN VPS smoke уже зелёный: два synthetic Telegram payload юзера прошли `POST /auth/telegram` → create trip/request → respond → accept → `my/*`
- Важный нюанс для следующего деплоя: прямой build с `NUXT_APP_BASE_URL=/iindiinda-app/` ломает Nuxt prerender; рабочий flow сейчас такой:
  1. build с `NUXT_APP_BASE_URL=/`
  2. rebased output: `href/src /assets/*` → `/iindiinda-app/assets/*`, `app.baseURL` → `/iindiinda-app/`
  3. publish `.output/public` в `gh-pages`
- Важно: в generated HTML всё ещё сериализуется ключ `public.devInitData` как пустая строка; не публиковать deploy build с непустым `NUXT_PUBLIC_DEV_INIT_DATA`

### Session-Restart Handoff (коротко)

Когда открывается новая сессия, стартовый prompt:

```text
Read vault/resume-plan.md and continue from “Stop Point”.
Current task: run AYAN browser UI flow against VPS backend and confirm local auth/bootstrap behavior.
```

Минимальный безопасный план на следующую сессию:
1. Поднять локально `frontend/` против VPS API (`frontend/.env` с `NUXT_PUBLIC_API_BASE=http://89.22.226.34/api`)
2. Для browser-only smoke path при необходимости включить `NUXT_PUBLIC_DEV_INIT_DATA=test`
3. Пройти UI smoke: `/` → `/ayan` → feed → detail pages
4. Пройти create/respond/accept/contact/`my/*` из браузера, не только direct API
5. После UI pass обновить `vault/logs/changelog.md` и `vault/resume-plan.md` под новый stop point

## Где мы остановились

### Frontend

- AYAN на фронте уже собран и переключён на **real API** (`USE_MOCK_API = false`)
- Готово: лента поездок/запросов, фильтры, единый `AyanCreateSlideover`, детали поездки/запроса, отклики, принятие/отклонение, Telegram contact link
- `npm run typecheck` и `npm run lint` проходят после переключения
- Browser auth intentionally не запускает старый OAuth flow до появления real backend support; TMA login path остаётся основным
- Для local frontend against VPS нужен `frontend/.env` с `NUXT_PUBLIC_API_BASE=http://89.22.226.34/api`; для browser-only dev smoke path можно добавить `NUXT_PUBLIC_DEV_INIT_DATA=test`
- GitHub Pages deploy уже live: `https://iindev-solutions.github.io/iindiinda-app/` и `/ayan` отвечают `200`
- Основной stop point фронта: direct API smoke уже подтверждён, теперь нужно пройти реальный UI flow против VPS backend и затем закрыть production-grade Telegram verification

### Backend

- Backend Phase 1 уже не skeleton-only: на VPS поднят рабочий Laravel runtime в `backend/`
- AYAN routes теперь реально зарегистрированы как `trips / requests / responses / my/*`
- Миграции `users`, `trips`, `requests`, `responses`, `personal_access_tokens` прогнаны на MySQL
- `AuthController` уже выдаёт реальный Sanctum token, `UserController` читает authenticated user
- `TripController`, `RequestController`, `ResponseController`, `MyController` переведены с sample arrays на MySQL persistence
- Guest API transport починен: protected `api/*` больше не падают в missing `login` route, а отвечают JSON `401`
- Production-grade Telegram `initData` verification пока ещё не реализована: сейчас работает stub `init_data = test` + простой parse payload

---

## Главные выводы аудита

### Что реально готово

1. Frontend AYAN уже переключён на real API и GitHub Pages deploy live
2. Backend AYAN runtime реально поднят на VPS и отвечает по HTTP
3. API контракт AYAN подтверждён direct smoke flow для основных persistence endpoints
4. Базовый backend regression layer теперь есть: auth + guest auth handling + AYAN persistence feature tests

### Что блокирует следующий этап

1. Нужен реальный frontend browser integration pass против VPS backend
2. Browser auth intentionally disabled until OAuth / Telegram verification is wired end-to-end
3. Telegram `initData` verification на backend пока не production-grade
4. В текущей среде нет `php`, `composer`, `docker`, поэтому backend проверяется только на VPS
5. Базовый `vitest` setup уже есть, но Nuxt/composable frontend harness ещё не настроен

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

**Текущий статус:** runtime + persistence уже подняты на VPS. Следующий шаг по backend — дочистить auth до production-grade Telegram verification и затем коммитнуть/запушить весь Laravel runtime пакет в git.

**Ключевые файлы:**
- `backend/bootstrap/app.php`
- `backend/app/Http/Middleware/ForceJsonResponse.php`
- `backend/app/Http/Controllers/AuthController.php`
- `backend/app/Http/Controllers/UserController.php`
- `backend/app/Http/Controllers/Ayan/TripController.php`
- `backend/app/Http/Controllers/Ayan/RequestController.php`
- `backend/app/Http/Controllers/Ayan/ResponseController.php`
- `backend/app/Http/Controllers/Ayan/MyController.php`
- `backend/database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php`
- `backend/tests/Feature/AuthApiTest.php`
- `backend/tests/Feature/AyanAuthTest.php`
- `backend/tests/Feature/AyanPersistenceTest.php`

**Что делать:**
1. Закоммитить и запушить Laravel runtime base + backend fixes, чтобы future `git pull` не зависел от ручного VPS состояния
2. Реализовать настоящую Telegram `initData` verification в `POST /api/auth/telegram`
3. Решить, нужен ли fallback/stub режим только для dev/VPS smoke path
4. После этого можно считать backend Phase 1 реально готовым для frontend integration

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
4. Проверить, что `POST /api/auth/telegram` и `GET /api/user` реально работают с текущим frontend auth flow

**Текущий статус:**
- `USE_MOCK_API = false` уже включён
- `frontend/nuxt.config.ts` уже знает `public.telegramBotId`
- `useAuth.ts` в browser mode больше не запускает сломанный OAuth flow автоматически

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

1. Пройти AYAN flow в UI против VPS backend
2. Зафиксировать deploy/live + UI integration findings в vault и отдельным commit'ом
3. Отдельным пакетом закрыть настоящую Telegram `initData` verification

## Короткая версия в одну строку

Мы уже подняли и запушили реальный AYAN backend на VPS, переключили фронт на real API, подняли GitHub Pages и прогнали direct API smoke; следующий этап — пройти UI flow в браузере против VPS и потом дочистить настоящую Telegram verification.
