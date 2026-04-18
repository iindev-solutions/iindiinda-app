# System Design — iindiinda-app

## Vision

> iindiinda — делаем сложные вещи просто. Modern solutions. Simply.

**iind.app** — это слой, который соединяет людей между собой. Не "сервис такси", не "доставка", не "маркетплейс".

Один принцип для всех сервисов:

> человек оставляет заявку → другой человек откликается → они сами договариваются

Без посредников. Без сложных алгоритмов. Просто люди находят друг друга.

## Overview
Telegram Mini App платформа с 4 сервисами. MVP — ayan (попутки). Остальные 3 — развиваются параллельно.

Фокус: регионы, где нет удобных локальных сервисов. Делаем быстро, просто, понятно.

## Stack
| Слой | Технология | Примечание |
|------|-----------|------------|
| Frontend | Nuxt 4 + Vue 3 + TypeScript | SPA-only, ssr: false |
| UI | @nuxt/ui v4 | Cyan тема, dark only |
| i18n | @nuxtjs/i18n | ru (default) + sah |
| Backend | Laravel + MySQL | RESTful API |
| Auth | Telegram WebApp SDK → Sanctum | initData → JWT |
| State | useState + composables | No Pinia (Nuxt built-in) |

## Architecture Diagram

```
┌─────────────────────────────────────────────────┐
│                  Telegram Mini App               │
│  ┌──────────────────────────────────────────┐   │
│  │            Nuxt 4 Frontend                │   │
│  │  ┌─────────┐  ┌──────────────────────┐   │   │
│  │  │ Base App │  │   Service Layers     │   │   │
│  │  │ (app/)   │  │ ayan │ uus │ agal │ tal│   │   │
│  │  │ composables  │  Each = Nuxt layer   │   │   │
│  │  │ types     │  │ Pages + components   │   │   │
│  │  │ layouts   │  └──────────────────────┘   │   │
│  │  └─────────┘                                │   │
│  │           │                                  │   │
│  │     ┌─────┴──────┐                          │   │
│  │     │  ITaxiAPI   │  ← interface            │   │
│  │     │  ┌────────┐ │                          │   │
│  │     │  │Real API│ │  → Laravel backend      │   │
│  │     │  │Mock API│ │  → localStorage         │   │
│  │     │  └────────┘ │                          │   │
│  │     └──────────────┘                          │   │
│  └──────────────────────────────────────────┘   │
└─────────────────────────────────────────────────┘
         │
         ▼
┌─────────────────────┐
│   Laravel Backend    │
│  api.php → controllers
│  Sanctum auth        │
│  MySQL database      │
└─────────────────────┘
```

## Service Layers
Каждый сервис — Nuxt layer, extends базовое приложение. Все разделяют одну идею: заявка + отклик.

| Код | Yakut | English | Описание | Статус |
|-----|-------|---------|----------|--------|
| ayan | Бардыбыт | Rides | Попутки между городами и улусами | MVP |
| uus | Уус | Masters | Поиск мастеров — ремонт, услуги | Активен |
| agal | Аҕал | Delivery | Отправка посылок через попутчиков | Активен |
| tal | Тал | Booking | Запись к мастерам — салоны, клиники | Активен |

## Auth Flow
See: [[wiki/architecture/auth-flow]]

## API Contract
See: [[wiki/architecture/api-contract]]

## Data Models
See: [[wiki/architecture/data-models]]
