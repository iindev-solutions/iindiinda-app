# RF Legal Audit — iind.app Platform and Service Layer

## Purpose

This audit captures the minimum legal posture the product should hold for operation in the Russian Federation, based on the current vision docs and the codebase state on `front/ayan`.

It is a product/compliance engineering audit, not a substitute for licensed legal advice.

## Scope Reviewed

- `vault/wiki/architecture/iind-app-vision.md`
- `vault/wiki/architecture/system-design.md`
- `vault/wiki/architecture/ayan-vision.md`
- `vault/wiki/architecture/uus-vision.md`
- `vault/wiki/architecture/tal-vision.md`
- `vault/wiki/architecture/agal-vision.md`
- current frontend routes and legal surfaces under `frontend/app/` and `frontend/services/`
- current AYAN backend data model and auth/runtime shape under `backend/`

## Operating Assumptions

Current product posture should remain:

1. connection platform, not service operator
2. direct user-to-user agreement after match
3. no in-app payment or escrow on MVP
4. no platform guarantee of trip, task completion, booking execution, or parcel delivery
5. direct contact disclosure only after a defined product event
6. minimal data collection via Telegram auth + user-generated listings/statuses

If product behavior moves beyond those assumptions, legal documents and backend controls must be revised again.

## Provisional Owner Decisions - 2026-04-25

These are not final disclosure details yet, but they are the current planning assumptions captured for future sessions:

- final operator model is planned as a natural person, not company-form wording for now
- production/personal-data hosting is planned to move to RF infrastructure later
- final public contact/requisite details will be added later and are still pending

Until those inputs are finalized, source texts should stay structurally ready but must not be treated as the final legal disclosure pack.

## Highest-Risk Findings

### 1. Personal-data localization is the top unresolved blocker

Current production API base and runtime history point to `89.22.226.34`, which geolocates to Sweden.

That creates a high-risk gap for RF personal-data localization expectations because the platform processes at least:

- Telegram ID
- first name
- username
- user-generated route/task/contact-related data
- technical logs tied to account activity

Until the team confirms compliant RF-based primary storage/recording/systematization for Russian users, legal closure is incomplete.

**Status:** not resolved by UI/legal copy alone.

### 2. Operator identity and formal requisites are still incomplete

The product can publish terms, privacy, consent, and support flow, but final production documents still need real operator details:

- legal entity / sole proprietor name
- formal support contact set
- mailing or service address where required
- final controller/operator wording for personal data

**Status:** partially mitigated by a support route, but still incomplete for final production-grade disclosure.

### 3. Several service visions create regulated-edge scenarios

The vision docs are intentionally simple, but they naturally touch regulated zones:

- AYAN -> passenger transport / taxi / dispatch risk
- UUS -> regulated or hazardous work categories
- TAL -> medical / health-data risk if clinics are included
- AGAL -> courier / freight-forwarding / dangerous-goods risk

The code and user-facing copy must keep the platform inside a narrow "match people, do not operate the underlying service" position.

**Status:** source copy has now been tightened toward that posture.

## Service-by-Service Risk Map

### AYAN

**Core posture:** ridesharing bulletin board for already planned trips.

**Main risks:**

- reclassification as taxi/dispatcher/carrier
- unsafe/off-platform arrangements
- unlawful passenger transport patterns
- dangerous goods passed through ride flows

**Required posture:**

- driver posts only a trip they already plan to take
- platform does not assign drivers, set route, or guarantee carriage
- platform is not a carrier or taxi service
- user is responsible for legal right to drive, route lawfulness, and safety checks

### UUS

**Core posture:** task board for direct customer-executor matching.

**Main risks:**

- platform seen as contractor/employer
- hazardous or regulated work posted without proper permits
- disputes over quality or damage
- overpromising verification of executor quality

**Required posture:**

- platform does not hire, assign, supervise, or guarantee executors
- regulated/high-risk categories may be limited or removed
- customer and executor verify qualifications and terms directly
- no false promise of ratings/verification on MVP

### TAL

**Core posture:** lightweight availability + booking request surface for masters.

**Main risks:**

- medical-services classification if clinics/health scenarios are included
- special-category personal data risk if health data is collected
- overpromising slot reservation or instant confirmation

**Required posture:**

- MVP limited to non-medical master scenarios
- no clinic/medical positioning in current copy
- platform does not maintain medical records
- platform does not guarantee booking slot reservation

### AGAL

**Core posture:** route-matching board for parcels carried by people already traveling.

**Main risks:**

- courier/logistics/freight-forwarding reclassification
- dangerous/prohibited contents
- international/customs handling creep
- false delivery guarantee impression

**Required posture:**

- platform matches route participants only
- no storage, carriage, insurance, or tracking guarantee
- RF-only baseline is safer than international messaging at MVP
- strict ban language for prohibited and dangerous items

## Platform-Wide Legal Pack Required

Minimum document set for current scope:

1. platform user agreement
2. privacy policy
3. personal-data consent
4. support and complaint route
5. AYAN rules
6. AYAN safety rules
7. UUS rules
8. TAL rules
9. AGAL rules

## UI Distribution Rule

Legal access should not live only inside AYAN.

Minimum UX rule:

- one common legal center on `/legal`
- visible entry on home screen
- persistent footer-level access from default layout
- service-specific legal links on service pages or access states

## Copy/Positioning Rules That Must Stay True

### Allowed positioning

- platform connects people
- people agree directly
- platform may moderate and reveal contact after match

### Disallowed positioning

- "we deliver"
- "we transport"
- "instant confirmed booking" unless product truly reserves the slot
- medical or clinic positioning in TAL MVP
- worldwide/international parcel promises in AGAL MVP
- rating/verification promises for UUS MVP when such systems do not exist

## Source Changes Implemented In This Session

- expanded legal center from AYAN-only slice toward platform-wide coverage in source
- added platform docs routes and service-specific rules routes in frontend
- added common legal footer surface in default layout
- aligned UUS/TAL/AGAL placeholder copy toward safer legal posture
- kept Russian-only document authoring in active source per current user preference

## Remaining Required Follow-Up

### Before production legal sign-off

1. decide RF-compliant personal-data hosting/localization strategy
2. fill final operator/controller details in published docs
3. confirm support intake process and ownership
4. manually review every legal text with RF counsel

### Strongly recommended product/backlog items

1. content/report action in UI for abuse and dangerous listings
2. explicit data-deletion/request handling runbook on backend side
3. documented moderation workflow for illegal goods/services reports
4. final monetization documents before any paid promotion/subscription/commission launch

## One-Line Conclusion

Platform legal posture is now structurally moving in the right direction, but RF personal-data localization and final operator disclosure remain the two blockers that copy alone cannot solve.
