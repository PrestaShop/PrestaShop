# PrestaShop — AI Context (Root)

> This is the root AI context file for the PrestaShop open-source project.
> For details on how this `.ai/` folder is organized, see [STRUCTURE.md](STRUCTURE.md).

## Project overview

PrestaShop is an open-source e-commerce platform built on Symfony. It follows a progressive migration from a legacy architecture (ObjectModel, legacy controllers) toward a modern Domain-Driven Design approach (CQRS, Symfony controllers, Doctrine).

## Architecture layers

| Layer | Location | Role |
|-------|----------|------|
| **Core Domain** | `src/Core/Domain/` | Business logic: Commands, Queries, Handlers, ValueObjects, Exceptions |
| **Core Components** | `src/Core/` (non-Domain) | Shared infrastructure: Grid, Form, Hook, Translation, etc. |
| **Adapter** | `src/Adapter/` | Bridges between Core and legacy code or external systems |
| **PrestaShopBundle** | `src/PrestaShopBundle/` | Symfony bundle: controllers, form types, Twig extensions, DI config |
| **Legacy** | `classes/`, `controllers/` | Legacy ObjectModel classes and controllers — do not extend, migrate instead |
| **Admin front-end** | `admin-dev/themes/new-theme/` | Back-office UI: Vue.js components, JavaScript, SCSS |
| **Front-office themes** | `themes/` | Customer-facing Smarty templates |
| **Modules** | `modules/` | Native and third-party modules |
| **Tests** | `tests/` | PHPUnit (unit/integration), Behat (behavior), Playwright (UI) |

## General coding standards

- **Strict types** — every PHP file must declare `declare(strict_types=1);`
- **Final classes by default** — use `final` unless inheritance is explicitly needed
- **Type declarations** — all parameters, return types, and properties must be typed
- **No ObjectModel in new code** — use Doctrine entities or CQRS commands instead
- **Symfony services** — all services must be defined in YAML config, no `new` in controllers
- **English only** — all code, comments, and documentation in English

## CQRS pattern

New back-office features must follow the CQRS pattern:
- **Commands** — represent write intentions (`AddProductCommand`, `UpdateCartRuleCommand`)
- **Queries** — represent read intentions (`GetProductForEditing`, `SearchCustomers`)
- **Handlers** — execute the business logic (`AddProductCommandHandler`)
- Commands and Queries are dispatched via the `CommandBus` / `QueryBus`
- Handlers must not call other handlers — compose at the controller level

## Do (project-wide)

- Follow existing patterns in the domain you're working on
- Use ValueObjects for domain identifiers (e.g., `ProductId`, `CartId`)
- Throw domain-specific exceptions (e.g., `ProductNotFoundException`)
- Write unit tests for Handlers, integration tests for Commands/Queries
- Use Behat for behavior scenarios that cross multiple domains

## Don't (project-wide)

- Don't instantiate services manually — use dependency injection
- Don't add business logic in controllers — delegate to Handlers
- Don't use legacy `Db::getInstance()` in new code — use Doctrine repositories
- Don't catch generic `\Exception` — catch specific domain exceptions
- Don't modify legacy classes unless fixing a bug — new features go in `src/`

## Testing expectations

| Type | Framework | Location | When to use |
|------|-----------|----------|-------------|
| Unit | PHPUnit | `tests/Unit/` | Handlers, ValueObjects, domain logic |
| Integration | PHPUnit | `tests/Integration/` | Commands, Queries, Doctrine repositories |
| Behavior | Behat | `tests/Integration/Behaviour/` | Domain scenarios, multi-step workflows |
| UI | Playwright | `tests/UI/` | End-to-end back-office and front-office flows |

## Domain contexts

These files contain conventions and rules specific to each business domain.

| Domain | Path | Status |
|--------|------|--------|
| Cart | [.ai/Domain/Cart/CONTEXT.md](Domain/Cart/CONTEXT.md) | Draft |
| Product | [.ai/Domain/Product/CONTEXT.md](Domain/Product/CONTEXT.md) | Draft |

> More domains will be added progressively. To add a new one, see [STRUCTURE.md](STRUCTURE.md#adding-context-for-a-new-domain).

## Component contexts

These files contain conventions and usage patterns for shared infrastructure components.

| Component | Path | Status |
|-----------|------|--------|
| Back Office Help | [.ai/Component/BackOfficeHelp/CONTEXT.md](Component/BackOfficeHelp/CONTEXT.md) | To be defined |
| Configuration | [.ai/Component/Configuration/CONTEXT.md](Component/Configuration/CONTEXT.md) | To be defined |
| Console | [.ai/Component/Console/CONTEXT.md](Component/Console/CONTEXT.md) | To be defined |
| Context | [.ai/Component/Context/CONTEXT.md](Component/Context/CONTEXT.md) | To be defined |
| Cookie | [.ai/Component/Cookie/CONTEXT.md](Component/Cookie/CONTEXT.md) | To be defined |
| CQRS | [.ai/Component/CQRS/CONTEXT.md](Component/CQRS/CONTEXT.md) | To be defined |
| Database | [.ai/Component/Database/CONTEXT.md](Component/Database/CONTEXT.md) | To be defined |
| Export | [.ai/Component/Export/CONTEXT.md](Component/Export/CONTEXT.md) | To be defined |
| Faceted Search | [.ai/Component/FacetedSearch/CONTEXT.md](Component/FacetedSearch/CONTEXT.md) | To be defined |
| Forms | [.ai/Component/Forms/CONTEXT.md](Component/Forms/CONTEXT.md) | To be defined |
| Global JS | [.ai/Component/GlobalJS/CONTEXT.md](Component/GlobalJS/CONTEXT.md) | To be defined |
| Grid | [.ai/Component/Grid/CONTEXT.md](Component/Grid/CONTEXT.md) | To be defined |
| Hook | [.ai/Component/Hook/CONTEXT.md](Component/Hook/CONTEXT.md) | To be defined |
| Import | [.ai/Component/Import/CONTEXT.md](Component/Import/CONTEXT.md) | To be defined |
| Link | [.ai/Component/Link/CONTEXT.md](Component/Link/CONTEXT.md) | To be defined |
| Locale | [.ai/Component/Locale/CONTEXT.md](Component/Locale/CONTEXT.md) | To be defined |
| Mail Template | [.ai/Component/MailTemplate/CONTEXT.md](Component/MailTemplate/CONTEXT.md) | To be defined |
| Position Updater | [.ai/Component/PositionUpdater/CONTEXT.md](Component/PositionUpdater/CONTEXT.md) | To be defined |
| Router | [.ai/Component/Router/CONTEXT.md](Component/Router/CONTEXT.md) | To be defined |
| Smarty | [.ai/Component/Smarty/CONTEXT.md](Component/Smarty/CONTEXT.md) | To be defined |
| TinyMCE | [.ai/Component/TinyMCE/CONTEXT.md](Component/TinyMCE/CONTEXT.md) | To be defined |
| Twig | [.ai/Component/Twig/CONTEXT.md](Component/Twig/CONTEXT.md) | To be defined |

> To add a new component, see [STRUCTURE.md](STRUCTURE.md#adding-context-for-a-new-component).
