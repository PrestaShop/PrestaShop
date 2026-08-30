---
step: 2
title: "Domain Layer"
previous: step-01-feature-flag.md
next: step-03-adapter-layer.md
deliverable: "src/Core/Domain/{Domain}/ fully populated: ValueObjects, Commands, Queries, DTOs, Exceptions, handler interfaces"
---

# Step 2 — Domain Layer

The domain layer lives in `src/Core/Domain/{Domain}/`. It contains **no implementation** — only contracts, value objects, and data structures. Everything here is framework-agnostic and dependency-free (no Doctrine, no Symfony, no ObjectModel).

Read `@.ai/Component/CQRS/CONTEXT.md` for CQRS conventions (command/query naming, handler interface placement, value object patterns, exception hierarchy).

## Why this step before Adapter

The domain layer is the contract. The adapter layer (step 3) implements those contracts. Writing the domain first lets you reason about the API in isolation: every handler interface, exception class, DTO field is something the rest of the system will depend on. Mistakes here are expensive to undo later.

## Skills to invoke

| Skill | Produces |
|---|---|
| `create-cqrs-commands` | `{Domain}Id` and other ValueObjects, write Commands (Add/Edit/Delete/Toggle), Exceptions, command handler interfaces |
| `create-cqrs-queries` | Read Queries (`Get{Domain}ForEditing`, optional list query), `Editable{Domain}` and sub-resource DTOs, query handler interfaces |
| `create-cqrs-bulk-commands` | Bulk commands (BulkDelete, BulkToggleStatus) — only if the manifest lists bulk actions in the grid |

## Orchestration notes

- Order: identity ValueObject first, then Commands, then Queries — Queries reference Commands' ValueObjects.
- Sub-resource commands are dispatched separately by the form data handler (step 6), never merged into `EditXxxCommand` when the sub-resource has its own table and lifecycle. For trivial fields, a single Edit command is fine.
- Every constraint that the manifest discovered in the legacy validation rules needs a typed `const` code on `{Domain}ConstraintException` — those codes are referenced from Behat assertions in step 4.

## Gate to next step

- [ ] All write commands listed in the manifest exist
- [ ] All read queries listed in the manifest exist
- [ ] DTOs are immutable (typed, readonly, getters only)
- [ ] Handler interfaces created for every command and query
- [ ] No Doctrine, Symfony, or ObjectModel imports under `src/Core/Domain/{Domain}/`
