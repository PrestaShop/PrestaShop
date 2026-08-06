---
name: generate-migration-manifest
description: >
  Synthesize the outputs of `audit-legacy-controller` and `audit-object-model`
  into a single `migration-manifest.md` that serves as the migration
  specification. Every subsequent migration step reads from this document to
  know what to create.
needs: [audit-legacy-controller, audit-object-model]
produces: "migration-manifest.md — authoritative spec listing all commands, queries, form fields, grid columns, hooks, and sub-resources"
subagent: recommended
---


## Instructions

1. Create `migration-manifest.md` at an agreed location (e.g., `docs/migration/{domain}-manifest.md` or project root).
2. Section 1 — Commands: list Add, Edit, Delete, BulkDelete, BulkToggleStatus, ToggleStatus for the domain. Mark each as required or conditional.
3. Section 2 — Queries: list GetForEditing (returns edit DTO) and GetList (for grid). Note any additional queries (e.g., GetForView).
4. Section 3 — Form fields: list each field with type, translatable (Y/N), required (Y/N), validation rule. For complex domains only, propose a tab grouping using `NavigationTabType` — most pages do not need tabs.
5. Section 4 — Grid columns: list each column with its type (DataColumn, ToggleColumn, ActionColumn, PositionColumn).
6. Section 5 — Grid filters: list each filter with its type (TextFilter, SelectFilter, DateRangeFilter, etc.).
7. Section 6 — Sub-resources: list any has-many relations (e.g., carrier ranges, carrier zones) that warrant their own commands and repositories.
8. Section 7 — Hooks: list all legacy hooks with their Symfony equivalents or note "no equivalent yet".
9. Section 8 — Milestone decision: based on complexity, propose how to split the migration (e.g., listing first / form later, or single sprint for simple pages).

## Rules

- This document is the single source of truth — all subsequent migration steps must reference it, not the legacy files
- Every field discovered during the ObjectModel audit must appear in either a form field or a grid column in the manifest
- Mark sub-resources explicitly — missing a sub-resource here causes silent data loss in handlers
- The milestone decision is per-page judgment — split listing/form into separate PRs when the entity is complex; a single PR is fine for simple entities
