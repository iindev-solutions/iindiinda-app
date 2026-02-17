# Telegram Mini App - Multi-Service Platform

Nuxt 4 приложение с архитектурой layers для Telegram Mini Apps.

## Структура проекта

```
tma-app/
├── app/                    # Главное приложение
│   ├── pages/
│   │   └── index.vue      # Выбор сервиса
│   ├── layouts/
│   │   └── default.vue    # TMA layout
│   └── assets/
│       └── css/
│           └── main.css
├── layers/
│   ├── core/              # Базовый слой (UI-kit, composables)
│   ├── taxi/              # Сервис такси
│   └── masters/           # Сервис мастеров
└── nuxt.config.ts
```

## Установка

```bash
cd tma-app
npm install
```

## Запуск

```bash
# Запуск всего приложения
npm run dev

# Запуск отдельных сервисов
npm run dev:taxi
npm run dev:masters
npm run dev:core
```

## Layers

### Core Layer
Базовый слой с общими компонентами и утилитами:
- Компоненты: TmaButton, TmaCard, TmaHeader
- Composables: useTg(), useBackButton(), useMainButton(), useTheme()
- Stores: user store (Pinia)
- Utils: API client

### Taxi Layer
Сервис заказа такси:
- `/taxi` - главная страница
- `/taxi/booking` - заказ поездки
- `/taxi/history` - история поездок

### Masters Layer
Сервис поиска мастеров:
- `/masters` - главная страница
- `/masters/catalog` - каталог мастеров

## Telegram Mini App

Приложение использует Telegram WebApp API:
- Theme params для адаптации под тему Telegram
- Safe area insets для iPhone
- BackButton и MainButton
- InitData для авторизации
