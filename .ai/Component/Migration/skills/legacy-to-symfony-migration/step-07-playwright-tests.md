---
step: 7
title: "Playwright UI Tests"
previous: step-06-form-page.md
next: step-08-general-availability.md
deliverable: "tests/UI/campaigns/functional/BO/{section}/{subsection}/ campaigns covering CRUD, filters, bulk actions, position (if applicable), and per-tab behavior"
---

# Step 7 — Playwright UI Tests

Playwright campaigns are the gate between beta and GA. They validate end-to-end UI behavior of both slices (listing + form) on real browsers. Start writing them as each slice stabilizes — not all at once at the end.

Read `@.ai/Component/Playwright/CONTEXT.md` for the page-object pattern, fixtures, and resetter conventions.

## Why a UI gate

The Behat suite (step 4) validates the CQRS layer. Playwright validates everything stacked on top: routing, controller wiring, JS interactions, form theme rendering, grid extensions, drag-and-drop. It is the only layer that catches regressions like a missing `enctype="multipart/form-data"` or a Vue component that fails to mount.

## Skills to invoke

| Skill | Produces |
|---|---|
| `create-playwright-page-objects` | BO page objects in `ui-testing-library` — Page Object Model: encapsulate selectors, never assert |
| `create-playwright-test-data` | Faker data classes + predefined data objects in `ui-testing-library` |
| `write-playwright-campaigns` | Campaigns in the core repo: CRUD lifecycle, filter/sort, bulk actions, position reorder, per-tab field verification |

## Orchestration notes

- Page objects (selectors + interactions) live in `ui-testing-library` (separate repo); test campaigns (assertions) live in the core repo and import them.
- Each campaign creates the data it needs and resets via a dedicated `Resetter` class — campaigns must not depend on the order they are run.
- Beta-stage campaigns enable the feature flag in `beforeAll`; the call is removed in step 8 (GA).
- Number campaigns following the existing convention of the target directory (`01_CRUD…`, `02_filterSort…`, …).

## Gate to next step

- [ ] CRUD lifecycle campaign passes against the flag-enabled environment
- [ ] Filter/sort campaign passes
- [ ] Bulk actions campaign passes (per the manifest's bulk list)
- [ ] Position reorder campaign passes (if applicable)
- [ ] Per-tab campaign exists for each tab when tabs were used
- [ ] All campaigns are green; no flaky tests
