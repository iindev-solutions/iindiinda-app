<img src="sakha-flag.svg" width="48" alt="Флаг Саха" />  iind.app

> iindiinda — делаем сложные вещи просто. Modern solutions. Simply.
> **made for .**

iind.app — платформа из 4 сервисов, где саха люди решают повседневные задачи через других людей.

**Принцип:** заявка → отклик → договор. Без посредников. Бесплатно для заказчиков.

---

## Сервисы

| Сервис | Yakut | Что решает | Описание |
|--------|-------|------------|----------|
| **AYAN** | Бардыбыт | Передвижение | Попутки между городами и улусами |
| **UUS** | Уус | Бытовые задачи | Поиск исполнителей под конкретную работу |
| **TAL** | Тал | Запись к мастеру | Быстрая запись к свободным мастерам |
| **AGAL** | Аҕал | Отправка посылок | Доставка через людей, которые уже едут |

**Один паттерн для всех:** создать заявку → получить отклики → договориться напрямую.

---

## Структура

```
iindiinda-app/
├── backend/                    # Laravel API (scaffold-only)
├── frontend/                   # Nuxt 4 Telegram Mini App
│   ├── app/                    # Базовое приложение
│   │   ├── composables/        # useTg, useAPI, useAuth, useBackButton
│   │   ├── components/         # Общие компоненты
│   │   ├── pages/              # index.vue (хаб), ui.vue
│   │   └── assets/css/         # Дизайн-система (cyan theme)
│   └── services/               # Сервисные слои (Nuxt layers)
│       ├── ayan/               # AYAN — попутки
│       ├── uus/                # UUS — услуги
│       ├── agal/               # AGAL — доставка
│       └── tal/                # TAL — запись
├── vault/                      # Документация и vision
│   └── wiki/architecture/      # Vision документы
│       ├── iind-app-vision.md  # Главный vision платформы
│       ├── ayan-vision.md      # AYAN: попутки
│       ├── uus-vision.md       # UUS: услуги
│       ├── tal-vision.md       # TAL: запись
│       └── agal-vision.md      # AGAL: доставка
├── docker-compose.yml          # Local dev compose (legacy/simple)
├── docker-compose.coolify.yml  # Coolify deployment stack
└── .env.example
```

---

## Vision документы

Прежде чем смотреть код — читай vision:

- 📄 **[iind-app-vision.md](vault/wiki/architecture/iind-app-vision.md)** — главный vision платформы
- 🚗 **[ayan-vision.md](vault/wiki/architecture/ayan-vision.md)** — попутки (Бардыбыт)
- 🔧 **[uus-vision.md](vault/wiki/architecture/uus-vision.md)** — услуги (Уус)
- 📅 **[tal-vision.md](vault/wiki/architecture/tal-vision.md)** — запись (Тал)
- 📦 **[agal-vision.md](vault/wiki/architecture/agal-vision.md)** — доставка (Аҕал)

**Принцип работы:** сначала vision → потом implementation (код).

---

## Быстрый старт

```bash
# Клонируем
git clone https://github.com/iindev-solutions/iindiinda-app.git
cd iindiinda-app

# Переменные окружения
cp .env.example .env

# Frontend (Nuxt 4)
cd frontend
npm install
npm run dev
# → http://localhost:3000

# Backend (Docker, scaffold-only)
docker-compose up -d
# → http://localhost:8000
```

### Coolify start

Starter Coolify files now live in:

- `docker-compose.coolify.yml`
- `frontend/Dockerfile.coolify`
- `backend/Dockerfile.coolify`
- `ops/coolify/README.md`

Use `.env.coolify.example` as the env baseline and expose only the `frontend` service publicly.

---

## Технологии

- **Frontend**: Nuxt 4, @nuxt/ui v4, Tailwind CSS, TypeScript
- **Backend**: Laravel, PHP 8.2, MySQL 8.0 (scaffold-only)
- **Platform**: Telegram Mini App (WebApp SDK)
- **Design**: Cyan theme (#5edac6), dark mode only, Geist font
- **i18n**: Русский + саха (Yakut)

---

## Архитектурные решения

Из [iind-app-vision.md](vault/wiki/architecture/iind-app-vision.md):

1. **Простота или смерть** — понятно за 3 секунды
2. **Поток прежде монетизации** — 100 заявок/день, потом деньги
3. **Люди договариваются сами** — платформа только соединяет
4. **Один паттерн для всех** — заявка/доступность/отклик
5. **Своё > чужое** — не копируем Яндекс, делаем как у нас

---

## Ссылки

- Telegram: [@iindapp_bot](https://t.me/iindapp_bot)
- GitHub: [iindev-solutions](https://github.com/iindev-solutions)

---

> **iindiinda** — делаем сложные вещи просто. Modern solutions. Simply.
