# Auth Flow — iind.app

**Date:** 2026-04-19
**Status:** Design Complete → Ready for Implementation

---

## Overview

iind.app поддерживает два режима авторизации:
1. **Telegram Mini App (TMA)** — instant auth через `initData`
2. **Browser (outside Telegram)** — OAuth redirect flow

---

## Auth Modes

### Mode 1: TMA (Telegram Mini App)

**Flow:**
```
1. User opens app in Telegram
2. Frontend detects window.Telegram.WebApp.initData
3. Auto-login: POST /auth/telegram { init_data }
4. Backend validates initData signature
5. Backend returns { token, user }
6. Frontend saves token → authenticated
```

**Advantages:**
- Instant (no redirect)
- Secure (Telegram validates)
- No user action required

**Edge cases:**
- initData expired → fallback to OAuth
- Validation failed → show error, retry

---

### Mode 2: Browser (OAuth)

**Flow:**
```
1. User opens app in browser
2. Frontend shows "Login with Telegram" button
3. Click → redirect to oauth.telegram.org/auth
4. User authorizes in Telegram
5. Redirect back to /auth/callback?hash=...
6. Frontend: POST /auth/telegram/oauth { hash }
7. Backend validates hash, returns { token, user }
8. Frontend saves token → authenticated
```

**Advantages:**
- Works outside Telegram
- Standard OAuth flow
- Secure (Telegram validates hash)

**Edge cases:**
- User cancels → show error, allow retry
- Hash invalid → show error
- Token expired → re-login

---

## Implementation

### Frontend: useAuth Composable

```typescript
export const useAuth = () => {
  const { initData, isInTelegram } = useTg()
  const api = useAPI()
  
  const token = useState<string | null>('auth-token', () => null)
  const user = useState<User | null>('auth-user', () => null)
  const isAuthenticated = computed(() => !!token.value)
  const isLoading = ref(false)
  
  // TMA mode: auto-login with initData
  const loginWithInitData = async () => {
    if (!initData.value) throw new Error('No initData')
    
    isLoading.value = true
    try {
      const response = await api.post<AuthResponse>('/auth/telegram', {
        init_data: initData.value
      })
      token.value = response.token
      user.value = response.user
    } finally {
      isLoading.value = false
    }
  }
  
  // Browser mode: OAuth redirect
  const loginWithOAuth = () => {
    const botId = useRuntimeConfig().public.telegramBotId
    const origin = window.location.origin
    const redirectUrl = `${origin}/auth/callback`
    
    window.location.href = `https://oauth.telegram.org/auth?bot_id=${botId}&origin=${origin}&request_access=write&return_to=${redirectUrl}`
  }
  
  // OAuth callback handler
  const handleOAuthCallback = async (hash: string) => {
    isLoading.value = true
    try {
      const response = await api.post<AuthResponse>('/auth/telegram/oauth', { hash })
      token.value = response.token
      user.value = response.user
      navigateTo('/')
    } finally {
      isLoading.value = false
    }
  }
  
  // Unified login (auto-detect mode)
  const login = async () => {
    if (isInTelegram.value) {
      await loginWithInitData()
    } else {
      loginWithOAuth()
    }
  }
  
  const logout = () => {
    token.value = null
    user.value = null
  }
  
  return {
    token: readonly(token),
    user: readonly(user),
    isAuthenticated,
    isLoading: readonly(isLoading),
    login,
    loginWithInitData,
    loginWithOAuth,
    handleOAuthCallback,
    logout
  }
}
```

---

### Frontend: Auto-Init Plugin

```typescript
// plugins/init.ts
export default defineNuxtPlugin(async () => {
  const { ready, expand, isInTelegram } = useTg()
  const { login, isAuthenticated } = useAuth()
  
  // Init Telegram SDK (if in TMA)
  if (isInTelegram.value) {
    ready()
    expand()
  }
  
  // Auto-login if not authenticated
  if (!isAuthenticated.value) {
    try {
      await login()
    } catch (error) {
      console.error('[init] Auto-login failed:', error)
      // Don't block app, user can retry manually
    }
  }
})
```

---

### Frontend: OAuth Callback Page

```vue
<!-- pages/auth/callback.vue -->
<script setup lang="ts">
const route = useRoute()
const { handleOAuthCallback } = useAuth()

onMounted(async () => {
  const hash = route.query.hash as string
  if (hash) {
    await handleOAuthCallback(hash)
  } else {
    navigateTo('/')
  }
})
</script>

<template>
  <div class="flex items-center justify-center min-h-screen">
    <LoadingSpinner size="lg" />
  </div>
</template>
```

---

### Frontend: Auth Middleware

```typescript
// middleware/auth.ts
export default defineNuxtRouteMiddleware((to, from) => {
  const { isAuthenticated } = useAuth()
  
  if (!isAuthenticated.value) {
    return navigateTo('/')
  }
})
```

**Usage:**
```vue
<script setup>
definePageMeta({
  middleware: 'auth'
})
</script>
```

---

### Backend: Endpoints

#### POST /auth/telegram (TMA mode)

**Request:**
```json
{
  "init_data": "query_id=...&user=...&auth_date=...&hash=..."
}
```

**Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "id": 1,
    "telegram_id": 123456789,
    "username": "john_doe",
    "first_name": "John",
    "role": "passenger",
    "created_at": "2026-04-19T10:00:00Z",
    "updated_at": "2026-04-19T10:00:00Z"
  }
}
```

**Validation:**
1. Parse initData
2. Verify hash (HMAC-SHA256 with bot token)
3. Check auth_date (not older than 24h)
4. Find or create user by telegram_id
5. Generate JWT token
6. Return token + user

---

#### POST /auth/telegram/oauth (Browser mode)

**Request:**
```json
{
  "hash": "id=123456789&first_name=John&username=john_doe&photo_url=...&auth_date=...&hash=..."
}
```

**Response:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": { ... }
}
```

**Validation:**
1. Parse hash
2. Verify hash (HMAC-SHA256 with bot token)
3. Check auth_date (not older than 24h)
4. Find or create user by id (telegram_id)
5. Generate JWT token
6. Return token + user

---

## Security

### initData Validation (TMA)

```php
// Laravel example
public function validateInitData(string $initData): bool
{
    $data = [];
    parse_str($initData, $data);
    
    $hash = $data['hash'] ?? '';
    unset($data['hash']);
    
    ksort($data);
    $dataCheckString = http_build_query($data, '', "\n");
    
    $secretKey = hash_hmac('sha256', config('telegram.bot_token'), 'WebAppData', true);
    $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);
    
    return hash_equals($calculatedHash, $hash);
}
```

### OAuth Hash Validation (Browser)

```php
public function validateOAuthHash(array $data): bool
{
    $hash = $data['hash'] ?? '';
    unset($data['hash']);
    
    ksort($data);
    $dataCheckString = implode("\n", array_map(
        fn($k, $v) => "$k=$v",
        array_keys($data),
        $data
    ));
    
    $secretKey = hash('sha256', config('telegram.bot_token'), true);
    $calculatedHash = hash_hmac('sha256', $dataCheckString, $secretKey);
    
    return hash_equals($calculatedHash, $hash);
}
```

---

## Environment Variables

```bash
# Frontend (.env)
NUXT_PUBLIC_TELEGRAM_BOT_ID=123456789

# Backend (.env)
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
JWT_SECRET=your-secret-key
JWT_TTL=10080  # 7 days in minutes
```

---

## Decision Log

### Why Two Auth Modes?

**Problem:** Разработка в браузере без Telegram невозможна с только initData

**Decision:** Поддерживаем оба режима (TMA + OAuth)

**Alternatives:**
- Only TMA → нельзя разрабатывать в браузере
- Only OAuth → медленнее в TMA (redirect)

**Trade-offs:**
- Больше кода (два flow)
- Но: удобная разработка + быстрый UX в TMA

---

### Why Auto-Login in Plugin?

**Problem:** Каждая страница должна проверять auth вручную

**Decision:** Auto-login в plugin при старте приложения

**Alternatives:**
- Manual login on each page → дублирование кода
- Middleware only → не работает для public pages

**Trade-offs:**
- Plugin блокирует старт на ~200ms
- Но: чистый код, единая точка входа

---

### Why JWT Token?

**Problem:** Нужно хранить сессию между запросами

**Decision:** JWT token в localStorage + Authorization header

**Alternatives:**
- Cookies → не работает в TMA (cross-origin)
- Session → не работает в SPA

**Trade-offs:**
- JWT нельзя отозвать (до истечения TTL)
- Но: stateless, работает везде
