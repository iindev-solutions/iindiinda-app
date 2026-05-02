# Vault Workflow

## Purpose

This file defines the required workflow for using `vault/` as the project's memory system.

`vault/` is mandatory project infrastructure.

## Session Start Protocol

At the start of every session, read these files in order:

1. `vault/master_index.md`
2. `vault/WORKFLOW.md`
3. `vault/sprint.md`
4. `vault/resume-plan.md`

Then read any task-specific files that apply, for example:

- `vault/CODE_MAP.md`
- `vault/wiki/services/ayan/api-contract.md`
- `vault/wiki/services/ayan/backend-bringup.md`
- relevant architecture pages under `vault/wiki/architecture/`

## Update Protocol

Before closing any meaningful task, update `vault/`.

Mandatory:

1. `vault/logs/changelog.md`

Update when relevant:

1. `vault/resume-plan.md` when the stop point, blocker, or next step changed
2. `vault/sprint.md` when sprint status or priorities changed
3. `vault/CODE_MAP.md` when code structure or file responsibilities changed
4. `vault/SESSION_LEDGER.md` with a short session note

## Session Ledger Format

Append one short block per working session:

```md
## YYYY-MM-DD HH:MM — Session Title

- Scope:
- Changes:
- Verified:
- Blockers:
- Next:
```

## Language Rule

All new content written inside `vault/` must be in English.

This includes:

- changelog entries
- handoff notes
- sprint notes
- resume notes
- workflow notes
- ad hoc session notes

## Definition Of Done For Knowledge Capture

A task is not fully closed until:

1. code and docs are updated
2. verification is recorded
3. the relevant `vault/` files are updated
4. the next session can resume without needing chat history

## Minimum Closure Checklist

- `vault/logs/changelog.md` updated
- `vault/resume-plan.md` updated if the stop point changed
- `vault/sprint.md` updated if sprint status changed
- `vault/CODE_MAP.md` updated if file ownership or structure changed
- `vault/SESSION_LEDGER.md` appended

## Rule For Future Sessions

Do not rely on chat memory when `vault/` can hold the state.

If important information exists only in chat, move it into `vault/`.
