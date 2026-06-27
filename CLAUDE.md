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

## Tech stack decisions
- **Traccar** — GPS/device tracking engine. Fork cleanly, stay upstream-compatible, extend around it via APIs (avoid editing core).
- **Django** — trusted business backend & control layer (tenants, users/workers, RBAC, billing, reports, workflows, approvals, audit, integrations, agent permission gateway). **Backend is the source of truth.**
- **React** — unified product interface (map, modules, AI coworkers, artifacts, reports, mini apps).
- **Hermes-Agent** (nousresearch/hermes-agent) — AI coworker runtime (profiles, memory, skills, sessions, MCP/CLI tools). Hermes is NOT the permission system.
- **Surya + RAG + swappable vector DB** (pgvector/Qdrant/Milvus/Weaviate/TurboVec) — document & image intelligence; grounded answers with citations.
- **Polars** — analytics. **VROOM** — route optimization. **City2Graph** — spatial graph (later). **Map3D** — 3D/digital twin (later). **Manifest** — model router (later).
- **ClawPatrol** — runtime approval/action firewall for risky actions.
- **GrowthBook** — feature flags. **Databasus + cloud backups/WAL** — DR.
- Internal-only dev tools: Ponytail, opensrc, AI Engineering Coach, CLI Printing Press, Skybridge (typed MCP apps).

Architecture sentence: *Traccar tracks · Django controls · Polars analyzes · VROOM
optimizes · Surya understands documents · RAG retrieves evidence · Hermes acts ·
Skybridge/MCP exposes tools · ClawPatrol protects · React presents.*

## Non-negotiable principles
- **Map-first** home; chat is a core navigation layer; artifacts open only when needed.
- **Multi-tenancy is mandatory and strict** — every request carries tenant + user + role + scope; zero cross-tenant access (data, agents, tools, document search, tables).
- **No prompt-only security** — enforce via backend RBAC, tenant-scoped tokens, MCP/CLI allowlists, permission gateway, ClawPatrol, audit logs, human approvals.
- **AI must explain serious decisions** — intent, tools, evidence, confidence, risk, approval status, next action, owner (see AI Output Standard in PRD §20).
- **Action over visualization**; everything auditable.
- Note: Flue (TypeScript) was considered earlier; current direction uses **Hermes-Agent** as the AI runtime.

## Full specification
The complete, authoritative PRD lives at **`docs/PUTPATH_PRD.md`**. Treat it as the
source of truth for scope, pages, data model, agents, RAG, Data Hub, Mini Apps,
safety model, and roadmap. This file is a summary anchor — when in doubt, read the PRD.
