# iind.uus — Мастера

**Версия**: v0.1  
**Статус**: Концепция

## Концепция

Поиск мастеров на любую задачу — муж на час, ремонт, монтаж, установка. Пользователь создаёт задачу, мастера откликаются.

## API

| Метод | Эндпоинт | Описание |
|-------|----------|----------|
| POST | `/api/uus/tasks` | Создать задачу |
| GET | `/api/uus/tasks/open` | Открытые задачи |
| POST | `/api/uus/tasks/{id}/respond` | Откликнуться |

## Модели

### Task
`{ title, description, category, budget, status }`

### Response
`{ task_id, master_id, message, price }`
