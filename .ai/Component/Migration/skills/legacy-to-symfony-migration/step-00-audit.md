---
step: 0
title: "Audit"
previous: null
next: step-01-feature-flag.md
deliverable: "A written field map, action inventory, and milestone decision before writing any new code"
---

# Step 0 — Audit

Before touching a single new file, you must have a complete picture of what the legacy page does. Skipping this step leads to missing CQRS commands, incomplete form fields, and broken UI interactions discovered late.

## Why this step

Legacy admin controllers blend routing, persistence, validation, business rules, and view rendering in one class. The migration cannot proceed without a clear map of those concerns: which fields exist, which actions are dispatched, which hooks are emitted, which sub-resources need their own commands, and how complex the resulting migration will be.

## Skills to invoke

| Skill | Produces |
|---|---|
| `audit-legacy-controller` | Action list, hook list, field list discovered in `Admin{Domain}sController.php` |
| `audit-object-model` | DB schema map, relation list, multilingual fields, validation rules from `classes/{Domain}.php` |
| `generate-migration-manifest` | `migration-manifest.md` — the single source of truth for every subsequent step |

## Orchestration notes

1. Run `audit-legacy-controller` and `audit-object-model` in either order — they are read-only and independent.
2. Run `generate-migration-manifest` last — it consumes the outputs of the two audits.
3. Read the generated manifest end-to-end and confirm with the team before starting any code-writing step. The manifest is what every later step references.

## Milestone decision (orchestrator-only concern)

The audit ends with a **milestone strategy** captured in the manifest. This is judgment, not a recipe:

| Strategy | When to choose |
|---|---|
| **Single sprint** | Simple entity (< 10 fields, no sub-resources, no multistore) |
| **Listing first, form later** | Complex entity; listing unblocks bulk actions immediately |
| **CQRS first, then UI** | Team wants the API layer solid before building the interface |

Document the decision in the PR description so reviewers understand the scope boundary. Listing-first / form-later is normal — the two slices (steps 5 and 6) can ship months or years apart.

## Gate to next step

- [ ] Manifest exists and is reviewed
- [ ] Every legacy field appears in the manifest as either a form field or a grid column
- [ ] All sub-resources with their own table are listed
- [ ] Multistore status confirmed
- [ ] Hooks listed
- [ ] Milestone strategy documented in the PR description
