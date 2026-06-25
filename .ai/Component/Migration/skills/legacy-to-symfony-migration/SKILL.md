---
name: legacy-to-symfony-migration
description: >
  Step-by-step orchestrator for migrating a PrestaShop Legacy admin page to
  Symfony/CQRS. Covers the full lifecycle from audit to GA. Trigger:
  "migrate the Xxx admin page", "create CQRS for Xxx",
  "add a Symfony form for Xxx", "migrate AdminXxxController".
---

# Legacy to Symfony/CQRS Migration Skill

Read `@.ai/Component/Migration/CONTEXT.md` for conventions, reference pages, dependency graph, and conditional activation matrix.

This skill is an **orchestrator**: each step file explains *why* the step exists, *when* to enter it, *what gates it*, and *which standalone skills to invoke*. The procedural detail lives in the standalone skills themselves and in the relevant Component CONTEXT.md.

## When to use this skill

Trigger when asked to:
- "Migrate the Xxx admin page to Symfony"
- "Create CQRS for the Xxx domain"
- "Add a Symfony form for Xxx"
- "Migrate AdminXxxController"

## Sub-agent delegation (Claude Code)

When the parent agent supports sub-agents (Claude Code does; other tools currently do not), step-00 audits are particularly suited to delegation: each audit reads a large legacy file and emits a structured artifact, so the parent can offload the read cost without losing context. The migration manifest synthesises both audits and acts as the shared context surface for every later step. Skills tagged `subagent: recommended` or `subagent: optional` in their frontmatter are candidates; tools without a sub-agent primitive simply run them in-line — behaviour is identical from the user's standpoint.

## Phase index

| # | File | Title | Deliverable |
|---|------|-------|-------------|
| 0 | [step-00-audit.md](step-00-audit.md) | Audit | Field map, action list, milestone decision |
| 1 | [step-01-feature-flag.md](step-01-feature-flag.md) | Feature Flag | `feature_flag.xml` entry (beta/state=0) — set up early so handlers can carry conditional code |
| 2 | [step-02-domain-layer.md](step-02-domain-layer.md) | Domain Layer | Commands, Queries, ValueObjects, Exceptions, Handler interfaces |
| 3 | [step-03-adapter-layer.md](step-03-adapter-layer.md) | Adapter Layer | Repository, Handlers, DI registration |
| 4 | [step-04-behat-tests.md](step-04-behat-tests.md) | Behat Tests | Integration test coverage for CQRS — gate before UI work |
| 5 | [step-05-listing-page.md](step-05-listing-page.md) | Listing page (vertical slice) | Working listing page: grid + controller actions + listing routes + index template + listing JS |
| 6 | [step-06-form-page.md](step-06-form-page.md) | Form page (vertical slice) | Working add/edit page (CRUD) and/or options block (settings) — branches on form type per block. See the step file for the settings vs CRUD skill chain. |
| 7 | [step-07-playwright-tests.md](step-07-playwright-tests.md) | Playwright Tests | UI test campaigns per feature area |
| 8 | [step-08-general-availability.md](step-08-general-availability.md) | General Availability | Promote flag to stable; optional upgrade SQL handoff |
| 9 | [step-09-removal.md](step-09-removal.md) | Removal | Track legacy controller removal in next major |

## Slice ordering (steps 5 and 6)

Listing-first is the conventional default — it unblocks bulk operations earlier and is usually simpler than the form. Form-first is valid when listing is already migrated or out of scope. Whichever runs first creates the controller class and routing file; the other extends them.
