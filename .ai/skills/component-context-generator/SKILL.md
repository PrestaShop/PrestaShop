---
name: component-context-generator
description: >
  Generates a CONTEXT.md file for a PrestaShop shared component inside the `.ai/Component/` folder.
  Trigger this skill when the user asks to "generate a context for [Component]", "document the [X] component",
  "fill in the CONTEXT.md for [Component]", or when working inside `.ai/Component/` directories.
  Components live under `src/Core/{Name}/` and/or `src/Adapter/{Name}/` — they are shared infrastructure,
  not business domains. Examples: Grid, Form, Hook, CQRS, Translation, Router.
---

# PrestaShop Component CONTEXT.md Generator

## Purpose

Generate a lean, accurate `CONTEXT.md` for a PrestaShop shared component
(`src/Core/{Name}/` + `src/Adapter/{Name}/`) by exploring the real codebase.

Components are **not** business domains — they have no CQRS structure. They provide shared infrastructure consumed by many domains.

## Output format

**Principle: paths not inventories.** Class names are greppable on demand. What earns tokens is:
- The layer table (where to look)
- Non-obvious patterns (things that would surprise a competent PHP developer)

```markdown
# {Component Name} Component

> **Status:** Draft — this context file is a starting point and should be refined by domain experts.

## Purpose

{1–2 sentences: what this component provides and what it does NOT do}

## Layers

| Layer | Path |
|-------|------|
| {layer name} | {path} |

## Non-obvious patterns

- {bullet: surprising architectural decision, gotcha, or non-obvious constraint}

## Canonical examples

- {file path — 1-line role}

## Skills

- [`skill-name`](../../skills/skill-name/SKILL.md) — one-line description

## Related

- [{Component}]({path}) — {why related}
```

**`## Skills` is optional** — only include it if a skill in `.ai/skills/` targets this component. Omit the section entirely if no relevant skill exists.

**Do NOT include:** `## Coding standards`, `## Do`, `## Don't`, `## Testing expectations`, `## Architecture overview` with verbose subsections. These inflate token cost without adding value.

Target size: **20–35 lines**.

---

## Step-by-step process

### 1. Confirm the target is a Component, not a Domain

- **Component** → `src/Core/{Name}/` with NO `Command/`, `Query/`, `Handler/` subdirectories at root
- **Domain** → `src/Core/Domain/{Name}/` — use `domain-context-generator` instead

### 2. Explore the codebase

Use the Explore agent (thoroughness: very thorough) to map:

- `src/Core/{Name}/` — interfaces, abstract classes, key concrete classes
- `src/Adapter/{Name}/` — concrete implementations, legacy bridges
- Grep for the component's main interface across `src/Core/Domain/` and `src/PrestaShopBundle/` — identify 2–3 representative consumers
- Note any sub-patterns with a non-obvious design decision

### 3. Fill the Layers table

One row per architectural layer actually found. Keep paths as specific as possible (file path for single-file layers, directory for multi-file layers).

### 4. Write Non-obvious patterns

Only include what would **surprise** a competent PHP developer:
- Coexisting design patterns (e.g. legacy vs modern)
- Constraints that break obvious assumptions (e.g. "stopPropagation() is blocked")
- Generated/cached artifacts that must be refreshed manually
- Performance or ordering gotchas
- Subtle API distinctions that cause bugs if missed

Skip anything derivable from reading the code for 5 minutes.

### 5. Write Canonical examples

Pick 2–3 files: the main interface, the most-used implementation, and one domain consumer.

### 6. Check for relevant skills

List the contents of `.ai/skills/` and check if any skill targets this component. If one exists, include a `## Skills` section before `## Related` linking to it.

### 7. Write Related

Links to other `.ai/Component/` or `.ai/Domain/` context files that are architecturally connected.

---

## Reference: lean Grid component example

```markdown
# Grid Component

> **Status:** Draft — this context file is a starting point and should be refined by domain experts.

## Purpose

Infrastructure for rendering and managing back-office data tables: column definitions, filters, row/bulk actions, query builders, data factories, and drag-and-drop position reordering. Does not contain any business data — each domain provides its own `GridDefinitionFactory` and Doctrine query builder.

## Layers

| Layer | Path |
|-------|------|
| Core contracts + factory | `src/Core/Grid/` |
| Column types, row/bulk actions | `src/Core/Grid/Column/`, `src/Core/Grid/Action/` |
| Query builder base | `src/Core/Grid/Query/AbstractDoctrineQueryBuilder.php` |
| Position updater | `src/Core/Grid/Position/` |
| Adapter utilities | `src/Adapter/Grid/` |

## Non-obvious patterns

- `AbstractGridDefinitionFactory` dispatches `action{GridId}GridDefinitionModifier` hook — modules add columns/actions without touching core code
- `SearchCriteriaInterface` is stored as a Symfony request attribute per grid, not a service — each grid type has its own `{Domain}Filters` class
- 60+ concrete query builders exist (one per domain grid) — all extend `AbstractDoctrineQueryBuilder` and implement `getSearchQueryBuilder()` + `getCountQueryBuilder()`

## Canonical examples

- `src/Core/Grid/Definition/Factory/AbstractGridDefinitionFactory.php`
- `src/Core/Grid/Definition/Factory/ProductGridDefinitionFactory.php`
- `src/Core/Grid/Query/AbstractDoctrineQueryBuilder.php`

## Related

- [CQRS Component](../CQRS/CONTEXT.md) — some grids dispatch queries via `QueryBus`
- [Forms Component](../Forms/CONTEXT.md) — filter forms use `FormChoiceProviderInterface`
- [Hook Component](../Hook/CONTEXT.md) — `action{GridId}GridDefinitionModifier` hook
```

---

## Output

Write the completed CONTEXT.md to:
```
.ai/Component/{Name}/CONTEXT.md
```

If the directory does not exist, create it first.
After writing, confirm the file path to the user.
