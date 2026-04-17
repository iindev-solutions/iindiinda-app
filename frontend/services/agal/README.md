# iind.agal — Аҕал (Доставка)

**Версия**: v0.1  
**Статус**: Концепция

## Концепция

Авиа-доставка посылок по всему миру. Отправитель создаёт посылку, попутчик (кто летит в нужном направлении) берёт на доставку.

## API

| Метод | Эндпоинт | Описание |
|-------|----------|----------|
| POST | `/api/agal/parcels` | Создать посылку |
| GET | `/api/agal/parcels/open` | Ищут перевозчика |
| POST | `/api/agal/parcels/{id}/take` | Взять на доставку |

## Модели

### Parcel
`{ from_city, to_city, weight_kg, description, status }`

### Статусы
`looking_for_carrier → in_transit → delivered`
