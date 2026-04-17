# iind.app — Telegram Mini App

Nuxt 4 приложение с архитектурой services (layers) для Telegram Mini Apps.

## Структура

```
tma-app/
├── app/                        # Главное приложение
│   ├── pages/
│   │   ├── index.vue           # Хаб — выбор сервиса
│   │   └── ui.vue              # Витрина UI компонентов
│   ├── layouts/
│   │   └── default.vue         # TMA layout с навигацией
│   ├── composables/
│   │   ├── useTg.ts            # Telegram WebApp SDK обёртка
│   │   ├── useAPI.ts           # HTTP клиент с initData
│   │   └── useAuth.ts          # Авторизация через Telegram
│   ├── types/
│   │   ├── api.ts              # Типы всех API моделей
│   │   └── telegram.d.ts       # Telegram SDK типы
│   └── assets/css/
│       └── main.css            # Дизайн-система
├── services/                   # Nuxt layers — сервисы
│   ├── ayan/                   # Бардыбыт (Такси)
│   ├── uus/                    # Мастера
│   ├── agal/                   # Доставка
│   └── tal/                    # Бронирование
└── nuxt.config.ts
```

## Установка

```bash
npm install
```

## Запуск

```bash
npm run dev
```

## Дизайн-система

- **Primary**: cyan (#5edac6)
- **Background**: #0a0c0e
- **Surface**: #0f1113
- **Font**: Geist, Inter
- **Mode**: Dark only

## Composables

### `useTg()`
Обёртка Telegram WebApp SDK — user, initData, themeParams, hapticFeedback, кнопки.

### `useAPI()`
HTTP клиент с автоматической передачей Telegram initData и Bearer token.

### `useAuth()`
Авторизация через `POST /api/auth/telegram`. Управление ролями (passenger/driver).
