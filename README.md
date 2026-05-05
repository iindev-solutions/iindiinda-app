<img src="sakha-flag.svg" width="48" alt="Sakha flag" />

# iind.app

> iindiinda — make hard things simple.

**iind.app** is a Telegram Mini App platform for everyday coordination in Sakha / regional communities.

Core pattern across every service:

**request → response → agreement**

People already solve these problems in chats. iind.app makes the same flows clearer, faster, and easier to trust.

---

## Current status

- **Live MVP runtime** on a real VPS
- **4 working service tracks**: AYAN, UUS, TAL, AGAL
- **User-validated in Telegram Mini App**
- **Same-origin Nuxt + Laravel deployment** is stable again
- **Current priority**: launch-readiness, legal/compliance closure, and early traction

Live domain: `https://iindiinda.duckdns.org`  
Bot: `https://t.me/iindapp_bot`

---

## Services

| Service | What it does now | Main user value |
|---|---|---|
| **AYAN** | Rides, ride requests, responses, contact reveal, final status flow | Find rides and coordinate directly |
| **UUS** | Task feed, task creation, limited responses, executor selection | Solve small everyday jobs through Telegram |
| **TAL** | Master availability cards, booking requests, accept/reject, contact reveal | Book a free master without long chat chaos |
| **AGAL** | Carrier routes, sender requests, responses, contact reveal, final status flow | Send parcels through people already traveling |

---

## Who this is for

- people already coordinating rides in chat groups
- households looking for fast local help
- masters who need lightweight availability, not full CRM overhead
- travelers and senders who already pass parcels through personal networks
- small regional communities where trust and direct communication matter more than heavyweight platforms

---

## Product principle

iind.app does **not** try to replace people with marketplace machinery.

It keeps the platform layer intentionally thin:

1. create a request / route / availability card
2. get responses
3. choose a person
4. reveal contact
5. agree directly

That principle stays the same across all services.

---

## What works now

### AYAN
- create trip or passenger request
- browse feed with filters
- respond to trip/request
- accept response and reveal Telegram contact
- mark result as completed or cancelled

### UUS
- create household/work task
- browse open tasks with filters
- respond to tasks
- choose performer and reveal contact
- track task/result state

### TAL
- switch into master role
- publish availability card
- browse masters by category / location / availability
- send booking request
- accept/reject booking and reveal contact after acceptance

### AGAL
- publish route or sender request
- browse matching directions with filters
- respond to route/request
- accept response and reveal contact
- mark handoff result

---

## What comes next

The project is now past basic MVP bring-up.

Next decisions are product and launch decisions, not foundation work:

- launch-readiness and legal/compliance closure
- public-facing roadmap visibility inside the app
- early-user feedback loops
- one deliberately chosen post-MVP feature slice instead of broad speculative expansion

---

## Repo structure

```text
iindiinda-app/
├── backend/                     # Laravel API
├── frontend/                    # Nuxt 4 Telegram Mini App
│   ├── app/                     # shared app shell, pages, composables, components
│   └── services/                # service layers: ayan, uus, tal, agal
├── ops/                         # deployment and infrastructure notes
├── vault/                       # canonical project memory / docs / handoff state
├── docker-compose.coolify.yml   # paused alternative deployment track
└── README.md
```

---

## Stack

- **Frontend:** Nuxt 4, Vue 3, TypeScript, @nuxt/ui v4, Tailwind CSS
- **Backend:** Laravel, PHP, MySQL
- **Platform:** Telegram Mini App
- **Design:** dark-only, cyan primary, Geist / Inter
- **i18n:** Russian + Sakha

---

## Local development

### Frontend

```bash
cd frontend
npm install
npm run dev
```

### Frontend checks

```bash
cd frontend
npm run typecheck
npm run lint
npm run build:static
```

### Backend

See:
- `backend/README.md`
- `vault/wiki/services/ayan/backend-bringup.md`

---

## Deployment reality

Current live baseline is intentionally simple:

- `nginx + php-fpm + mysql`
- Laravel backend in `/var/www/iind-app/backend`
- Nuxt static frontend in `/var/www/iind-app/frontend/public`
- same-origin `/api`

**Coolify exists in source only as a paused alternative track.** Manual VPS deployment is the current real baseline.

---

## Documentation / source of truth

If you work on this repo, start with the vault.

Read first:
1. `vault/master_index.md`
2. `vault/WORKFLOW.md`
3. `vault/sprint.md`
4. `vault/resume-plan.md`

Useful docs:
- `vault/CODE_MAP.md`
- `vault/wiki/architecture/iind-app-vision.md`
- `vault/wiki/architecture/system-design.md`
- `vault/wiki/services/ayan/api-contract.md`
- `vault/wiki/services/uus/api-contract.md`
- `vault/wiki/services/tal/api-contract.md`
- `vault/wiki/services/agal/api-contract.md`

---

## Why this project matters

This is not “yet another super app” pitch.

It is a focused coordination layer for problems people already solve manually every day:
- rides
- small jobs
- booking a free master
- sending parcels through existing travel routes

The bet is simple:

**if direct coordination already exists, better flow beats more complexity.**
