# AYAN

> Сервис попутных поездок. Люди, которые едут → находят тех, кому по пути.

## Концепция

AYAN — **доска попуток**, не такси.

- Водитель создаёт поездку (откуда, куда, когда, места, цена)
- Пассажир создаёт запрос (ищу попутку)
- Они откликаются и связываются напрямую
- Без контроля, без статусов, без трекинга

## Структура (Nuxt Layer)

```
frontend/services/ayan/
├── app/
│   ├── pages/
│   │   ├── ayan.vue              → /ayan (parent wrapper)
│   │   └── ayan/
│   │       ├── index.vue         → /ayan (hub: rides list)
│   │       ├── create.vue        → /ayan/create
│   │       ├── request.vue       → /ayan/request
│   │       ├── ride/:id.vue      → /ayan/ride/:id
│   │       └── my-rides.vue     → /ayan/my-rides
│   ├── components/
│   │   ├── RideCard.vue
│   │   ├── RequestCard.vue
│   │   └── ContactModal.vue
│   ├── composables/
│   │   └── useAyanAPI.ts
│   └── i18n/
│       └── ... (keys)
├── nuxt.config.ts
└── README.md
```

## Дизайн

Полный Vision + Concept + MVP → `vault/wiki/architecture/ayan-rewrite-design.md`

## MVP Features

1. Создать поездку
2. Создать запрос
3. Список поездок/запросов
4. Откликнуться → контакт

## Status

**В разработке.** Чистый старт на `front/ayan`.

## Tech

- Nuxt 4 layer
- useAPI() напрямую (без ITaxiAPI)
- useIntervalFn для редкого обновления списка
- i18n обязательно
