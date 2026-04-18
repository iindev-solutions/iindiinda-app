# System Design — iindiinda-app

## Overview
Telegram Mini App платформа с 4 сервисами. MVP — ayan (такси). Остальные 3 — заглушки.

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
Каждый сервис — Nuxt layer, extends базовое приложение.

| Код | Yakut | English | Описание | Статус |
|-----|-------|---------|----------|--------|
| ayan | Бардыбыт | Taxi | Пассажир назначает цену | MVP |
| uus | Уус | Masters | Ремонт, мастер на час | Заглушка |
| agal | Аҕал | Delivery | Доставка посылок авиа | Заглушка |
| tal | Тал | Booking | Бронирование салонов | Заглушка |

## Auth Flow
See: [[wiki/architecture/auth-flow]]

## API Contract
See: [[wiki/architecture/api-contract]]

## Data Models
See: [[wiki/architecture/data-models]]
