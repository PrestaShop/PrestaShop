---
name: component-context-generator
description: >
  Generates a CONTEXT.md file for a PrestaShop shared component inside the `.ai/Component/` folder.
  Trigger this skill when the user asks to "generate a context for [Component]", "document the [X] component",
  "fill in the CONTEXT.md for [Component]", or when working inside `.ai/Component/` directories.
  Components live under `src/Core/{Name}/` and/or `src/Adapter/{Name}/` — they are shared infrastructure,
  not business domains. Examples: Grid, Form, Hook, CQRS, Translation, Router.
subagent: recommended
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

Target size: **aim for ~30–50 lines.** Simple components fit in that range. If a component legitimately needs more (multiple parallel patterns, several layers, substantial non-obvious behaviour), prefer splitting into sub-contexts over inflating a single file — see "When to split into sub-contexts" below. A single CONTEXT.md above ~80–100 lines is a smell.

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

### 7. Write Related (use sparingly)

Links to other `.ai/Component/` or `.ai/Domain/` context files — but **only when the relationship is non-obvious**.

The whole point of splitting contexts into separate files is to avoid loading everything at once. Every cross-reference is a potential cascade: an AI agent reads component A, follows a link to component B, follows B's link to C... and ends up loading all contexts. This defeats the purpose of the split.

**Include a link when:**
- The relationship is architecturally surprising (e.g. PositionUpdater lives inside Grid's source tree)
- Two components coexist during a migration and the coexistence has gotchas (e.g. Twig ↔ Smarty)

**Do NOT include a link when:**
- The relationship is obvious from imports (e.g. "Controller dispatches CQRS commands")
- You're linking just to mention a hook name or a specific class — those are greppable
- The link points to a domain just because that domain is a heavy consumer of the component
- The link would create a bidirectional reference (A → B and B → A)

When in doubt, omit the link. An agent can always find related contexts via the index in `.ai/CONTEXT.md`.

---

## When to split into sub-contexts

Most components fit comfortably in a single `CONTEXT.md`. A few don't — when the component has **two (or more) parallel patterns that share little code but coexist under the same umbrella**, split the file rather than letting it grow past ~80 lines.

### Trigger conditions (all should hold)

- Two or more usage patterns coexist (e.g. settings forms vs CRUD forms; legacy ObjectModel vs Doctrine repositories within the same Component).
- Most readers only work on **one pattern at a time** — loading the other pattern's rules wastes tokens.
- The patterns each have non-trivial pattern-specific rules (base classes, service folders, hooks, anti-patterns) that don't reduce to a few bullets.
- A meaningful shared surface exists (decision tree, shared concerns, skills table) — otherwise consider splitting into two separate Components instead.

### Recommended layout

```
.ai/Component/{Name}/
├── CONTEXT.md            ← lean root: purpose, decision tree, shared concerns,
│                            skills table, links to each sub-context.
│                            Pure routing hub — minimal pattern-specific detail.
├── {PATTERN_A}.md        ← pattern A: required layers, service definitions,
│                            hooks, anti-patterns, canonical example.
├── {PATTERN_B}.md        ← pattern B: same shape as PATTERN_A.
└── skills/
    ├── {pattern-a skill}/  ← body opens with: Read CONTEXT.md + PATTERN_A.md
    ├── {pattern-b skill}/  ← body opens with: Read CONTEXT.md + PATTERN_B.md
    └── {generic skill}/    ← body opens with: Read CONTEXT.md only
```

Naming: use the existing `STRUCTURE.md` / `GOTCHAS.md` / `MULTISTORE.md` convention for sub-context files — UPPERCASE single noun describing the pattern (e.g. `SETTINGS.md`, `CRUD.md`).

### Rules

- **Root `CONTEXT.md` stays the entry point.** Every external link from other components/domains points at `CONTEXT.md`, not at a sub-context — the decision tree in the root routes the reader to the right sub-context. External links should only specialise to a sub-context when their own surrounding text is unambiguously pattern-specific (saves a hop without confusing readers).
- **No duplication between root and sub-contexts.** Shared concerns live in the root. Pattern-specific rules live in exactly one sub-context.
- **Skills load sub-contexts conditionally.** A pattern-specific skill body opens with `Read @.ai/Component/{Name}/CONTEXT.md` *and* `Read @.ai/Component/{Name}/{PATTERN}.md`. A skill that serves both patterns (e.g. a unified controller-actions skill) opens with `Read @.ai/Component/{Name}/CONTEXT.md` and instructs the reader to load the relevant sub-context based on the branch they're working on.
- **Skills table in the root** includes a "Pattern detail to load" column so the reader sees the load decision upfront.

### Reference

See `.ai/Component/Forms/` for a worked example: `CONTEXT.md` (decision tree + shared concerns) + `SETTINGS.md` + `CRUD.md`, with skills that route to one sub-context or the other.

### When NOT to split

- The component has only one pattern, just lots of layers — solve with a tighter Layers table instead.
- The sub-contexts would each be under ~20 lines — the split adds ceremony without saving tokens.
- The "two patterns" are actually one pattern with optional features — keep them in one file with a "Conditional layers" subsection.

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

- [PositionUpdater Component](../PositionUpdater/CONTEXT.md) — drag-and-drop reordering sub-layer (lives inside Grid source tree)
```

---

## Output

Write the completed CONTEXT.md to:
```
.ai/Component/{Name}/CONTEXT.md
```

If the directory does not exist, create it first.
After writing, confirm the file path to the user.
