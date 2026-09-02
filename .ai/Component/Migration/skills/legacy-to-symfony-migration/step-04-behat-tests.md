---
step: 4
title: "Behat Integration Tests"
previous: step-03-adapter-layer.md
next: step-05-listing-page.md
deliverable: "tests/Integration/Behaviour/ green for all CQRS commands and queries — gate before any UI work"
---

# Step 4 — Behat Integration Tests

Integration tests are written **immediately after the Adapter layer**, before building any UI. They give confidence that the CQRS layer is correct before the controller and form layers are built on top of it. A failing Behat suite is a blocker — do not proceed to step 5 with red tests.

Read `@.ai/Component/Behat/CONTEXT.md` for Behat conventions (stateless steps, `referenceToId`, context registration, available tags, exception handling via `setLastException` / `assertLastErrorIs`).

## Why this is the gate

The CQRS layer is the API the rest of the application sees. If a command or query is wrong, every UI layer built on top of it will inherit the bug. Behat at this point validates:
- Each command does what its name promises (and only that)
- Each query returns the right DTO shape
- Every typed constraint exception is thrown with the right code on the right inputs
- Multistore behavior (if the entity is multistore-aware) on per-shop scenarios

## Skills to invoke

| Skill | Produces |
|---|---|
| `create-behat-context` | `{Domain}FeatureContext` class + registration in `behat.yml` |
| `write-behat-scenarios` | `.feature` files covering CRUD, bulk, constraint, i18n, multistore — split into multiple feature files when the domain is large |

## Orchestration notes

- One scenario per command path; one scenario per query path. Do not merge "I edit and then I read it back" into one — they exercise different handlers.
- Every typed `{Domain}ConstraintException::INVALID_*` code from step 2 needs a scenario that triggers it.
- For complex domains, split scenarios across multiple `.feature` files (one per area: management, sub-resource, multistore) so the suite can be run partially when debugging.

## Gate to next step

- [ ] Every command from the manifest has at least one happy-path scenario
- [ ] Every query has at least one scenario asserting on the DTO shape
- [ ] Every constraint code has at least one scenario triggering it
- [ ] Multistore scenarios cover the relevant tier (if the entity is multistore-aware)
- [ ] Full Behat suite green in CI
