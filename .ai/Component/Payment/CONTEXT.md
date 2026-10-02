# Payment Component

## Purpose

Everything the core provides so that a payment module can offer itself at checkout and turn a validated
cart into an order: the `PaymentOption` value object modules return from the payment hooks, the finder
that collects those options, the legacy `PaymentModule` base class whose `validateOrder()` creates the
order, and the back-office screens that decide which module is offered to whom. It implements no payment
method of its own - every actual method is a module - and it does not own the order lifecycle after the
order exists.

## Layers

| Layer | Path |
|-------|------|
| Payment option value object + form decorator | `src/Core/Payment/PaymentOption.php`, `src/Core/Payment/PaymentOptionFormDecorator.php` |
| Back-office preferences configuration | `src/Core/Payment/PaymentModulePreferencesConfiguration.php` |
| Checkout collection and rendering of options | `classes/checkout/PaymentOptionsFinder.php`, `classes/checkout/CheckoutPaymentStep.php` |
| Legacy payment module base — `validateOrder()` lives here | `classes/PaymentModule.php` |
| Free-order pseudo module | `classes/PaymentFree.php` |
| Payments recorded against an order | `classes/order/OrderPayment.php`, `src/Adapter/Order/CommandHandler/AddPaymentHandler.php` |
| Back-office controllers and forms | `src/PrestaShopBundle/Controller/Admin/Improve/Payment/`, `src/PrestaShopBundle/Form/Admin/Improve/Payment/` |
| Module lists and presentation | `src/Adapter/Module/PaymentModuleListProvider.php`, `src/Adapter/Presenter/Module/PaymentModulesPresenter.php` |

## Non-obvious patterns

- A payment option can arrive through **three** hooks and `PaymentOptionsFinder::find()` reads all of them,
  in order: `displayPaymentEU` (deprecated - each entry is passed through
  `PaymentOption::convertLegacyOption()`), `advancedPaymentOptions`, then `paymentOptions`. Only the last
  is type-checked, via `HookFinder::$expectedInstanceClasses`, which **throws** when a module returns
  something that is not a `PaymentOption`. The first two are trusted.
- `paymentOptions` is special-cased inside `Hook::getHookModuleExecList()`: its module list is **never
  cached**, and every other hook's query explicitly excludes it (`h.name != "paymentOptions"`). On the
  front office that same query then filters payment modules by the context country, currency and customer
  group through `ps_module_country`, `ps_module_currency` and `ps_module_group`. A payment module can
  therefore be installed, active and correctly hooked and still appear nowhere, because of a missing row
  in one of those three tables.
- Those rows are written from two unrelated places: `PaymentModule::addCheckboxCurrencyRestrictionsForModule()`
  and its country/carrier siblings, called by the module at install time, and the back-office Payment
  preferences form. A payment module that installs without calling them restricts itself to nothing.
- `PaymentModule::validateOrder()` is the single entry point that turns a cart into an order, and it is
  roughly 640 lines of `classes/PaymentModule.php` - it creates the order and its details, applies cart
  rules, decrements stock, sends the confirmation mail and dispatches the payment hooks. New work belongs
  in the Order domain's CQRS handlers; this method is called by modules and cannot change shape.
- A free order does not go through a module. `PaymentOptionsFinder::findFree()` synthesises a
  `PaymentOption` for `free_order`, and `classes/PaymentFree.php` is a `PaymentModule` subclass carrying
  nothing but that name and `active = true`.
- `PaymentOption` implements `HookContentClassInterface`, which is what lets a hook return an object
  rather than a rendered string; the decision of how it renders belongs to `PaymentOptionFormDecorator`.

## Canonical examples

- `src/Core/Payment/PaymentOption.php` — what a module builds and returns from `paymentOptions`
- `classes/checkout/PaymentOptionsFinder.php` — the three-hook collection and the free-order case
- `classes/PaymentModule.php` — the base class every payment module extends
- `src/Core/Payment/PaymentModulePreferencesConfiguration.php` — how the back office writes the restrictions

## Related

- [Hook Component](../Hook/CONTEXT.md) — `HookFinder`, and the `paymentOptions` special case described above
- [Order Domain](../../Domain/Order/CONTEXT.md) — owns everything after `validateOrder()` has run
- [Cart Domain](../../Domain/Cart/CONTEXT.md) — supplies the cart that `validateOrder()` consumes
- [Module Domain](../../Domain/Module/CONTEXT.md) — installation, and the module lists the back office shows
- [Forms Component](../Forms/CONTEXT.md) — the Payment preferences form types
