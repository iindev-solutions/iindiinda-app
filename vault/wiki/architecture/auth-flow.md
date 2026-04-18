# Auth Flow

## Overview
Два режима работы: Telegram (production) и Localhost (development).

## Telegram Mode (production)

```
┌──────────┐     ┌──────────────┐     ┌──────────────┐
│ User opens│     │ useAuth      │     │ Backend      │
│ TMA       │     │ auto-login   │     │ /auth/telegram│
└─────┬─────┘     └──────┬───────┘     └──────┬───────┘
      │                  │                     │
      │  1. App loads    │                     │
      │─────────────────→│                     │
      │                  │  2. Read initData   │
      │                  │  from Telegram SDK  │
      │                  │                     │
      │                  │  3. POST /auth/     │
      │                  │  telegram           │
      │                  │────────────────────→│
      │                  │                     │
      │                  │  4. {token, user}   │
      │                  │←────────────────────│
      │                  │                     │
      │                  │  5. Save token      │
      │                  │  (localStorage +    │
      │                  │   useState)         │
      │                  │                     │
      │  6. App ready    │                     │
      │←─────────────────│                     │
```

### Steps
1. `useTg()` инициализируется, получает `initData`
2. `useAuth().autoLogin()` вызывается из middleware
3. Если initData валидный → `POST /auth/telegram` с `X-Telegram-Init-Data` header
4. Backend верифицирует initData через Telegram Bot API
5. Возвращает JWT token + User (с role)
6. Token сохраняется в `useState` + `localStorage`
7. Все последующие запросы используют `Authorization: Bearer {token}`

### Role Selection (MVP)
После первого логина пользователь выбирает роль:
- Пассажир → может создавать заказы
- Водитель → может принимать заказы

Выбор вызывает `POST /user/switch-role`. Роль сохраняется в User.

## Localhost Mode (development)

```
┌──────────┐     ┌──────────────┐     ┌──────────────┐
│ Dev opens │     │ useAuth      │     │ Mock API     │
│ localhost │     │ auto-login   │     │ (in-memory)  │
└─────┬─────┘     └──────┬───────┘     └──────┬───────┘
      │                  │                     │
      │  1. No initData  │                     │
      │  (not in TG)     │                     │
      │                  │                     │
      │                  │  2. Detect:         │
      │                  │  isInTelegram=false  │
      │                  │                     │
      │                  │  3. Mock auto-login  │
      │                  │────────────────────→│
      │                  │                     │
      │                  │  4. Mock user +      │
      │                  │  mock token          │
      │                  │←────────────────────│
      │                  │                     │
      │                  │  5. Save as above   │
```

### Dev Mode Rules
1. Если `isInTelegram === false` → автоматически использовать mock auth
2. Mock создаёт тестового пользователя с выбираемой ролью
3. Mock token = `"mock-token-dev"` (не отправляется на реальный backend)
4. Переключение mock/real через `NUXT_PUBLIC_USE_MOCK_API` env var (runtime, не compile-time)

## Token Persistence

```typescript
// composable: useAuth
const token = useState<string | null>('auth-token', () => {
  if (import.meta.client) {
    return localStorage.getItem('auth-token')
  }
  return null
})

watch(token, (newVal) => {
  if (import.meta.client) {
    if (newVal) localStorage.setItem('auth-token', newVal)
    else localStorage.removeItem('auth-token')
  }
})
```

## Token Refresh
- Token expiry: 7 days
- No refresh token в MVP — по истечении автоматический ре-логин через initData
- На localhost — token бессрочный

## Auth Middleware
Nuxt middleware `auth.global.ts`:
1. Проверяет наличие token
2. Если нет → вызывает `autoLogin()`
3. Если autoLogin fail → показывает ошибку
4. Не блокирует навигацию, но страницы могут проверять `isAuthenticated`

See also: [[wiki/architecture/system-design]], [[wiki/architecture/api-contract]]
