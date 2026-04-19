# System Design — iindiinda-app

## Vision

> iindiinda — делаем сложные вещи просто. Modern solutions. Simply.

**iind.app** — платформа, которая соединяет людей между собой. Простые решения для повседневных задач.

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

| Код | English | Описание | Статус |
|-----|---------|----------|--------|
| AYAN | Rides | Попутки между городами и улусами | MVP |
| UUS | Masters | Поиск мастеров — ремонт, услуги | Активен |
| AGAL | Delivery | Отправка посылок через попутчиков | Активен |
| TAL | Booking | Запись к мастерам — салоны, клиники | Активен |

## Auth Flow
Telegram WebApp SDK → initData validation → JWT token (Sanctum)
See: [[wiki/architecture/iind-app-vision]]

## Implementation Docs
*Created after Vision Phase completion*
- API endpoints, contracts → after vision finalized
- Data models → after vision finalized
- Auth implementation → after vision finalized
