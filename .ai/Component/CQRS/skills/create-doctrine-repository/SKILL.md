---
name: create-doctrine-repository
description: >
  Create the repository in the Adapter layer. This is the ONLY class that touches
  the database — all handlers delegate to it. Covers multistore patterns with
  ShopConstraint when the entity requires it. Trigger: "create repository for {Domain}".
needs: [create-cqrs-commands]
produces: "{Domain}Repository.php — the single persistence entry point for the domain"
subagent: optional
---

# create-doctrine-repository

See [CQRS/CONTEXT.md](../../CONTEXT.md#repository) for conventions (stateless, no Context dependency, multistore tier model, typed exceptions).

## 1. Choose the base class

Create `src/Adapter/{Domain}/{Domain}Repository.php`. Choose the base class based on the multistore tier table in CONTEXT.md.

## 2. Core methods

Implement these methods (adapt naming to your domain):

- `get{Domain}({Domain}Id $id): {LegacyObjectModel}` — loads the ObjectModel, throws `{Domain}NotFoundException` if not found
- `create(...): {Domain}Id` — calls ObjectModel `save()` or `add()`, wraps in try/catch, returns new ID
- `update(...)` — calls ObjectModel `save()` or `update()`
- `delete({Domain}Id $id)` — deletes the entity, respects multistore if applicable

**Reference:** `src/Adapter/Tax/CommandHandler/` uses a simple repository pattern, `src/Adapter/Carrier/CommandHandler/` uses `AbstractMultiShopObjectModelRepository`

## 3. Multistore pattern (when using AbstractMultiShopObjectModelRepository)

- Receive `ShopConstraint` as a method parameter — never read it from Context
- Call `$shopIds = $this->getShopIdsByConstraint($shopConstraint)` at the start of every write
- Iterate `$shopIds` and apply the write for each shop context
- Modes: `ShopConstraint::allShops()`, `ShopConstraint::shop($shopId)`, `ShopConstraint::shopGroup($groupId)`
- Single-shop installs still go through this path (returns array with one ID)

## 4. Sub-resource methods (if applicable)

For entities with has-many sub-resources, two strategies:

### Atomic replace (simpler)
- `set{SubResource}s({Domain}Id, array $items)` — delete all existing rows, insert new set
- Wrap in transaction — partial replace corrupts data

### Incremental update (cleaner)
- Compare existing rows with new collection, apply only changes
- Preferred when sub-resources have their own identity

## Rules

Conventions (stateless, no Context, typed exceptions) are in [CQRS/CONTEXT.md](../../CONTEXT.md#repository). Skill-specific reminders:

- Never use `Db::getInstance()` — use Doctrine DBAL or ObjectModel methods
- Never hard-code `Context::getContext()->shop->id` — receive shop as parameter
- The repository **only persists** — no business validation (uniqueness, required-by-config, related-record existence). That belongs in the `{Domain}Validator` service the handler calls (see [CQRS/CONTEXT.md](../../CONTEXT.md) → Handlers → Validation placement). The only checks here are existence guards via the base helper `assertObjectModelExists` (add a dedicated `assert{Related}Exists` rather than fetching-and-discarding)
