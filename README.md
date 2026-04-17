# iind

> iindiinda — делаем сложные вещи просто. Modern solutions. Simply.

Монорепозиторий экосистемы городских сервисов **iind.app** — Telegram Mini App платформа.

## Сервисы

| Сервис | Код | Описание |
|--------|-----|----------|
| **Бардыбыт** (ayan) | `services/ayan` | Такси — пассажир ставит цену, водитель принимает |
| **Уус** (uus) | `services/uus` | Поиск мастеров — ремонт, монтаж, муж на час |
| **Аҕал** (agal) | `services/agal` | Авиа-доставка посылок по миру |
| **Тал** (tal) | `services/tal` | Онлайн-бронирование — салоны, клиники, мастерские |

## Структура

```
iindiinda-app/
├── backend/                    # Laravel API
│   └── routes/api.php          # API контракты всех сервисов
├── frontend/tma-app/           # Nuxt 4 TMA
│   ├── app/                    # Главное приложение
│   │   ├── composables/        # useTg, useAPI, useAuth
│   │   ├── pages/              # index.vue (хаб), ui.vue
│   │   ├── types/              # api.ts, telegram.d.ts
│   │   └── assets/css/         # Дизайн-система (cyan theme)
│   └── services/               # Nuxt layers — сервисы
│       ├── ayan/               # Бардыбыт (Такси MVP)
│       ├── uus/                # Мастера
│       ├── agal/               # Доставка
│       └── tal/                # Бронирование
├── docker-compose.yml          # PHP 8.2 + MySQL 8.0
└── .env.example
```

## Быстрый старт

```bash
# Клонируем
git clone https://github.com/iindev-solutions/iindiinda-app.git
cd iindiinda-app

# Переменные окружения
cp .env.example .env

# Frontend
cd frontend/tma-app
npm install
npm run dev

# Backend (Docker)
docker-compose up -d
```

## Технологии

- **Frontend**: Nuxt 4, @nuxt/ui v4, Tailwind CSS, TypeScript
- **Backend**: Laravel, PHP 8.2, MySQL 8.0
- **Platform**: Telegram Mini App (WebApp SDK)
- **Design**: Custom cyan theme, dark mode, Geist font

## Ссылки

- Telegram Bot: [@iindapp_bot](https://t.me/iindapp_bot)
- GitHub: [iindev-solutions](https://github.com/iindev-solutions)
