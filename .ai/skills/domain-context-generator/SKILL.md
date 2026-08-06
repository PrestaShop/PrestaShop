---
name: domain-context-generator
description: >
  Generates a CONTEXT.md file for a PrestaShop domain inside the `.ai/Domain/` folder.
  Trigger when the user asks to "generate a context", "fill in a CONTEXT.md",
  "write the context for [Domain]", "document the [X] domain",
  or when working inside `.ai/Domain/` directories.
  Also triggers on "create context for", "add AI context", "populate CONTEXT.md".
subagent: recommended
---

# PrestaShop Domain CONTEXT.md Generator

## Purpose

Generate a **minimal**, accurate `CONTEXT.md` for a PrestaShop business domain
(`src/Core/Domain/{Name}/`). The goal is the smallest file that gives an AI agent
enough orientation to work in the domain — not an inventory of every class.

## Output format

```markdown
# {Domain} Domain

> **Status:** Draft — this context file is a starting point and should be refined by domain experts.

## Purpose

{1–2 sentences: what this domain does and what it does NOT do}

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/{Name}/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/{Name}/` — handler implementations, repositories |
| Legacy ObjectModel | `classes/{Name}.php` ({N} lines) — do not add logic here |
| Back-office UI | controller path, grid factory name, TS frontend path |

## Non-obvious patterns

- {Things that aren't discoverable by reading the directory structure}
- {Surprising abstractions, delegation chains, cross-domain flows, legacy gotchas}

## Canonical examples

- `path/to/Command.php` + `path/to/Handler.php`
- `path/to/Query.php` + `path/to/ResultDTO.php`

## Skills

- [`skill-name`](../../skills/skill-name/SKILL.md) — one-line description

## Related

- [Component/Domain](path) — one-line reason
```

**`## Skills` is optional** — only include it if a skill in `.ai/skills/` targets this domain. Omit the section entirely if no relevant skill exists.

**Do NOT include:** class name inventories (command names, exception names, value object names, handler names). These are greppable on demand and waste context tokens.

**Do NOT include:** `## Coding standards`, `## Do`, `## Don't`, `## Testing expectations` — project-wide, live in `.ai/CONTEXT.md`.

---

## Step-by-step process

### 0. Confirm the target is a Domain, not a Component

Check that `src/Core/Domain/{Name}/` exists and contains a CQRS structure (`Command/`, `Query/`, or handler interfaces). If you don't find a dedicated CQRS layer or a Controller for this domain, it is probably a Component — use the `component-context-generator` skill instead.

### 1. Explore the codebase

Use the Explore agent to map:
- `src/Core/Domain/{Name}/` — subdirectory names (Command/, Query/, ValueObject/, Exception/, sub-domains)
- `src/Adapter/{Name}/` — subdirectory names (CommandHandler/, QueryHandler/, Repository/, any unusual patterns)
- `classes/{Name}.php` — does it exist? approximate line count
- `src/PrestaShopBundle/Controller/Admin/**/{Name}Controller.php` — exists?
- Any sub-domains under `src/Core/Domain/{Name}/` with their own CQRS structure
- Other domains that reference this domain (grep for `{Name}Id` or `{Name}Command`)

**Do NOT** enumerate every file — just note what layers/patterns exist and what's non-obvious.

### 2. Write the Layers table

One row per layer that actually exists. Path only — no class names.

### 3. Write Non-obvious patterns

Ask: *what would surprise a developer reading this domain for the first time?*
- Separate calculation/engine layer (like Cart's `src/Core/Cart/`)
- Delegation chains (like Product's filler pattern)
- Sub-domains with their own full CQRS structure
- Legacy class size and usage warning
- Cross-domain flows (which other domain consumes this one)

Skip anything obvious from the directory structure.

### 4. Write Canonical examples

2 file path pairs maximum:
- One Command + its Handler
- One Query + its result DTO

### 5. Check for relevant skills

List the contents of `.ai/skills/` and check if any skill targets this domain (e.g. a `create-{entity}` or `add-{domain}-form` skill). If one exists, include a `## Skills` section before `## Related` linking to it.

### 6. Write Related (use sparingly)

Every cross-reference is a potential cascade: an AI agent reads domain A, follows a link to domain B, follows B's link to component C... and loads everything. This defeats the purpose of the split.

**Include a link when:**
- The cross-domain flow is non-obvious and would surprise a developer (e.g. Cart → Order conversion happens in the Order domain, not Cart)
- A DevDocs URL exists for this domain

**Do NOT include a link when:**
- The relationship is obvious from imports (e.g. "this domain uses CQRS" — they all do)
- You're linking to a component just because the domain consumes it (Controller, Forms, Grid are consumed by every migrated domain)
- The link would create a bidirectional reference (A → B and B → A)

When in doubt, omit the link. An agent can always find related contexts via the index in `.ai/CONTEXT.md`.

---

## Reference: Cart domain (canonical lean example)

```markdown
# Cart Domain

> **Status:** Draft — this context file is a starting point and should be refined by domain experts.

## Purpose

Manages the shopping cart lifecycle: creation, product additions/removals, quantity updates,
cart rule application, address/carrier/currency settings, and price computation.
Does not handle order creation — that is the Order domain's responsibility.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Cart/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Cart/` — handler implementations, `CartRepository`, `CartProductsComparator` |
| Calculation engine | `src/Core/Cart/` — pure price/tax/discount computation, independent of persistence |
| Legacy ObjectModel | `classes/Cart.php` (5 000+ lines), `classes/CartRule.php` — do not add logic here |
| Presenter | `src/Adapter/Presenter/Cart/` — lazy-loading Smarty presentation layer |
| Back-office UI | `CartController.php`, `CartGridDefinitionFactory`, `CartSummaryType`, `cart-editor.ts` |

## Non-obvious patterns

- The **calculation engine** (`src/Core/Cart/`) is separate from the CQRS domain layer —
  `Calculator` orchestrates totals purely in memory; adapter handlers call it before persisting
- Cart→Order conversion happens via `AddOrderFromBackOfficeCommand` in the **Order domain**,
  which takes a `CartId`; the Cart domain has no knowledge of this
- `AbstractCartHandler` in Adapter provides shared cart-loading and validation used by all handlers

## Canonical examples

- `src/Core/Domain/Cart/Command/AddProductToCartCommand.php` + `src/Adapter/Cart/CommandHandler/AddProductToCartHandler.php`
- `src/Core/Cart/Calculator.php` — calculation engine entry point

## Related

- [Order Domain](../Order/CONTEXT.md) — Cart→Order conversion happens in the Order domain via `AddOrderFromBackOfficeCommand`
- [DevDocs](https://devdocs.prestashop-project.org/9/development/architecture/domain/references/cart/#cart-domain/)
```

---

## Output

Write to `.ai/Domain/{Name}/CONTEXT.md`. Create the directory if needed.
Confirm the file path to the user when done.
