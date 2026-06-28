# Project Memory

## Repository role
This repo (`djmomen/legacy-code-gpswox`) is the **GPSWOX legacy system** (PHP/Laravel).
It is **a domain-knowledge reference only** — not the foundation to build on.
Mine it for proven telematics domain knowledge (alerts, sensors, protocols, commands,
reports, geofences, fuel, maintenance, SaaS plan limits) and **rebuild those modules
cleanly in the new stack**. Do NOT port the PHP code.

## Active product: PutPath
**PutPath** is an **Agentic Fleet Operating System** / AI Operations Workforce platform
for telematics, logistics, fleet, construction, rental, and heavy-equipment companies.
It is a **multi-tenant SaaS**, not a framework.

One-line positioning:
> PutPath is the AI operating layer that turns fleet data, documents, alerts, and
> workflows into assigned work, investigations, reports, approvals, and operational decisions.

It is NOT: a generic chatbot, a plain GPS dashboard, a reporting-only tool, a Traccar
wrapper, or a customer-facing coding-agent platform. Agents stay strictly inside the
fleet / logistics / telematics / operations scope.

### Core thesis — Operational Intelligence Loop
`detect → investigate → explain → assign → approve → execute → follow up → learn`
PutPath continues past `alert → report → dashboard` into accountable action.

## Tech stack decisions (FINAL — see docs/PUTPATH_PRD_FINAL.md)
- **Traccar** — GPS/device tracking engine. Fork cleanly, stay upstream-compatible; consume via REST/WebSocket/Position-Forwarding behind a `TraccarPort`. Never edit core.
- **Django + DRF** — trusted business backend & control layer (customers, users/workers, RBAC, plans, reports, workflows, approvals, audit, integrations, agent permission gateway). **Backend is the source of truth.** Bounded Contexts = Django apps + Hexagonal edges + service layer (MVCS).
- **React** — unified product interface (map-first, AI coworkers, artifacts, reports, mini apps).
- **Flue** (flueframework.com, TypeScript) — **AI runtime** (chosen over Hermes-Agent). Runs as an isolated AI-layer service; Django calls it via `AIAgentPort`. Sessions/tools/skills/sandbox/durability.
- **LiteLLM** — AI model router (unified proxy, fallback, per-customer cost limits; language-neutral via HTTP). Chosen over Manifest.
- **Hybrid knowledge / RAG** — Surya OCR + vector RAG (unstructured: PDFs/images/scans) **+** OKF/Markdown (curated: fleet rules, device specs, SOPs), behind `KnowledgePort`. Vector DB swappable (pgvector/Qdrant/Milvus/Weaviate).
- **TimesFM** (google-research/timesfm) — time-series forecasting worker reading TimescaleDB (predictive maintenance, fuel, ETA, utilization).
- **Polars** — analytics. **VROOM** — route optimization. **City2Graph** — spatial graph (later). **Map3D** — 3D/digital twin (later).
- **ClawPatrol** — runtime approval/action firewall for risky actions.
- **GrowthBook** — feature flags. **Databasus + cloud backups/WAL** — DR.
- **Databases:** PostgreSQL (business) + **TimescaleDB** (positions/telemetry) + vector DB (RAG) + Redis (cache). **Events:** Redis Streams → Kafka at scale.
- **Customer model (GPSWOX-style):** single shared DB + row-level `owner_id` isolation + `manager_id` reseller tree + per-account SaaS plans (NOT vehicle-rental billing). Mined from GPSWOX (`User.manager_id`, `billing_plan_id`, `user_device_pivot`).
- **Dev workflow:** Spec Kitty (spec-driven, spec-kitty.ai) + opensrc (opensrc.sh, mine GPSWOX source). Save mined knowledge as OKF/MD (serves build-time specs + run-time RAG).

Architecture sentence: *Traccar tracks · Django controls · Flue acts · LiteLLM routes ·
Surya+RAG+OKF understand · TimesFM forecasts · Polars analyzes · VROOM optimizes ·
ClawPatrol protects · React presents.*

## Non-negotiable principles
- **Map-first** home; chat is a core navigation layer; artifacts open only when needed.
- **Strict customer isolation** — the platform serves **customers** (fleet/logistics companies), GPSWOX-style: every request carries owner/customer + user + role + scope; zero cross-customer access (data, agents, tools, document search, tables). (Frame as "customers", not "tenants".)
- **No prompt-only security** — enforce via backend RBAC, tenant-scoped tokens, MCP/CLI allowlists, permission gateway, ClawPatrol, audit logs, human approvals.
- **AI must explain serious decisions** — intent, tools, evidence, confidence, risk, approval status, next action, owner (see AI Output Standard in PRD §20).
- **Action over visualization**; everything auditable.
- Note: **Flue** is the chosen AI runtime (Hermes-Agent was considered but dropped).

## Strategy
Build a clean **GPSWOX clone in Python** first (proven, sellable foundation), then layer
PutPath agentic ideas gradually — but build the clone **AI-ready from day one** (Ports,
Domain Events, owner_id scope, clean domain) so PutPath attaches with no refactor.

## Docs & artifacts (in this repo)
- **`docs/PUTPATH_PRD_FINAL.md`** + `.html` — the consolidated final PRD (stack, customer model, domain knowledge, AI layer). Primary source of truth.
- `docs/PUTPATH_PRD.md` — original product-vision PRD (pages, agents, data model).
- `docs/ARCHITECTURE_FINAL.html` — final architecture & stack.
- `docs/STACK_COMPARISON.html` — stack decisions with scoring.
- `docs/GPSWOX_TO_PYTHON.html` — GPSWOX experience → Python rebuild guide.
- `docs/GPSWOX_OPENSRC_PLAYBOOK.html` — recipes to mine GPSWOX with opensrc.
- `design/putpath-prototype/` — full 26-page HTML UI prototype (open `index.html`).
