# Browser Back Button Component — Design Doc

> Based on: [[wiki/services/nuxt-app/routing]], [[vault/master_index]]

## Overview

Универсальный компонент кнопки "Назад" для iindiinda-app с поддержкой:
1. **Браузерной навигации** — умный fallback, хуки, проверка секций
2. **Telegram BackButton** — автоматическая интеграция с TMA SDK
3. **@nuxt/ui v4** — стилизация под дизайн-систему проекта

## Use Cases

| Сценарий | Поведение |
|----------|-----------|
| Пользователь внутри раздела `/ayan/*` | `router.back()` — возврат по истории |
| Пользователь открыл страницу напрямую (нет истории) | `navigateTo(fallbackRoute)` |
| Telegram Mini App | Показываем нативный BackButton, скрываем UI-кнопку |
| Браузер (не TMA) | Показываем UI-кнопку из @nuxt/ui |
| beforeNavigate вернул false | Навигация отменена (например, показать confirm) |

## Component Interface

```typescript
interface BackButtonProps {
	/**
	 * Резервный маршрут: куда идти, если в истории некуда возвращаться
	 * (например, пользователь открыл страницу напрямую)
	 */
	fallbackRoute?: string // путь, например '/ayan'

	/**
	 * Хук перед навигацией.
	 * Может вернуть Promise<boolean> или просто boolean.
	 * Если false — навигация отменится (удобно для подтверждений)
	 */
	beforeNavigate?: () => boolean | Promise<boolean>

	/**
	 * Хук для переопределения логики перехода.
	 * Если передан - стандартная логика не сработает.
	 */
	onNavigate?: () => void | Promise<void>

	/**
	 * Текст кнопки (только для UI-режима, в TMA не используется)
	 * @default 'Назад'
	 */
	label?: string

	/**
	 * Показывать ли UI-кнопку даже в Telegram (для отладки)
	 * @default false
	 */
	forceUi?: boolean

	/**
	 * Дополнительные классы для UI-кнопки
	 */
	class?: string
}
```

## Smart Navigation Logic

```
┌─────────────────────────────────────────────────────────┐
│  BackButton нажата / Telegram BackButton clicked        │
└────────────────────┬──────────────────────────────────────┘
                     │
                     ▼
        ┌────────────────────────┐
        │ onNavigate передан?    │
        └───────────┬────────────┘
                    │
        Да ─────────┼─────────► Выполнить onNavigate()
                    │                    (кастомная логика)
                    │
                    ▼ Нет
        ┌────────────────────────┐
        │ beforeNavigate?          │
        └───────────┬────────────┘
                    │
        Да ─────────┼─────────► await beforeNavigate()
                    │                    │
                    │                    ▼
                    │         ┌────────────────────┐
                    │         │ Результат true?    │
                    │         └───────┬────────────┘
                    │                 │
                    │      Нет ───────┼───────► Отменить навигацию
                    │                 │
                    │                 ▼ Да (или нет хука)
                    ▼
        ┌────────────────────────┐
        │ isSameSection?         │
        └───────────┬────────────┘
                    │
        Да ─────────┼─────────► router.back()
                    │
                    ▼ Нет
        ┌────────────────────────┐
        │ fallbackRoute?         │
        └───────────┬────────────┘
                    │
        Да ─────────┼─────────► navigateTo(fallbackRoute)
                    │
                    ▼ Нет
              navigateTo('/') // домой
```

## Same Section Detection

```typescript
const isSameSection = computed(() => {
	const backRoute = router.options.history.state?.back
	if (!backRoute || typeof backRoute !== 'string') return false

	const currentSection = route.path.split('/')[1] // 'ayan' из '/ayan/create'
	const backSection = backRoute.split('/')[1]

	return currentSection === backSection && currentSection !== ''
})
```

## Telegram Integration

```typescript
// В onMounted
if (isInTelegram.value && !props.forceUi) {
	tg.showBackButton()
	tg.onBackButtonClicked(handleBack)
}

// В onUnmounted
if (isInTelegram.value && !props.forceUi) {
	tg.hideBackButton()
}
```

## File Structure

```
frontend/app/components/
└── BackButton.vue          # Универсальный компонент

frontend/app/composables/
└── useSmartBack.ts         # (опционально) вынесенная логика
```

## Example Usage

```vue
<!-- Простое использование -->
<template>
	<BackButton fallback-route="/ayan" />
</template>

<!-- С подтверждением -->
<template>
	<BackButton 
		:fallback-route="/ayan"
		:before-navigate="confirmUnsavedChanges"
	/>
</template>

<script setup>
const confirmUnsavedChanges = async () => {
	if (hasUnsavedChanges.value) {
		return await showConfirmDialog('У вас есть несохранённые изменения')
	}
	return true
}
</script>

<!-- Кастомная навигация -->
<template>
	<BackButton 
		:on-navigate="customNavigate"
	/>
</template>
```

## Dependencies

- `useTg()` — уже существует в `app/composables/useTg.ts`
- `@nuxt/ui` — UButton для UI-режима
- `vue-router` — router.back(), navigateTo()

## Nuxt 4 Compliance

- Composables auto-imported — no manual imports needed
- Uses `navigateTo()` — NEVER `router.push()` per AGENTS.md
- Follows design system: cyan colors, dark theme
- Telegram SDK: checks `supportsVersion('6.1')` before using BackButton

## i18n Keys

```json
{
	"backButton": {
		"label": "Назад",
		"ariaLabel": "Вернуться назад"
	}
}
```

## Implementation Notes

1. **AGENTS.md Rule**: Всегда использовать `navigateTo()` вместо `router.push()`
2. **Telegram SDK**: Не модифицируем SDK, используем готовый composable `useTg()`
3. **No hardcoded strings**: Все тексты через `$t()`
4. **TypeScript**: strict mode, все пропсы типизированы

---

**Status**: Ready for implementation  
**Related**: [[wiki/services/nuxt-app/routing]], [[wiki/architecture/ayan-rewrite-design]]
