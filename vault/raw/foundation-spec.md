# Foundation SPEC — iindiinda-app

> Part of: [[raw/SPEC]]
> Phase: 0 — Foundation

## Overview

Foundation = общая инфраструктура, которую используют ВСЕ сервисы. Не привязана к конкретному сервису. Базовые строительные блоки.

---

## 1. Project Structure

```
frontend/
├── app/                          # Базовое приложение (Nuxt layer)
│   ├── app.vue                   # Entry point (<UApp> + layouts)
│   ├── app.config.ts             # Nuxt app config
│   ├── composables/              # SHARED composables
│   │   ├── useTg.ts              # Telegram WebApp SDK
│   │   ├── useAuth.ts            # Auth flow (token, user)
│   │   ├── useAPI.ts             # HTTP client (baseURL, headers, request)
│   │   └── useUtils.ts           # Helpers (formatDate, formatPrice, etc.)
│   ├── components/               # SHARED components
│   │   ├── BackButton.vue        # Универсальная кнопка назад
│   │   ├── AppHeader.vue         # Шапка с навигацией
│   │   ├── ServiceCard.vue       # Карточка сервиса на главной
│   │   ├── LoadingSpinner.vue    # Индикатор загрузки
│   │   ├── EmptyState.vue        # Пустое состояние
│   │   └── ErrorMessage.vue      # Сообщение об ошибке
│   ├── layouts/
│   │   └── default.vue           # Базовый layout
│   ├── pages/
│   │   ├── index.vue             # Hub — главная (все 4 сервиса)
│   │   └── ui.vue                # UI demo page
│   ├── types/
│   │   ├── api.ts                # Общие типы API (User, ApiResponse, etc.)
│   │   └── telegram.d.ts         # Telegram SDK types
│   ├── config/
│   │   └── api.config.ts         # USE_MOCK_API toggle
│   └── assets/css/
│       └── main.css              # Глобальные стили
├── services/                     # Service layers (Nuxt layers)
│   ├── ayan/app/                 # AYAN (попутки)
│   ├── uus/app/                  # UUS (услуги)
│   ├── agal/app/                 # AGAL (доставка)
│   └── tal/app/                  # TAL (запись)
└── i18n/locales/
    ├── ru.json
    └── sah.json

backend/                          # Laravel
├── app/
│   ├── Http/Controllers/Api/
│   ├── Models/
│   └── Services/
├── database/migrations/
├── routes/api.php
└── composer.json
```

---

## 2. Composables (Base App)

### 2.1 useTg — Telegram WebApp SDK

**Файл:** `app/composables/useTg.ts` (УЖЕ СУЩЕСТВУЕТ ✅)

**Интерфейс:**
```typescript
interface UseTg {
  isInTelegram: Ref<boolean>           // находимся в Telegram
  webApp: Ref<TelegramWebApp | null>   // WebApp instance
  user: ComputedRef<TelegramUser | null> // из initDataUnsafe
  initData: ComputedRef<string>        // initData для отправки на backend
  version: ComputedRef<string>         // версия SDK
  themeParams: ComputedRef<ThemeParams> // цвета из Telegram
  isReady: ComputedRef<boolean>
  
  // Methods
  supportsVersion(minVersion: string): boolean  // проверка версии
  ready(): void                                  // WebApp.ready()
  expand(): void                                 // WebApp.expand()
  close(): void                                  // WebApp.close()
  showBackButton(): void
  hideBackButton(): void
  onBackButtonClicked(callback: () => void): void
  showMainButton(text: string, onClick: () => void): void
  hideMainButton(): void
  hapticFeedback(type: 'impact' | 'notification' | 'selection'): void
}
```

**Статус:** ✅ Готов (нужна небольшая чистка)

---

### 2.2 useAuth — Authentication

**Файл:** `app/composables/useAuth.ts` (УЖЕ СУЩЕСТВУЕТ ⚠️ — нужна доработка)

**Интерфейс:**
```typescript
interface UseAuth {
  token: Readonly<Ref<string | null>>     // JWT token
  user: Readonly<Ref<User | null>>        // current user
  tgUser: ComputedRef<TelegramUser | null> // from Telegram SDK
  isAuthenticated: ComputedRef<boolean>
  isLoading: Readonly<Ref<boolean>>
  
  login(): Promise<void>                  // авторизация через initData
  logout(): void                          // выход
}
```

**Фичи:**
- Auto-login при первом входе (проверяем token, если нет — логинимся)
- Token persistence через useState + localStorage
- Обработка ошибок авторизации

**Статус:** ⚠️ Есть базовый код, нужно доработать:
- [ ] auto-login logic
- [ ] token persistence (проверить что не теряется при refresh)
- [ ] error handling (показывать пользователю ошибку)

---

### 2.3 useAPI — HTTP Client

**Файл:** `app/composables/useAPI.ts` (УЖЕ СУЩЕСТВУЕТ ✅)

**Интерфейс:**
```typescript
interface UseAPI {
  baseURL: ComputedRef<string>
  headers: ComputedRef<Record<string, string>>
  token: Ref<string | null>
  
  request<T>(
    endpoint: string, 
    options?: { method?: Method; body?: object; params?: Record<string, string> }
  ): Promise<T>
  
  get<T>(endpoint: string, params?: Record<string, string>): Promise<T>
  post<T>(endpoint: string, body?: object): Promise<T>
  put<T>(endpoint: string, body?: object): Promise<T>
  del<T>(endpoint: string): Promise<T>
}
```

**Особенности:**
- Автоматически добавляет `X-Telegram-Init-Data` и `Authorization: Bearer {token}`
- baseURL из runtimeConfig.public.apiBase

**Статус:** ✅ Готов

---

### 2.4 useUtils — Helpers (НОВЫЙ)

**Файл:** `app/composables/useUtils.ts`

```typescript
interface UseUtils {
  // Date/Time
  formatDate(date: string, format?: 'short' | 'long'): string
  formatTime(time: string): string
  formatRelativeTime(date: string): string  // '2 часа назад', 'сегодня', 'вчера'
  
  // Price
  formatPrice(price: number | null): string  // '350 ₽' или 'договорная'
  
  // Validation
  validatePhone(phone: string): boolean
  validatePrice(price: number): { valid: boolean; error?: string }
  
  // Misc
  truncate(text: string, maxLength: number): string
  debounce<T extends (...args: any) => any>(fn: T, delay: number): T
}
```

**Статус:** 🆕 Создать

---

## 3. Components (Base App)

### 3.1 BackButton — Кнопка Назад

**Файл:** `app/components/BackButton.vue` (УЖЕ СУЩЕСТВУЕТ ✅)

**Props:**
```typescript
interface Props {
  fallbackRoute?: string     // куда идти если нет истории
  beforeNavigate?: () => boolean | Promise<boolean>  // можно отменить
  onNavigate?: () => void | Promise<void>  // кастомная логика
  label?: string             // текст кнопки
  forceUi?: boolean          // показывать даже в Telegram
  uiClass?: string
}
```

**Логика:**
- В TMA: использует нативный BackButton + handler
- В браузере: UI кнопка + router.back()
- Проверяет same-section (если внутри одного раздела — назад, иначе fallback)

**Статус:** ✅ Готов

---

### 3.2 AppHeader — Шапка (НОВЫЙ)

**Файл:** `app/components/AppHeader.vue`

```typescript
interface Props {
  title: string
  showBack?: boolean
  fallbackRoute?: string
}
```

**Использование:**
```vue
<AppHeader title='Попутки' :show-back='true' fallback-route='/ayan' />
```

**Статус:** 🆕 Создать

---

### 3.3 ServiceCard — Карточка сервиса (НОВЫЙ)

**Файл:** `app/components/ServiceCard.vue`

```typescript
interface Props {
  name: string
  description: string
  icon: string              // lucide icon name
  to: string               // route
  active?: boolean         // false = disabled
}
```

**Дизайн:**
- Внутри сервиса: cyan border, opacity 100%
- Вне сервиса: gray border, opacity 60%, не кликабелен
- Haptic feedback при клике

**Статус:** 🆕 Создать (можно вынести из index.vue)

---

### 3.4 LoadingSpinner — Загрузка (НОВЫЙ)

**Файл:** `app/components/LoadingSpinner.vue`

```typescript
interface Props {
  size?: 'sm' | 'md' | 'lg'
  label?: string           // текст 'Загрузка...'
}
```

**Статус:** 🆕 Создать

---

### 3.5 EmptyState — Пустое состояние (НОВЫЙ)

**Файл:** `app/components/EmptyState.vue`

```typescript
interface Props {
  title: string
  description?: string
  icon?: string            // lucide icon
  actionLabel?: string     // текст кнопки
  actionTo?: string        // route для кнопки
}
```

**Статус:** 🆕 Создать

---

### 3.6 ErrorMessage — Ошибка (НОВЫЙ)

**Файл:** `app/components/ErrorMessage.vue`

```typescript
interface Props {
  message: string
  retryLabel?: string
  onRetry?: () => void
}
```

**Статус:** 🆕 Создать

---

## 4. Types (Base App)

### 4.1 Общие типы — `app/types/api.ts` (УЖЕ СУЩЕСТВУЕТ ⚠️)

**Сейчас там:** User, AuthResponse, PaginatedResponse, ApiError

**Нужно добавить:**
```typescript
// Базовые
interface ApiResponse<T> {
  success: boolean
  data?: T
  message?: string
  errors?: Record<string, string[]>
}

// User (уже есть, проверить актуальность)
interface User {
  id: number
  telegram_id: number
  username: string | null
  first_name: string
  role: 'passenger' | 'driver' | 'carrier' | 'master'
  rating?: number
  completed_orders?: number
  created_at: string
}
```

**Статус:** ⚠️ Доработать — убрать лишнее (TaxiOrder), оставить только базовые типы

---

### 4.2 Telegram SDK — `app/types/telegram.d.ts` (УЖЕ СУЩЕСТВУЕТ ✅)

**Статус:** ✅ Готов

---

## 5. i18n — Интернационализация

### 5.1 Структура ключей

```
ru.json / sah.json
├── common                    # Общие фразы
│   ├── back                  # Назад
│   ├── save                  # Сохранить
│   ├── cancel                # Отмена
│   ├── loading               # Загрузка...
│   ├── error                 # Ошибка
│   ├── retry                 # Попробовать снова
│   └── empty                 # Ничего не найдено
├── services                  # Общие для всех сервисов
│   ├── name                  # Название
│   └── desc                  # Описание
├── backButton
│   ├── label                 # Назад
│   └── ariaLabel             # Вернуться назад
└── errors
    ├── network               # Ошибка сети
    ├── unauthorized          # Нужна авторизация
    └── server                # Серверная ошибка
```

### 5.2 Сервисные ключи (будут в service layers)

```
ayan.name = Попутки
ayan.desc = Поездки между городами

uus.name = Услуги
uus.desc = Найти исполнителя

tal.name = Запись
tal.desc = Запись к мастерам

agal.name = Доставка
agal.desc = Отправить посылку
```

**Статус:** 🆕 Создать полную структуру ключей

---

## 6. Design System

### 6.1 Цвета (dark only)

```css
/* Background */
--color-background: #0a0c0e

/* Primary — Cyan */
--color-cyan-500: #5edac6
--color-cyan-400: #7de5d4
--color-cyan-600: #4bc9b3

/* Text */
--color-text: #eff3f5
--color-text-muted: #8da3ad

/* Grays */
--color-gray-100: #f3f4f6
--color-gray-400: #9ca3af
--color-gray-600: #4b5563
--color-gray-800: #1f2937
--color-gray-900: #111827
```

### 6.2 Spacing

```css
/* Padding (mobile-first) */
--padding-page-x: 20px        /* px-5 */
--padding-page-y: 32px        /* py-8 */

/* Gaps */
--gap-card: 12px              /* gap-3 */
--gap-section: 24px           /* gap-6 */
```

### 6.3 Border Radius

```css
--radius-card: 16px           /* rounded-2xl */
--radius-button: 12px         /* rounded-xl */
--radius-input: 8px           /* rounded-lg */
```

### 6.4 Components

- Buttons: @nuxt/ui UButton (variant: solid, ghost, outline)
- Inputs: @nuxt/ui UInput
- Cards: custom with bg-gray-800
- Icons: lucide-vue-next

---

## 7. Layout — default.vue (УЖЕ СУЩЕСТВУЕТ ⚠️)

**Сейчас:** простой header с кнопкой назад

**Проблемы:**
- Дублирование с BackButton (back button в header + BackButton component)
- Нет единой структуры для всех страниц

**Решение:**
- Header остаётся в layout (для всех страниц кроме главной)
- BackButton используется только на вложенных страницах
- Или убрать BackButton component и использовать только header

**Статус:** ⚠️ Нужно уточнить архитектуру навигации

---

## 8. Mock API — Использование

### 8.1 API Toggle

**Файл:** `app/config/api.config.ts`

```typescript
export const USE_MOCK_API = true  // false = real API
export const MOCK_CONFIG = {
  errorRate: 0.1,           // 10% шанс ошибки
  baseDelay: 300,           // базовая задержка (ms)
  maxExtraDelay: 500,       // максимальная доп. задержка
  autoAcceptDelay: 5000     // задержка автопринятия (для демо)
}
```

### 8.2 Service-specific Mock API

Каждый сервис может иметь свой мок:
- `app/composables/useMockAPI.ts` — общий (auth, user)
- `services/ayan/app/composables/useAyanMockAPI.ts` — для AYAN
- etc.

**Принцип:** useServiceAPI() проверяет USE_MOCK_API и выбирает нужный мок или реальный API.

---

## 9. TODOs — Foundation

### Phase 0.1: Очистка (1-2 дня)

- [ ] Удалить старый код AYAN из `services/ayan/` (баговый, переделаем)
- [ ] Очистить `app/types/api.ts` (убрать TaxiOrder, оставить базовые типы)
- [ ] Удалить `app/composables/useMockAPI.ts` (старый, заменить на service-specific)
- [ ] Удалить `app/composables/useTaxiAPI.ts` (специфичный для AYAN, не для foundation)

### Phase 0.2: Composables (2-3 дня)

- [ ] Доработать `useAuth.ts` (auto-login, token persistence)
- [ ] Создать `useUtils.ts`
- [ ] Проверить `useTg.ts` на актуальность
- [ ] Проверить `useAPI.ts` на актуальность

### Phase 0.3: Components (2-3 дня)

- [ ] Создать `AppHeader.vue`
- [ ] Создать `ServiceCard.vue` (вынести из index.vue)
- [ ] Создать `LoadingSpinner.vue`
- [ ] Создать `EmptyState.vue`
- [ ] Создать `ErrorMessage.vue`
- [ ] Проверить `BackButton.vue` на актуальность

### Phase 0.4: Types & i18n (1-2 дня)

- [ ] Обновить `app/types/api.ts`
- [ ] Создать полную структуру i18n ключей (ru + sah)
- [ ] Добавить переводы для всех компонентов

### Phase 0.5: Layout & Pages (1-2 дня)

- [ ] Уточнить архитектуру навигации (header + BackButton)
- [ ] Обновить `default.vue` layout
- [ ] Обновить `index.vue` (использовать ServiceCard)
- [ ] Обновить `ui.vue` (если нужен)

### Phase 0.6: Mock APIs (2-3 дня)

- [ ] Создать базовый mock для auth (login, logout)
- [ ] Создать mock для user (get, update)
- [ ] Подготовить структуру для service-specific mocks

---

## 10. Dependencies

```json
{
  // Уже установлены
  @nuxt/ui: ^4.3.0
  @nuxtjs/i18n: ^10.2.1
  nuxt: ^4.2.2
  zod: ^4.3.6
  
  // Дополнительно (если нужны)
  @vueuse/core: ^12.0.0  // useIntervalFn, useLocalStorage, etc.
  lucide-vue-next: ^0.500.0  // icons
  date-fns: ^4.0.0  // date formatting
}
```

---

## 11. Architecture Decisions

### 11.1 Service Layers = Nuxt Layers

```
frontend/nuxt.config.ts:
extends: ['./services/ayan', './services/tal', './services/uus', './services/agal']
```

Каждый сервис — Nuxt layer. Pages, composables, components в `services/*/app/`.

### 11.2 Shared vs Service-specific

| Что | Где | Почему |
|-----|-----|--------|
| useTg, useAuth, useAPI | app/composables/ | Все сервисы используют |
| BackButton, AppHeader | app/components/ | Все сервисы используют |
| User, ApiResponse types | app/types/ | Все сервисы используют |
| useAyanAPI | services/ayan/app/composables/ | Только AYAN |
| ayan pages | services/ayan/app/pages/ | Только AYAN |

### 11.3 Navigation Strategy

1. Hub page (`/`) — ссылки на 4 сервиса
2. Service page (`/ayan`) — обёртка с `<NuxtPage />`
3. Sub-pages (`/ayan/create`, `/ayan/orders`) — внутри сервиса
4. BackButton — для возврата внутри сервиса
5. Layout header — для возврата на уровень сервиса

### 11.4 Auth Flow

```
1. User opens app
2. useAuth checks token in useState
3. If no token → auto-login via /auth/telegram with initData
4. Token stored in useState (persists on refresh via localStorage)
5. All API calls use token in Authorization header
```

---

## 12. Verification Checklist

После завершения Foundation проверяем:

- [ ] Все composables экспортируются и работают
- [ ] Все компоненты используются в pages
- [ ] i18n работает (ru + sah)
- [ ] Mock API toggle работает
- [ ] Нет захардкоженных строк в UI
- [ ] Типы корректные (TypeScript strict)
- [ ] Lint/typecheck проходят без ошибок

---

**Следующий шаг:** Начать Phase 0.1 — очистка старого кода