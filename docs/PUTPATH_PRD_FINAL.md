# PutPath — Final PRD (Detailed)

**Agentic Fleet Operating System & AI Operations Workforce Platform**
Built on Traccar · Rebuilt clean in Python · Domain knowledge mined from GPSWOX

> Status: final, consolidated. Supersedes earlier drafts. Reflects all stack decisions:
> Django backend, Flue AI runtime, LiteLLM router, hybrid knowledge (Surya+vector RAG + OKF/MD),
> TimesFM forecasting, TimescaleDB telemetry, ClawPatrol safety, GPSWOX-style customer model,
> Spec Kitty + opensrc dev workflow.

---

## 1. Overview

PutPath is an **Agentic Fleet Operating System** and **AI Operations Workforce Platform** for telematics, logistics, fleet, construction, and heavy-equipment companies. It is a **multi-customer SaaS** (GPSWOX-style account model), not a framework and not a single-customer deployment.

PutPath is **not**: a GPS dashboard, a reporting tool, an AI chatbot, a Traccar wrapper, or a generic project manager. It is the **AI operating layer** above live telematics data, documents, images, tables, forms, approvals, and operations.

**One-line positioning:**
> PutPath is the AI operating layer that turns fleet data, documents, alerts, and workflows into assigned work, investigations, reports, approvals, and operational decisions.

**Core thesis — the Operational Intelligence Loop.** Traditional systems stop at `alert → report → dashboard`. PutPath continues:

```
detect → investigate → explain → assign → approve → execute → follow up → learn
```

Everything is accountable, evidence-backed, tenant-isolated, and auditable.

---

## 2. Target Customers & Users

**Customers (companies):** logistics, heavy-equipment rental, construction fleets, delivery, transportation, facility management, oil & gas service fleets, municipal fleets, field service, and telematics service providers (TSPs / resellers) — including companies already on Wialon, Traccar, GPSWOX, or Teltonika.

**Users (roles):** Owner/Executive, Fleet Manager, Operations Manager, Dispatcher, Technical Support, Maintenance Manager, Fuel Analyst, Admin, Client Success/Reporting, Reseller (manager), External Client (sub-user).

---

## 3. Product Principles

1. **Map-first** home; chat is a core navigation layer; artifacts open only when needed.
2. **Action over visualization** — every insight answers: what / why / who owns it / next action / approval / follow-up.
3. **Backend is the source of truth** — Django owns customers, RBAC, plans, approvals, audit, workflow state.
4. **No prompt-only security** — enforced by RBAC, scoped tokens, tool allowlists, permission gateway, ClawPatrol, audit, human approvals.
5. **AI must explain serious decisions** — intent, tools, evidence, confidence, risk, approval status, next action, owner.
6. **Strict customer isolation** — every request carries customer + user + role + scope; zero cross-customer access.
7. **Mine GPSWOX for knowledge, not code** — rebuild clean in Python; keep Traccar as the device engine.

---

## 4. Final Technology Stack

| Layer | Final decision | Notes |
|---|---|---|
| Device engine | **Traccar** (clean fork, upstream-compatible) | 44+ protocols; consumed via API/WS/Forwarding behind `TraccarPort`. Never edit core. |
| Backend | **Python Django + DRF** | Source of truth: RBAC, plans, approvals, audit, business modules. |
| Code organization | Bounded Contexts as **Django apps** + **Hexagonal** on external edges + **service layer (MVCS)** + Clean dependency direction | Discipline applied Django-idiomatically. |
| Frontend | **React** | Map-first; chat nav; artifacts on demand. |
| AI runtime | **Flue** (TypeScript, isolated AI-layer service) | Sessions/tools/skills/sandbox/durability; called by Django via `AIAgentPort`. |
| AI router | **LiteLLM** | Unified proxy (GPT/Gemini/Claude), fallback, cost tracking, per-customer cost limits; language-neutral (HTTP). |
| Knowledge / RAG | **Hybrid:** Surya OCR + vector RAG (unstructured) + OKF/MD (curated) behind `KnowledgePort` | Vector DB swappable (pgvector/Qdrant/Milvus/Weaviate). |
| Forecasting | **TimesFM** (Google Research) | Time-series foundation model; worker reads TimescaleDB → maintenance/fuel/ETA/utilization forecasts. |
| Safety | Django RBAC + Permission Gateway + **ClawPatrol** + audit + human approvals | Runtime action firewall for risky agent actions. |
| Databases | **PostgreSQL** (business) + **TimescaleDB** (positions/telemetry) + **vector DB** (RAG) + **Redis** (cache/streams) | PostGIS for geofences. |
| Events | **Redis Streams** (start) → **Kafka** (at scale) | Event-driven agentic loop. |
| Analytics / routing | **Polars** (analytics) + **VROOM** (route optimization); later City2Graph, Map3D | |
| Release / DR | **GrowthBook** (feature flags) + **Databasus + WAL** backups | |
| Customer model | Single shared DB + row-level `owner_id` isolation + `manager_id` reseller tree + per-account plans | GPSWOX-style. |
| Dev workflow | **Spec Kitty** (spec-driven) + **opensrc** (mine GPSWOX) | Decisions locked in Spec Kitty constitution. |
| Scale | Stay Modular Django; extract services (FastAPI/Go for hot ingestion) only at real scale symptoms | |

**Architecture sentence:** *Traccar tracks · Django controls · Flue acts · LiteLLM routes · Surya+RAG+OKF understand · TimesFM forecasts · Polars analyzes · VROOM optimizes · ClawPatrol protects · React presents.*

---

## 5. System Architecture

```
Devices (GPS) ──TCP/UDP protocol ports──▶ Traccar (Java)  ──▶ Redis (live) + telemetry
                                                │
                                    Position Forwarding / WS / REST
                                                ▼
React (View) ─▶ Django (DRF) ─────────────────────────────────────────┐
  • accounts (customers, manager tree, plans, RBAC)                     │
  • tracking (Device/Position/Trip/Sensor + TraccarPort)               │
  • fleet · geo · alerting · reporting · knowledge · ai_agent          │
        │ Domain Events (Redis Streams)                                 │
        ├─▶ ai-layer (Flue, TS) ─▶ KnowledgePort (Surya+RAG / OKF/MD)  │
        │                       ─▶ ForecastPort (TimesFM)              │
        │                       ─▶ LLMPort (LiteLLM)                    │
        ├─▶ ClawPatrol (risky-action firewall) ─▶ human approval       │
        └─▶ audit log                                                   │
Databases: PostgreSQL (business) · TimescaleDB (positions) · vectorDB (RAG) · Redis
Workers: Polars (analytics) · VROOM (routing) · TimesFM (forecast)
```

**Folder structure (Django modular monolith):**

```
platform/
├── apps/
│   ├── accounts/        # Account(User), Client, manager tree, Plans, RBAC
│   ├── tracking/        # Device, Position, Trip, Sensor + TraccarPort
│   ├── fleet/           # Vehicle, Driver, Group
│   ├── geo/             # Geofence, Poi, Route (PostGIS)
│   ├── alerting/        # AlertRule, Event, Notification (rule engine)
│   ├── reporting/       # Report, ReportLog (CQRS read models, Polars)
│   ├── knowledge/       # SuryaAdapter, VectorAdapter, OKFAdapter
│   └── ai_agent/        # AIAgentPort, LLMPort, ForecastPort + UseCases
│       └── (each app: domain/ application/ services/ infrastructure/)
├── shared/  events/ (Redis Streams) · kernel/ (OwnerId, DeviceId, GeoPoint, owner scope)
├── ai-layer/  (TypeScript)  flue/ agents · knowledge/ OKF+MD
├── forecasting/  (Python)   TimesFM worker
└── infra/  traccar/ postgres/ timescaledb/ redis/ vectordb/ litellm/
```

---

## 6. Customer Model (GPSWOX-style)

Single shared database; isolation by ownership; reseller hierarchy; per-account plans. Derived from GPSWOX code (`User.manager_id`, `billing_plan_id`, `user_device_pivot`).

```
Super Admin                         # sees all
   └─ Reseller / Manager            # manager_id — manages their customers
        └─ Customer (Account)       # owner_id — owns devices/data
             ├─ billing_plan        # SaaS plan + devices_limit + subscription
             └─ Sub-user (Client)   # scoped / view-only
```

| Concept | Mechanism | GPSWOX source |
|---|---|---|
| Data isolation | Every record carries `owner_id`; global scope enforces filtering on every query | `user_device_pivot.user_id` |
| Reseller hierarchy | Self-referencing `manager_id` tree | `User.manager()` |
| Plans & limits | Per-account SaaS plan: device limit, subscription expiry | `billing_plan_id`, `devices_limit`, `subscription_expiration` |
| Sub-accounts | Client belongs to an account, restricted permissions | `Client → user()` |

**Important:** "plans" = SaaS subscription plans (device limits, renewal) — **not** vehicle-rental billing (out of scope). Three access tiers: admin → reseller → customer (+ sub-user).

---

## 7. Bounded Contexts

| Context | Responsibility | Key entities |
|---|---|---|
| Accounts, Hierarchy & Plans | Accounts, reseller tree, roles/permissions, plans & limits | Account(User), Client, Role, Permission, Plan |
| Tracking | Ingest positions/telemetry from Traccar; store | Device, Position, Sensor, Trip |
| Fleet | Vehicles, drivers, groups | Vehicle, Driver, DeviceGroup |
| Geo | Geofences, POIs, routes | Geofence, Poi, Route |
| Alerting | Alert rules, events, notifications | AlertRule, Event, Notification |
| Reporting | Reports, analytics, run logs | Report, ReportLog |
| Knowledge | Documents/images, OCR, hybrid RAG, memory | Document, Chunk, KnowledgeItem |
| AI-Agent | Insights, anomaly detection, forecasting, report generation | AgentTask, Insight, Anomaly, Forecast |

---

## 8. Domain Knowledge Mined from GPSWOX

Rebuild this proven domain knowledge clean in Python (mine via opensrc; do not port PHP).

### 8.1 Traccar linkage
- Traccar receives devices on protocol ports (`tracker_ports`), decodes 44 protocols, writes positions, publishes live via Redis.
- Config from `config/tracker.php`: web API port 8082, `registerUnknown=true`, `time.override=serverTime`.
- `devices.traccar_device_id` links to `traccar_devices` (key `uniqueId` = IMEI), which holds latest lat/lng/speed/course/power/protocol.
- **Python:** consume via REST (8082) + WebSocket (`/api/socket`) + Position Forwarding webhook, behind `TraccarPort`.

### 8.2 Position storage at scale
- GPSWOX shards positions across many DBs (`DatabaseService`, `database{id}`, `user_database_pivot`, table-per-device).
- **Python:** replace with **TimescaleDB** hypertables (auto-partition + compression + retention); isolate by `owner_id`.

### 8.3 Sensors (~35 types) — the most valuable knowledge
Ignition, Engine, EngineHours, Odometer, FuelTank, FuelConsumption, Temperature, RFID, Seatbelt, Battery, BatteryExternal, Door, Satellites, GSM, VIN, Tachometer, SpeedECM, Counter, Load, HarshAcceleration, HarshBreaking, HarshTurning, Blocked, Plugged, Acc, DriveBusiness, DrivePrivate, Logical, Numerical, Textual, Datetime.
- Each has **extraction logic** (fuel-tank calibration tables, engine-hours from ACC, harsh-driving thresholds).
- **Python:** `SensorExtractor` strategy per type, unit-tested.

### 8.4 Reports (~100 types), grouped
- **Fuel:** fillings, thefts, level, flow rate, tank usage (+driver/split).
- **Drives/stops:** drives-stops (+drivers/geofences/merge/simplified), stops, stops-filter.
- **Geofences:** in/out (+drivers/engine/shift/24-mode), stop, devices-in-geofences.
- **Speed:** overspeeds (+roads/custom/in-geofence/ECM), speed, underspeeds, gps-vs-ecm compare.
- **Engine/distance:** engine-hours (current/daily/graph), odometer (+daily), drive-time, work-hours-daily.
- **Driver behavior:** RAG (+driver/geofences/seatbelt/with-turn), IVMS drivers, max-rpm.
- **General/fleet:** general-information (+merged/shift), object-history, last-location, travel-sheet, temperature.
- **Ops:** offline-device, installation, sent-commands, users-devices, device-expenses, checklist.
- **Python:** one Polars report engine + ~10–15 core reports; rest generated on demand by Report Analyst agent.

### 8.5 Alerts & events
- `Alert` (rule + conditions) → `AlertDevice`/`AlertGeofence`/`AlertFuelConsumption`; `Event`/`EventCustom`/`EventLog`.
- Types: geofence in/out, overspeed, fuel drain/fill, ignition, offline, harsh driving, sensor, SOS, low battery.
- **Python:** `AlertRule` + rule engine on `PositionReceived` → `Event` → notification → feeds the agentic loop.

### 8.6 Commands (GPRS/SMS)
- `CommandTemplate`/`UserGprsTemplate`, `SentCommand`, `CommandSchedule`; channels: GPRS (via Traccar/TCP), SMS gateway, protocol-specific.
- **Python:** behind `TraccarPort.send_command()`, gated by **ClawPatrol + human approval** (engine cut, unlock = high risk).

### 8.7 Services catalog (~60)
Accounts, Device, Sensor, Geofence/POI/Route, Event/Notification, Command, Task/Checklist, Forward (3rd-party push), FCM/SMS, SimBlocking, Sharing, Database (sharding), Cleaner, Translation. = the **feature map** for the product.

---

## 9. AI Layer

| Component | Role |
|---|---|
| **Flue** (TS service) | AI runtime: orchestrates agents (sessions/tools/skills/sandbox/durability); called via `AIAgentPort`. |
| **LiteLLM** | Model router: unified API, fallback, cost tracking, per-customer cost limits. |
| **Hybrid Knowledge** | Surya OCR + vector RAG (unstructured files: PDFs/images/screenshots) + OKF/MD (curated: fleet rules, device specs, SOPs), behind `KnowledgePort`. |
| **TimesFM** | Forecasting worker reading TimescaleDB: predictive maintenance, fuel forecasting + anomaly baselines, ETA, utilization. |
| **ClawPatrol** | Runtime firewall: blocks/pauses risky actions, routes to approval, logs everything. |

### 9.1 AI Coworkers (agents)
Master/Router, Fuel Investigator, Device & Offline Agent, Report Analyst, Geofence Agent, Driver Agent, Maintenance Watcher, Dispatch Agent (VROOM), Routine Agent, Bottleneck Coach, Client Update Agent, Knowledge Agent, Data Hub Agent, Data Entry & Admin Agent.

### 9.2 AI Output Standard
Every serious answer: Summary · Detected intent · Evidence · Tools/data used · Confidence · Risk level · Recommended next action · Approval requirement · Owner suggestion · Audit status.

---

## 10. Data Model (core)

`Account(User)`, `Client`, `Role`, `Permission`, `Plan`, `Device`, `Vehicle`, `Driver`, `Group`, `Geofence`, `Poi`, `Route`, `Position`(TimescaleDB), `Trip`, `Sensor`, `AlertRule`, `Event`, `Notification`, `Report`, `ReportLog`, `Command`, `SentCommand`, `WorkRequest`, `Case`, `ApprovalRequest`, `Routine`, `MiniApp`, `AuditLog`, `Document`, `Chunk`, `KnowledgeItem`, `Forecast`, `DataSource`, `Dataset`, `DataTable`, `Form`, `Integration`, `NotificationChannel`, `FeatureFlag`.

**Device fields to keep (from GPSWOX):** imei (unique), traccar_device_id, plate_number, vin, registration_number, object_owner, device_model, sim_number, fuel_quantity/price/per_km, engine_hours mode, detect_engine, min_moving_speed, min_fuel_fillings/thefts, snap_to_road, icon_colors{moving,stopped,offline,engine}, expiration_date, parameters(JSON).

**Key relationships:** Account has many Devices (via ownership); Account.manager_id → reseller tree; Device → Vehicle → Driver; Geofence/Case/Document scoped by owner; AuditLog records every sensitive action.

---

## 11. Security & Auth

| Layer | Decision (GPSWOX → Python) |
|---|---|
| Authn | session (web) + JWT/OAuth2 (api) + device auth backend |
| Identity providers | Custom User model (+ secondary credentials option), device provider |
| 2FA | django-otp / pyotp (GPSWOX uses google2fa) |
| Authz | RBAC: Role/Permission models, module→{view,edit,remove}, DRF permissions + policy layer; plan gating in services |
| Hierarchy | Reseller (manager) access; customer; sub-user — enforced via scope |
| Carried-over practices | ConfirmedAction → ClawPatrol; OneSessionPerUser; PermissionsHash (invalidate on change); enforce `owner_id` in service layer (not controllers) |
| Middleware parity | ActiveSubscription, AdminAuth, ManagerAuth, 2FA, captcha, email/phone verify, token auth, tracker auth |

---

## 12. Knowledge / RAG & Document Intelligence

Two truths: **live operational truth** (APIs/Traccar/DB) and **knowledge/evidence truth** (hybrid RAG).

- **Unstructured** (PDFs, scans, screenshots, photos, manuals, contracts): Surya OCR/layout/table extraction → chunk → embed → vector DB → retrieve → grounded answer with citations.
- **Curated/structured** (fleet rules, device specs, SOPs, client preferences, known exceptions): OKF/Markdown files navigated by the agent.
- **Both** behind `KnowledgePort`; combine as GraphRAG when useful.
- RAG types: Document, Image, Table, Case, Memory, Hybrid.
- Security: customer isolation mandatory; document permissions enforced; citations required; all searches logged.

---

## 13. Integrations & Data Hub

Connect → clean → structure → activate operational data. Supports: app/API/DB connections, file imports, tables (manual/CSV/API/AI-filled/AI-analyzed), forms, surveys, quizzes, daily checklists, validation rules, sync jobs, automation triggers, agent access policies. Sources: telematics (Traccar/Wialon/GPSWOX/Teltonika/CANbus), business (ERP/TMS/Odoo/Sheets), comms (WhatsApp/email/SMS/Slack/Teams), files (PDF/Excel/images). Page tabs: Connected Apps, Data Sources, Tables, Forms, Surveys & Quizzes, Data Quality, Sync Jobs, Agent Access, Automation Usage, Logs.

---

## 14. Mini Apps

Operational apps built from tables/forms/datasets/documents/automations/agents. Creation: wizard, prompt, from-table, from-document (quiz), from-automation. Examples: Daily Vehicle Inspection, Fuel Refill Verification, Technician Installation, Client Report Portal, Driver Safety Quiz, Fuel Anomaly Board. Permissions: private/team/project/client/public-with-expiry, row-level, view/edit/approve/export; external publish requires approval. Each can attach an AI coworker.

---

## 15. Navigation & Pages

Home (map-first) · AI Coworkers · Work Requests · Collaboration · Projects · Bottlenecks · Reports · Routine Studio · Integrations & Data Hub · Mini Apps · Alerts · History · Worker Management · Device Management · Vehicle/Asset Management · Driver Management · Fuel Management · Maintenance Management · Dispatch/Route Optimization · Knowledge & Data · Approvals · Audit Log · Channels · Logs · Insights · Settings.

Distinction: **Data Hub** = connect/clean/structure/activate data · **Knowledge & Data** = search/remember/retrieve (RAG) · **Mini Apps** = build apps from that data.

---

## 16. Approval & Safety Model

- **Auto-approved (read-only):** searches, history, summaries, route analysis, draft reports/recommendations, allowed document/table reads.
- **Confirmation required:** create geofence, send message, assign task, schedule automation, export sensitive report, update device settings, publish mini app, external share.
- **High-risk (ClawPatrol gate):** delete records, remove worker, disable device, change admin permissions, mass update, bulk device commands, billing/security changes, public mini-app publish.
- **Flow:** user asks → agent prepares → RBAC check → ClawPatrol risk check → approval if risky → execute only if approved → audit everything.

---

## 17. Artifacts

Interactive AI outputs: report, table, map, chart, PDF preview, workflow, task-plan, compliance, case investigation, route optimization, document answer, data quality, mini-app preview. Capabilities: generate/preview/edit/export/share/regenerate/save-to-reports/convert-to-work-request/schedule/attach-to-case/submit-for-approval.

---

## 18. ENV & Configuration Plan

Use `pydantic-settings` (typed, validated at boot). Groups:

```
# App
APP_ENV, APP_URL, SECRET_KEY
# Databases
POSTGRES_URL, TIMESCALE_URL, VECTOR_DB_URL, REDIS_URL
# Traccar
TRACCAR_API_URL, TRACCAR_WS_URL, TRACCAR_USER, TRACCAR_PASS, TRACCAR_FORWARD_SECRET
# Auth
JWT_SECRET, OAUTH_*, OTP_ISSUER
# AI layer
FLUE_SERVICE_URL, LITELLM_URL, LITELLM_MASTER_KEY
# Knowledge / forecast
SURYA_*, OKF_KNOWLEDGE_PATH, TIMESFM_MODEL
# Channels / maps
SMS_PROVIDER, FCM_KEY, WHATSAPP_*, MAPS_KEY
# Platform
GROWTHBOOK_KEY, SENTRY_DSN, STORAGE_* (S3)
```

(GPSWOX has ~35 config files: app, database, tracker, auth, permissions, limits, payments, sms, maps, fcm, broadcasting, queue, cache, session, google2fa, captcha, cors, webhook — use as the checklist of concerns.)

---

## 19. Scale Strategy (pragmatic)

Stay Modular Django. Extract a service only on a real symptom:

| Symptom | Action |
|---|---|
| Ingestion pressure chokes the rest | Extract `tracking-svc` (FastAPI/Go) + Kafka |
| Heavy reports slow writes | CQRS read models / projections |
| Team deployment conflicts | Split the affected context into a service |
| High AI/forecast load | Scale ai-layer & forecasting workers horizontally |

No full microservices, Istio, or Event Sourcing prematurely. TimescaleDB, an event bus, and LiteLLM are in from day one (cheap, pay off later).

---

## 20. Dev Workflow

- **Spec Kitty** (spec-driven): lock decisions (Django, Flue, LiteLLM, hybrid RAG, TimesFM, customer model, MVCS/Hexagonal) in the **constitution**, then `spec → plan → tasks → next → review → accept → merge` with Kanban + git worktrees + parallel agents.
- **opensrc**: `opensrc path github:djmomen/legacy-code-gpswox` to mine GPSWOX domain knowledge (see the GPSWOX × opensrc playbook). Save mined knowledge as OKF/MD in `ai-layer/knowledge/` — serving both build-time (specs) and run-time (RAG).

---

## 21. Roadmap

1. **Core Fleet OS** — Traccar integration, Django, React, accounts/hierarchy/plans, devices/vehicles/drivers/groups, geofences, trips/history, alerts, basic reports, audit.
2. **Operations Workflow** — work requests, cases, approvals, projects, bottlenecks, collaboration, channels, settings.
3. **Agentic Layer** — Flue agents (Master, Fuel, Device/Offline, Report, Maintenance, Geofence, Bottleneck), LiteLLM, tool audit.
4. **Knowledge & Document Intelligence** — Surya, hybrid RAG, OKF/MD, citations, links to assets/projects/customers.
5. **Forecasting** — TimesFM workers; predictive maintenance/fuel/ETA.
6. **Integrations & Data Hub** — sources, tables, AI tables, forms, surveys, quizzes, agent access.
7. **Mini Apps** — wizard/prompt/table/document-to-app, client portals, publish approvals.
8. **Safety & Governance** — Permission Gateway, ClawPatrol, risk scoring, sensitive-export controls, audit hardening.
9. **Advanced Intelligence & Maturity** — Polars/VROOM, City2Graph, Map3D, GrowthBook, observability, DR, marketplace.

---

## 22. Success Metrics

- **Product:** time-to-find-asset, time-to-report, AI actions completed, approval completion rate, AI suggestion acceptance, document-search success, mini-app usage.
- **Operational:** reduced manual reporting time, unresolved alerts, offline duration, fuel anomalies, overdue maintenance; better workload balance & report delivery.
- **Business:** MRR, revenue/asset, activation time, churn, expansion, AI/integration adoption.
- **Safety:** blocked risky actions, approval response time, audit completeness, cross-customer incidents = **zero**, unauthorized retrieval = **zero**.

---

## 23. Risks & Mitigation

| Risk | Mitigation |
|---|---|
| Scope too broad | Prioritize the loop: data → AI judgment → case → approval → execution → audit |
| AI trust | Show evidence, confidence, missing data, citations, approval |
| Messy telematics data | Stable connectors, flexible mapping, CSV fallback, normalization |
| Dangerous automation | RBAC, approvals, ClawPatrol, audit, action limits |
| Competitors add AI | Defensible via operational memory, responsibility graph, document intelligence, data hub, human-approved execution, vertical fleet knowledge |
| OCR/RAG accuracy | Citations, confidence, never present uncertain evidence as final |
| Flue language split (TS vs Python) | Run Flue as an isolated AI service behind `AIAgentPort`; LiteLLM bridges via HTTP |

---

## 24. Final Principle

> Power + safety + domain focus + customer isolation + manager control + auditability.

> PutPath is an agentic fleet operating system that turns live telematics data, operational documents, structured tables, forms, workflows, and AI coworkers into safe, accountable, automated fleet operations — for fleet and logistics **customers**, built on Traccar, rebuilt clean in Python, powered by Flue + LiteLLM + hybrid knowledge + TimesFM.
