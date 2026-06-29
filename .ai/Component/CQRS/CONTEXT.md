# CQRS Component

## Purpose

Command Bus and Query Bus infrastructure (built on Symfony Messenger) that decouples controllers from business logic. Does not define any business commands or queries — those live in each domain's `Command/` and `Query/` directories.

## Layers

| Layer | Path |
|-------|------|
| Bus interface | `src/Core/CommandBus/CommandBusInterface.php` |
| Handler attributes + compiler pass | `src/PrestaShopBundle/DependencyInjection/Compiler/CommandAndQueryRegisterPass.php` |
| Domain interfaces (per domain) | `src/Core/Domain/{Domain}/CommandHandler/` + `QueryHandler/` |
| Concrete handlers (per domain) | `src/Adapter/{Domain}/CommandHandler/` + `QueryHandler/` |
| Repositories (per domain) | `src/Adapter/{Domain}/{Domain}Repository.php` |

## Non-obvious patterns

- Both buses share the same `CommandBusInterface` — differentiated only by the message type passed, not separate interfaces
- `#[AsCommandHandler]` / `#[AsQueryHandler]` attributes on adapter classes auto-register via the compiler pass; it infers the handled message type from the handler method's first parameter type hint
- Grid query builders are **not** dispatched through the bus — they query the DB directly
- `ExecutedCommandRegistry` tracks all dispatched commands with backtraces in debug mode

## Conventions

### Scalar inputs, VO getters (critical rule)

**Command and Query constructor parameters must always be scalar types** (`int`, `string`, `bool`, `array`) to facilitate serialization. Getters can (and should) return Value Objects built from those scalars.

Accepted exceptions to the scalar-only input rule:
- `ShopConstraint` — for multistore scoping
- `DecimalNumber` — ALWAYS use instead of native `float` (which carries imprecision)
- `DateTimeInterface` / `DateTimeImmutable` — for date/time values

### Commands

- Commands are **data objects** — no business logic, no DB calls
- **Add command:** constructor takes all required fields as scalar parameters. Returns `{Domain}Id` from the handler
- **Edit command (partial-update):** constructor takes **only** the entity ID. All editable fields are nullable with fluent setters (`setName(string $name): self`). In the handler, check `$command->getField() !== null` before updating — null means "not set in this request", not "set to null in DB"
- **Delete command:** single `int $id` parameter. Existence/constraint checks happen in the handler
- **Sub-resource commands:** has-many relations get their **own** command (e.g. `Set{Domain}{SubResource}sCommand`), never merged into Edit

### Queries and result DTOs

- Queries are read-only data objects — no side effects
- **QueryResult DTOs use only scalar types** — `int`, `string`, `bool`, `array` (for multilingual, keyed by lang ID). No Value Objects in QueryResult — the data is already validated
- Most list queries are handled by the grid QueryBuilder — don't create unnecessary query classes. Only create `Get{Domain}sForListing` when the domain explicitly uses one

### Exception hierarchy

Each domain creates its own exception tree in `src/Core/Domain/{Domain}/Exception/`:
- **Base:** `{Domain}Exception extends \RuntimeException` with integer constants per error code
- **Not found:** `{Domain}NotFoundException extends {Domain}Exception`
- **Per-action:** `CannotAdd{Domain}Exception`, `CannotUpdate{Domain}Exception`, `CannotDelete{Domain}Exception`
- **Constraints:** `{Domain}ConstraintException` with `const INVALID_NAME = 1`, `const INVALID_ID = 2`, etc.

Error code constants must be unique integers within the class. Never throw generic `\Exception` from domain code.

### Handlers

- Use `#[AsCommandHandler]` / `#[AsQueryHandler]` attributes — no manual service tags. Combined with `autoconfigure: true`, Symfony Messenger picks them up automatically
- Handlers contain **business orchestration only** — no direct SQL, no raw `Db::getInstance()`. All DB access goes through the repository
- **Never call another handler** from within a handler — compose at controller level
- **Add handler** returns `{Domain}Id`; all other command handlers return `void`
- Query handlers always return a typed DTO — never void, never ObjectModel instances
- **Validation placement** — single-value format validation belongs in the field's Value Object constructor (a `{Domain}ConstraintException`). Cross-field / entity-level business rules (uniqueness, required-fields-by-config, existence of related records) belong in a dedicated **`{Domain}Validator` service** (in `src/Adapter/{Domain}/`) that the handler calls — **not inline in the repository, the controller, or the FormType**. The repository only persists. See `src/Adapter/Country/Validate/CountryValidator.php`
- **Handler is the single integration point** — cross-cutting/derived logic (auto-filling defaults, normalizing values, side effects) belongs in the command handler, **not** in the FormType or controller. Putting it in the handler means the Admin API, AJAX endpoints, Behat and programmatic callers all get the same behavior for free; logic that only lives in the form is silently bypassed everywhere else
- **Edit commands are partial updates** — an `Edit{Domain}Command` carries only the fields the caller intends to change (often nullable setters). A handler must apply only what the command actually provides and preserve untouched fields (e.g. don't overwrite all language values when only one was submitted). The command is the source of truth for *what changed*, not for the full entity state

### Bulk handlers

- **All bulk handlers extend `AbstractBulkCommandHandler`** — this is not optional
- Always continue after individual failure — never abort mid-batch
- Report ALL failed IDs via `BulkCommandExceptionInterface`, not just the first one
- Command takes `array $ids` (`int[]`) and optionally a target state (e.g. `bool $expectedStatus`)

### Repository

- **Repositories must be stateless** — no instance state between calls
- **Never depend on Context services** — receive all contextual values (shop, language, etc.) as method parameters. The caller consults the Context and passes values to the repository
- Throw typed domain exceptions (`{Domain}NotFoundException`, `CannotAdd{Domain}Exception`), not generic exceptions
- Base class depends on multistore tier:

| Multistore tier | Base class | When to use |
|---|---|---|
| Tier 1 — no shop relation | `AbstractObjectModelRepository` | Entity has no shop association |
| Tier 2 — simple shop association | `AbstractObjectModelRepository` or `AbstractMultiShopObjectModelRepository` | Content is same across shops, linked to a list of shops |
| Tier 3 — per-shop content | `AbstractMultiShopObjectModelRepository` | Fields change by shop (e.g. Product, Category) |

### Value Objects

- Identity VO (`{Domain}Id`): constructor takes `int $value`, validates `> 0`, throws `{Domain}Exception`
- Additional VOs only for fields with non-trivial validation (enums, URLs, constrained numbers) — not every field needs a VO
- For has-many relations, typed collections implementing `\Countable` and `\IteratorAggregate`

## Canonical examples

- `src/Core/CommandBus/CommandBusInterface.php`
- `src/Adapter/Hook/CommandHandler/UpdateHookStatusCommandHandler.php`
- `src/PrestaShopBundle/Controller/Admin/PrestaShopAdminController.php` — `dispatchCommand()` / `dispatchQuery()` helpers
- `src/Core/Domain/Tax/` — simple domain (few commands, one VO, simple exceptions)
- `src/Core/Domain/Carrier/` — complex domain (many commands, sub-resources, multistore)

## Skills

| Skill | Trigger |
|-------|---------|
| [`create-cqrs-commands`](skills/create-cqrs-commands/SKILL.md) | "create CQRS commands for {Domain}" |
| [`create-cqrs-queries`](skills/create-cqrs-queries/SKILL.md) | "create queries for {Domain}" |
| [`create-cqrs-bulk-commands`](skills/create-cqrs-bulk-commands/SKILL.md) | "create bulk commands for {Domain}" |
| [`implement-cqrs-handlers`](skills/implement-cqrs-handlers/SKILL.md) | "implement handlers for {Domain}" |
| [`create-doctrine-repository`](skills/create-doctrine-repository/SKILL.md) | "create repository for {Domain}" |
| [`register-cqrs-services`](skills/register-cqrs-services/SKILL.md) | "register CQRS services for {Domain}" |

