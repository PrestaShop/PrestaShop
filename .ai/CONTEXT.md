# PrestaShop — AI Context (Root)

> For folder structure and navigation, see [STRUCTURE.md](STRUCTURE.md).
> For cross-domain naming traps and identity gotchas, see [GOTCHAS.md](GOTCHAS.md).
> For multi-store scoping (ShopConstraint, AbstractMultistoreConfiguration), see [MULTISTORE.md](MULTISTORE.md).

## Project overview

PrestaShop is an open-source e-commerce platform built on Symfony. It follows a progressive migration from a legacy architecture (ObjectModel, legacy controllers) toward a modern Domain-Driven Design approach (CQRS, Symfony controllers, Doctrine).

## Architecture layers

| Layer | Location | Role |
|-------|----------|------|
| Core Domain | `src/Core/Domain/` | Business logic: Commands, Queries, Handlers, ValueObjects, Exceptions |
| Core Components | `src/Core/` (non-Domain) | Shared infrastructure: Grid, Form, Hook, Translation, etc. |
| Adapter | `src/Adapter/` | Bridges between Core and legacy code or external systems |
| PrestaShopBundle | `src/PrestaShopBundle/` | Symfony bundle: controllers, form types, Twig extensions, DI config |
| Legacy | `classes/`, `controllers/` | Legacy ObjectModel classes and controllers — do not extend, migrate instead |
| Legacy BO theme | `admin-dev/themes/default/` | Legacy back-office theme for non-migrated pages |
| Admin front-end | `admin-dev/themes/new-theme/` | Back-office UI: Vue.js components, JavaScript, SCSS |
| Front-office themes | `themes/` | Customer-facing Smarty templates |
| Modules | `modules/` | Native and third-party modules |
| Tests | `tests/` | PHPUnit (unit/integration), Behat (behavior), Playwright (UI) |

## Coding standards

- Every PHP file: `declare(strict_types=1);`
- Classes `final` by default; all parameters, return types, and properties must be typed
- No ObjectModel in new code — use Doctrine entities or CQRS commands
- All services defined in YAML; no `new` in controllers
- No `Db::getInstance()` in new code — use Doctrine repositories
- No business logic in controllers — delegate to Handlers
- Catch specific domain exceptions, not generic `\Exception`
- Run `php vendor/bin/php-cs-fixer fix` to apply coding style (config: `.php-cs-fixer.dist.php`)
- Run `php vendor/bin/phpstan analyse` for static analysis (config: `phpstan.neon.dist`)

## CQRS pattern

- **Commands** — write intentions dispatched via `CommandBus`
- **Queries** — read intentions dispatched via `QueryBus`
- **Handlers** — implement logic; never call other handlers (compose at controller level)
- Handler interfaces in `src/Core/Domain/{Domain}/CommandHandler|QueryHandler/`
- Concrete implementations in `src/Adapter/{Domain}/CommandHandler|QueryHandler/`

## Testing

| Type | Framework | Location |
|------|-----------|----------|
| Unit | PHPUnit | `tests/Unit/` |
| Integration | PHPUnit | `tests/Integration/` |
| Behavior | Behat | `tests/Integration/Behaviour/` |
| UI | Playwright | `tests/UI/` |

## Skills

| Skill | Path | Trigger |
|-------|------|---------|
| `create-skill` | [skills/create-skill/SKILL.md](skills/create-skill/SKILL.md) | "create a skill for …" |
| `domain-context-generator` | [skills/domain-context-generator/SKILL.md](skills/domain-context-generator/SKILL.md) | "generate context for [Domain]" |
| `component-context-generator` | [skills/component-context-generator/SKILL.md](skills/component-context-generator/SKILL.md) | "generate context for [Component]" |

## Domain contexts

All 58 domains under `src/Core/Domain/` have a context file at `Domain/{DomainName}/CONTEXT.md`. Read the relevant one before working in a domain.

Domains: Address, Alias, ApiClient, Attachment, AttributeGroup, Carrier, Cart, CartRule, CatalogPriceRule, Category, CmsPage, CmsPageCategory, Combination (code lives under `src/Core/Domain/Product/Combination/`), Configuration, Contact, Country, CreditSlip, Currency, Customer, CustomerMessage, CustomerService, Discount, Employee, Feature, Hook, ImageSettings, Language, MailTemplate, Manufacturer, Meta, Module, Notification, Order, OrderMessage, OrderReturn, OrderReturnState, OrderState, Position, Product, Profile, Search, SearchEngine, Security, Shipment, Shop, ShowcaseCard, SqlManagement, State, Store, Supplier, Tab, Tag, Tax, TaxRulesGroup, Theme, Title, Webservice, Zone

## Component contexts

All 23 shared infrastructure components have a context file at `Component/{ComponentName}/CONTEXT.md`.

Components: BackOfficeHelp, Configuration, Console, Context, ContextStateManager, Cookie, CQRS, Database, Export, FacetedSearch, Forms, GlobalJS, Grid, Hook, Import, Link, Locale, MailTemplate, PositionUpdater, Router, Smarty, TinyMCE, Twig

## Generated indexes

Pre-built snapshots in `generated/` — useful when no PHP runtime is available (web-based assistants, CI contexts). Regenerate with `bash bin/generate-ai-index.sh`.

When PHP is available, prefer the authoritative live sources:
- CQRS commands/queries: `./bin/console prestashop:list:commands-and-queries`
- Routes: `./bin/console debug:router`
- Hooks: `app/Resources/hooks/hook.xml`
- Entities: read Doctrine entity files directly under `src/PrestaShopBundle/Entity/`

| File | Contents | When to use |
|------|----------|-------------|
| [generated/cqrs.md](generated/cqrs.md) | All Commands + Queries grouped by domain | Before adding a Command/Query — check it doesn't already exist |
| [generated/routes.md](generated/routes.md) | Symfony admin/API routes | Before adding a route or looking up a controller action |
| [generated/entities.md](generated/entities.md) | Doctrine entity columns and relations | Before writing a query or migration |
| [generated/hooks.md](generated/hooks.md) | Hook names discovered in source | Before dispatching or listening to a hook |
