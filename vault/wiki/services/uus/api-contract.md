# UUS API Contract

> Status: first MVP contract lock
> Purpose: define the first real UUS implementation slice before backend/frontend work expands beyond the current placeholder state

## Product Role

UUS is a task-and-response board for local household and small work services.

UUS is **not**:

- an employer
- a contractor
- a staffing agency
- a guarantee of work quality
- a payment intermediary
- a controlled fulfillment workflow

UUS only helps two sides find each other:

1. a **customer** who needs something done
2. an **executor** who is willing to do the work

Contact is revealed only after an explicit accept action.

## MVP Scope

UUS MVP should keep the same platform pattern already proven by AYAN and AGAL:

1. customer creates a task
2. executors browse the feed with filters
3. executors respond with a short message
4. customer accepts one response
5. contact is revealed
6. customer later marks the outcome as `completed` or `cancelled`

UUS adds one service-specific rule from the vision docs:

- response cap per task
  - `3` responses for urgent tasks
  - `5` responses for normal tasks

## Base URL

```text
/api/uus
```

## Authentication

All UUS endpoints are protected.

- Login entrypoint: `POST /api/auth/telegram`
- Auth transport: `Authorization: Bearer {token}`
- Backend auth runtime should follow the same Sanctum path already used by AYAN and AGAL

## Core Entities

### Task

Customer-side task created by the person who needs work done.

```ts
Task {
  id: number
  customer: {
    id: number
    name: string
    username: string | null
  }
  category: 'home' | 'repair' | 'delivery' | 'other'
  description: string
  location: string
  desired_when: 'today' | 'tomorrow' | 'date' | 'flexible'
  date: string | null
  budget: number | null
  budget_type: 'fixed' | 'negotiable'
  urgency: 'urgent' | 'normal'
  response_limit: 3 | 5
  status: 'open' | 'matched' | 'completed' | 'cancelled'
  created_at: string
}
```

### Response

Executor-side response to a task.

```ts
Response {
  id: number
  task_id: number
  user: {
    id: number
    name: string
    username: string | null
  }
  message: string | null
  offered_price: number | null
  status: 'pending' | 'accepted' | 'rejected'
  created_at: string
}
```

## Lifecycle Rules

### Task statuses

- `open`
- `matched`
- `completed`
- `cancelled`

### Response statuses

- `pending`
- `accepted`
- `rejected`

### Behavior

- new task starts as `open`
- urgent task gets `response_limit = 3`
- normal task gets `response_limit = 5`
- once the response cap is reached, no new responses may be created for that task
- accepting a response moves task to `matched`
- owner can later mark task as `completed` or `cancelled`
- once a response is accepted, contact may be shown
- non-pending responses cannot be deleted

## Endpoint Surface

### Tasks

#### `GET /uus/tasks`

List open tasks.

**Filters:**

- `category` optional
- `location` optional
- `urgency` optional
- `desired_when` optional
- `date` optional

#### `POST /uus/tasks`

Create task.

**Body:**

```json
{
  "category": "home",
  "description": "Need window cleaning on Saturday",
  "location": "DSK district",
  "desired_when": "date",
  "date": "2026-05-10",
  "budget": 2000,
  "budget_type": "fixed",
  "urgency": "normal"
}
```

#### `GET /uus/tasks/{id}`

Get single task details.

#### `PATCH /uus/tasks/{id}`

Owner updates task status/details.

### Responses

#### `GET /uus/tasks/{id}/responses`

Owner-only list of responses for a task.

#### `POST /uus/tasks/{id}/responses`

Respond to a task.

**Body:**

```json
{
  "message": "Can do it tomorrow morning",
  "offered_price": 1800
}
```

#### `PATCH /uus/responses/{id}`

Owner accepts or rejects response.

#### `DELETE /uus/responses/{id}`

Responder deletes own pending response only.

### My Area

#### `GET /uus/my/tasks`
#### `GET /uus/my/responses`

Same intent as AYAN/AGAL `my/*` endpoints.

## Validation Baseline

### Task validation

- `category`: required enum
- `description`: required string, max `500`
- `location`: required string, max `255`
- `desired_when`: required enum
- `date`: nullable date, required when `desired_when = date`, must be today or future
- `budget`: nullable integer, `>= 0`
- `budget_type`: required enum
- `urgency`: required enum

### Response validation

- `message`: nullable string, max `500`
- `offered_price`: nullable integer, `>= 0`
- at least one of `message` or `offered_price` should be present

## Explicit Non-Goals For MVP

Do not implement yet:

- ratings and reviews
- executor portfolios
- document verification
- in-app payments
- safe-deal / escrow flow
- GPS tracking
- internal chat
- push notifications
- employer-style workflow or payroll logic

## Implementation Decision

UUS should reuse the now-proven AYAN/AGAL architecture wherever possible:

- same feed/detail/my-area structure
- same response acceptance flow
- same lifecycle statuses
- same contact-reveal moment
- same Telegram-first runtime assumptions

The main UUS-specific addition is the response cap tied to urgency.

## First Practical Slice

Before building the real persisted MVP, complete these in order:

1. lock this contract
2. align backend placeholder routes to the contract shape
3. align frontend page structure to the required Nuxt service pattern
4. add real backend persistence/controllers
5. add frontend feed/create/detail/respond flow
