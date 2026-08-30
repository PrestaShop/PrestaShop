# ContextStateManager Component

> **Status:** Draft — this context file is a starting point and should be refined by domain experts.

## Purpose

Stack-based save/restore utility for the legacy `Context` singleton. Allows CQRS handlers to temporarily swap context state (currency, language, shop, cart, customer, etc.) for legacy code that reads from `Context::getContext()`, then restore it when done. Does not replace the modern Context component — it wraps the legacy one during the migration period.

## Layers

| Layer | Path |
|-------|------|
| Concrete class | `src/Adapter/ContextStateManager.php` — no interface exists |
| Service registration | `src/PrestaShopBundle/Resources/config/services/adapter/common.yml` |

## Non-obvious patterns

- **Stack-based nesting**: `saveCurrentContext()` pushes a new layer; `restorePreviousContext()` pops it. This allows nested handlers to each modify and restore context independently without corrupting each other's state.
- **Lazy tracking**: The context fields stack is `null` until the first setter is called. Only fields actually modified are tracked — unmodified fields are not saved or restored.
- **Shop context is special**: Both the `Shop` object and `Shop` static fields must be saved/restored together. `AdminController::$currentIndex` is also managed.
- **Language/locale sync**: Setting a language automatically synchronizes the translator locale; restoring the language also restores the locale.
- Heavily used by **Order** (16 handlers) and **Cart** (6 handlers) domains — typically to set cart-specific currency/language/customer/shop before legacy price calculations.

## Canonical examples

- `src/Adapter/ContextStateManager.php` — the full component (single class)
- `src/Adapter/Order/AbstractOrderHandler.php` — `setCartContext()` / `setOrderContext()` helper methods showing the save/modify/restore pattern

## Related

- [Context Component](../Context/CONTEXT.md) — the modern replacement; ContextStateManager wraps the legacy version
