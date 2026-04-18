# Foundation Audit — 2026-04-18

> Current state of base app (app/) and services/

---

## 1. EXISTING FILES

### 1.1 Composables — `app/composables/`

| Файл | Статус | Комментарий |
|------|--------|-------------|
| `useTg.ts` | ✅ Работает | Telegram SDK wrapper, полный функционал |
| `useAuth.ts` | ⚠️ Частично | Базовая логика, нет auto-login, проблемы с persistence |
| `useAPI.ts` | ✅ Работает | HTTP client, всё ОК |
| `useTaxiAPI.ts` | ❌ Удалить | Специфичный для AYAN, не для foundation |
| `useMockAPI.ts` | ❌ Удалить | Старый мок с TaxiOrder, нужно переделать |

**Проблемы useAuth:**
- Нет авто-логина при первом входе
- Token не персистится (useState сбрасывается при refresh)
- Ошибки не показываются пользователю

### 1.2 Components — `app/components/`

| Файл | Статус | Комментарий |
|------|--------|-------------|
| `BackButton.vue` | ✅ Работает | Smart navigation, Telegram BackButton integration |
| `AppTitle.vue` | ⚠️ Проверить | Не смотрел, возможно не нужен |
| `LoadingSpinner.vue` | ❌ Нет | Нужен для foundation |
| `EmptyState.vue` | ❌ Нет | Нужен для foundation |
| `ErrorMessage.vue` | ❌ Нет | Нужен для foundation |
| `ServiceCard.vue` | ❌ Нет | Есть в index.vue, нужно вынести |

### 1.3 Types — `app/types/`

| Файл | Статус | Комментарий |
|------|--------|-------------|
| `api.ts` | ⚠️ Нужна чистка | Есть User, но还有很多 TaxiOrder, Tal*, Uus*, Agal* — это для конкретных сервисов, не для foundation |
| `telegram.d.ts` | ✅ Работает | Telegram SDK types |

**Проблемы api.ts:**
- Содержит типы для всех 4 сервисов (не separation of concerns)
- TaxiOrder — это старый AYAN тип, не актуальный
- Нужно оставить только базовые типы, остальное в service layers

### 1.4 Pages — `app/pages/`

| Файл | Статус | Комментарий |
|------|--------|-------------|
| `index.vue` | ✅ Работает | Hub page с 4 сервисами, ServiceCard inline |
| `ui.vue` | ✅ Работает | UI demo page |

**Проблемы index.vue:**
- ServiceCard встроен в код, нужно вынести в компонент

### 1.5 Layouts — `app/layouts/`

| Файл | Статус | Комментарий |
|------|--------|-------------|
| `default.vue` | ⚠️ Работает | Header с кнопкой назад, базовый функционал |

**Проблемы:**
- Простой header (NuxtLink), не использует BackButton component
- Может дублироваться с BackButton на вложенных страницах

### 1.6 Config — `app/config/`

| Файл | Статус | Комментарий |
|------|--------|-------------|
| `api.config.ts` | ✅ Работает | USE_MOCK_API toggle |

---

## 2. SERVICES

### 2.1 AYAN — `services/ayan/`

| Файл | Статус | Действие |
|------|--------|----------|
| `README.md` | ✅ Документация | Оставить |
| `app/pages/ayan.vue` | ⚠️ Есть | Обёртка с NuxtPage, ОК |
| Остальное | ❌ Баговый код | **УДАЛИТЬ ВСЁ** |

**Код который нужно удалить:**
- services/ayan/app/composables/ (старые useAyanAPI)
- services/ayan/app/pages/ayan/ (старые страницы с багами)
- services/ayan/app/components/ (если есть)
- services/ayan/app/types/ (если есть)

### 2.2 UUS — `services/uus/`

| Файл | Статус | Действие |
|------|--------|----------|
| `README.md` | ✅ | Оставить |
| `nuxt.config.ts` | ✅ | Оставить |
| `app/pages/uus.vue` | ✅ | Обёртка, ОК |
| `package.json` | ✅ | Оставить |

**Статус:** Чистый, почти пустой, ОК для development

### 2.3 TAL — `services/tal/`

| Файл | Статус | Действие |
|------|--------|----------|
| `README.md` | ✅ | Оставить |
| `nuxt.config.ts` | ✅ | Оставить |
| `app/pages/tal.vue` | ✅ | Обёртка, ОК |
| `app/pages/tal-showcase.vue` | ❓ | Проверить, возможно showcase |
| `app/composables/useTalAPI.ts` | ⚠️ | Пустые методы, нужно реализовать |
| `app/composables/useTalStore.ts` | ⚠️ | Проверить что там |
| `package.json` | ✅ | Оставить |

### 2.4 AGAL — `services/agal/`

| Файл | Статус | Действие |
|------|--------|----------|
| `README.md` | ✅ | Оставить |
| `nuxt.config.ts` | ✅ | Оставить |
| `app/pages/agal.vue` | ✅ | Обёртка, ОК |
| `package.json` | ✅ | Оставить |

**Статус:** Чистый, пустой, ОК

---

## 3. ROOT CONFIG

### 3.1 Nuxt Config

```typescript
// frontend/nuxt.config.ts
extends: ['./services/agal', './services/tal', './services/uus']
// НО НЕТ services/ayan — это баг, нужно добавить
```

**Проблема:** AYAN не в extends, но есть в nuxt.config

### 3.2 Package.json

```json
{
  // Deps OK
  @nuxt/ui: ^4.3.0
  @nuxtjs/i18n: ^10.2.1
  nuxt: ^4.2.2
  zod: ^4.3.6
  
  // DevDeps OK
  @nuxt/eslint: ^1.12.1
  eslint: ^9.39.2
  prettier: ^3.8.0
  typescript: ^5.9.3
  vue-tsc: ^3.2.1
}
```

**Всё ОК**, не требует изменений

---

## 4. PROBLEMS TO FIX

### Priority 1: Critical (блокируют development)

1. **AYAN не в extends** — нужно добавить в nuxt.config.ts
2. **Старый код AYAN** — удалить баговые файлы
3. **useAuth не работает** — нет auto-login, token не персистится

### Priority 2: Important (нужно для foundation)

4. **types/api.ts засорен** — убрать TaxiOrder, Tal*, Uus*, Agal*
5. **Нет базовых компонентов** — LoadingSpinner, EmptyState, ErrorMessage, ServiceCard
6. **useTaxiAPI.ts и useMockAPI.ts** — удалить или перенести в services/ayan

### Priority 3: Nice to have (улучшения)

7. **layout + BackButton дублирование** — уточнить архитектуру
8. **useTalStore.ts** — проверить что там
9. **tal-showcase.vue** — проверить что это

---

## 5. ACTION PLAN

### Step 1: Очистка (30 мин)

```
УДАЛИТЬ:
- app/composables/useTaxiAPI.ts
- app/composables/useMockAPI.ts
- services/ayan/app/* (кроме pages/ayan.vue)

ОЧИСТИТЬ:
- app/types/api.ts (оставить только базовые типы)
```

### Step 2: Исправить nuxt.config (5 мин)

```typescript
// frontend/nuxt.config.ts
extends: ['./services/ayan', './services/agal', './services/tal', './services/uus']
```

### Step 3: Починить useAuth (1 час)

```typescript
// app/composables/useAuth.ts — нужные фичи:
// 1. Auto-login on mount
// 2. Token persistence (localStorage + useState)
// 3. Error handling (показывать пользователю)
```

### Step 4: Создать компоненты (2-3 часа)

```
СОЗДАТЬ:
- app/components/AppHeader.vue
- app/components/ServiceCard.vue
- app/components/LoadingSpinner.vue
- app/components/EmptyState.vue
- app/components/ErrorMessage.vue
```

### Step 5: Создать useUtils (1 час)

```
СОЗДАТЬ:
- app/composables/useUtils.ts
```

---

## 6. CURRENT ARCHITECTURE (AS-IS)

```
┌─────────────────────────────────────────┐
│           Telegram Mini App              │
│  ┌─────────────────────────────────┐    │
│  │         Nuxt 4 Frontend          │    │
│  │  ┌───────────────────────────┐  │    │
│  │  │   Base App (app/)         │  │    │
│  │  │   - useTg, useAuth        │  │    │
│  │  │   - useAPI, useUtils      │  │    │
│  │  │   - BackButton, AppHeader │  │    │
│  │  └───────────────────────────┘  │    │
│  │  ┌───────────────────────────┐  │    │
│  │  │  Service Layers           │  │    │
│  │  │  ayan │ uus │ tal │ agal  │  │    │
│  │  │  (Nuxt layers)            │  │    │
│  │  └───────────────────────────┘  │    │
│  └─────────────────────────────────┘    │
└─────────────────────────────────────────┘
```

**Проблема:** AYAN not in extends list

---

## 7. FILES TO DELETE

```bash
# OLD/BAUG CODE - DELETE ALL
app/composables/useTaxiAPI.ts
app/composables/useMockAPI.ts

# AYAN OLD PAGES - DELETE ALL except pages/ayan.vue
services/ayan/app/composables/*    # все composables
services/ayan/app/pages/ayan/*     # все страницы
services/ayan/app/components/*     # все компоненты
services/ayan/app/types/*          # все типы

# Check what's in these dirs before deleting
```

---

**Итог: ~6-8 часов работы для Foundation Phase**