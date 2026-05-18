---
name: create-cqrs-queries
description: >
  Create the read-side CQRS layer: queries, result DTOs, and query handler interfaces.
  Covers Get{Domain}ForEditing (single entity for edit form) and optionally
  Get{Domain}sForListing (grid data source). Trigger: "create queries for {Domain}",
  "set up read side for {Domain}".
needs: [create-cqrs-commands]
produces: "src/Core/Domain/{Domain}/Query/, QueryResult/, QueryHandler/ — read-side domain layer"
subagent: optional
---

# create-cqrs-queries

Read [CQRS/CONTEXT.md](../../CONTEXT.md) for conventions (scalar inputs/VO getters, QueryResult scalar-only rule).

## 1. Get-for-editing query

Create `src/Core/Domain/{Domain}/Query/Get{Domain}ForEditing.php`:

- Constructor takes `int $id` (scalar)
- Getter returns VO: `getId(): {Domain}Id`
- Queries are read-only data objects — no side effects

## 2. Result DTO

Create `src/Core/Domain/{Domain}/QueryResult/Editable{Domain}.php`:

- Constructor parameters: all fields the edit form needs to pre-fill (scalar types only — see CONTEXT.md)
- Public getter for every field
- Immutable: no setters, all values set at construction

**Reference:** `src/Core/Domain/Tax/QueryResult/EditableTax.php` (simple), `src/Core/Domain/Manufacturer/QueryResult/EditableManufacturer.php` (with associations)

### Expose primitive state fields directly

Recurring PR review trap: a result DTO exposes a derived collection (`getActions()`, `getAvailableTransitions()`, `getDisplayLabel()`) but **not** the raw state primitive it was computed from (`getStatus()`, `getType()`, `isActive()`). Consumers then have to infer state from the derived data, which is fragile and forces them to bypass the bus.

Rule of thumb: every column that exists on the ObjectModel/Doctrine entity and is meaningful for the UI must have its own getter on the DTO — even if a derived getter also exists. Derived getters complement the primitive, they do not replace it.

## 3. List query (assess first)

Most PS grids use `SearchCriteria` + grid `QueryBuilder` directly — no explicit CQRS query class needed.

Check the domain convention before creating a list query:
- If the domain uses `SearchCriteria` + `QueryBuilder` in the grid: skip this step
- If the domain uses an explicit query: create `Get{Domain}sForListing.php` with `SearchCriteria` parameter

**Reference:** Most domains (Tax, Manufacturer, Category) use the grid QueryBuilder pattern without an explicit list query.

## 4. Query handler interfaces

Create in `src/Core/Domain/{Domain}/QueryHandler/`:

- `Get{Domain}ForEditingHandlerInterface` with `handle(Get{Domain}ForEditing $query): Editable{Domain}`
- If list query exists, create corresponding interface
- Query handlers always return data (typed DTO or array) — never void

## Rules

Conventions (scalar-only DTOs, read-only queries) are in [CQRS/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Map ALL editable fields in the DTO — missing fields cause empty form fields on edit
- Return typed DTOs, not ObjectModel instances
