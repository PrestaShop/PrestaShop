# Multi-store — Cross-cutting Guide

Multi-store affects forms, configuration reads/writes, repositories, and queries throughout almost every domain. Incorrect scoping is a common source of bugs.

## Core concept: ShopConstraint

`ShopConstraint` (in `src/Core/Domain/Shop/ValueObject/ShopConstraint.php`) defines the scope of an operation:

| Factory method | Scope |
|---------------|-------|
| `ShopConstraint::allShops()` | All shops — write applies everywhere |
| `ShopConstraint::shopGroup($id)` | All shops in one group |
| `ShopConstraint::shop($id)` | Single shop only |

Always pass `ShopConstraint` explicitly when a command or query is scope-sensitive. Never infer scope from the current context inside a handler.

## Configuration reads and writes

Use `src/Adapter/Configuration.php` (wraps `Configuration` ObjectModel):

```php
// Read — always pass ShopConstraint
$value = $this->configuration->get('MY_KEY', null, $shopConstraint);

// Write — always pass ShopConstraint
$this->configuration->set('MY_KEY', $value, $shopConstraint);
```

**Never** call `Configuration::get()` / `Configuration::updateValue()` static methods in new code — they ignore shop scope.

## Settings pages: AbstractMultistoreConfiguration

For back-office settings forms that must support per-shop overrides, extend `src/Core/Configuration/AbstractMultistoreConfiguration.php`. It:
- Automatically handles the "use shop default" checkbox rendered per field
- Reads and writes using `ShopConstraint` derived from the current admin context
- Requires implementing `getConfiguration()` and `updateConfiguration(array $data)`

See `src/Adapter/Configuration/` for concrete examples.

## Repositories

`AbstractMultiShopObjectModelRepository` extends `AbstractObjectModelRepository` with `ShopConstraintTrait`. Methods like `getObjectModel()` and `updateObjectModel()` accept an optional `ShopConstraint`. Use it when the entity can have per-shop overrides.

## Commands and Queries

- Commands that modify per-shop data must accept a `ShopConstraint` parameter
- Inject `ShopContext` (from `src/Core/Context/ShopContext.php`) to get the current shop's constraint: `$shopContext->getShopConstraint()`
- Handlers must never read `Context::getContext()->shop` directly — use the injected `ShopContext`

## Non-obvious patterns

- `ShopConstraint::allShops()` write does **not** override existing per-shop values — it only sets the "all shops" default. Per-shop overrides take precedence at read time.
- Deleting a per-shop override (reverting to the all-shops value) requires calling `configuration->delete($key, $shopConstraint)`, not setting it to null.
- Grid filters are not multi-store aware by default — scope filtering must be added manually to the Doctrine query builder if needed.

## Related

- [Configuration Component](Component/Configuration/CONTEXT.md) — `AbstractMultistoreConfiguration`, `Configuration` adapter
- [Context Component](Component/Context/CONTEXT.md) — `ShopContext::getShopConstraint()`
- [Database Component](Component/Database/CONTEXT.md) — `AbstractMultiShopObjectModelRepository`
- [Shop Domain](Domain/Shop/CONTEXT.md) — `ShopConstraint` value object lives here
