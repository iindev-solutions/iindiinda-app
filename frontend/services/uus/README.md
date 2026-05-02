# iind.uus

UUS is the local task-and-response service of iind.app.

## Current Status

- current frontend source: first real MVP slice exists (`/uus` feed/my-area/create + `/uus/task/[id]` detail/respond)
- current backend source: first real persisted MVP slice exists (`tasks`, `responses`, `my/*`, response-cap rule)
- current deployment status: first real MVP slice is deployed live; latest UI polish pass is still local/source-only until the next frontend deploy

## Intended MVP

1. customer creates a task
2. executors browse open tasks with filters
3. executors respond with a short message
4. customer accepts one response
5. contact is revealed
6. customer marks result as `completed` or `cancelled`

## Service-Specific Rule

UUS keeps a response cap per task:

- urgent task -> max `3` responses
- normal task -> max `5` responses

## Contract

See:

- `vault/wiki/services/uus/api-contract.md`

## Planned Frontend Shape

- `/uus` -> feed + filters + create CTA
- `/uus/task/[id]` -> task detail + respond / accept flow
- shared my-area cards on the main UUS screen

## Planned Backend Shape

- `GET/POST /api/uus/tasks`
- `GET/PATCH /api/uus/tasks/{id}`
- `GET/POST /api/uus/tasks/{id}/responses`
- `PATCH/DELETE /api/uus/responses/{id}`
- `GET /api/uus/my/tasks`
- `GET /api/uus/my/responses`
