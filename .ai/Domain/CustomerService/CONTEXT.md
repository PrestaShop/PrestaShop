# CustomerService Domain

## Purpose

Manages customer service threads (conversations between customers and the back-office team), including replies, forwarding, status transitions, and bulk deletion. Does NOT handle sending transactional order messages — use the CustomerMessage domain for that.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/CustomerService/` — Commands, Queries, QueryResults, ValueObjects, Exceptions, `Status/`, `Repository/` (interface) |
| Adapter | `src/Adapter/CustomerService/` — not present; handlers are wired directly via the legacy `CustomerThread` ObjectModel |
| Legacy ObjectModel | `classes/CustomerThread.php` (274 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Sell/CustomerService/CustomerThreadController.php`, `src/Core/Grid/Definition/Factory/CustomerThreadGridDefinitionFactory.php` |

## Non-obvious patterns

- The domain entity is called `CustomerThread` in the legacy layer and in most infrastructure code, but the domain itself is named `CustomerService`. Value objects such as `CustomerThreadId` and `CustomerThreadStatus` reflect the legacy naming.
- `Status/CustomerThreadStatusColor.php` maps thread status identifiers to their display colours — this is a presentation concern kept inside the domain.
- `CustomerThreadRepository` is defined at the Core domain level (not only in the adapter), meaning the interface lives in `src/Core/Domain/CustomerService/Repository/`.
- The controller namespace also contains `MerchandiseReturnController` and `OrderMessageController` — related UI lives in the same bundle directory but belongs to different domains.

## Canonical examples

- `src/Core/Domain/CustomerService/Command/ReplyToCustomerThreadCommand.php` + `src/Core/Domain/CustomerService/CommandHandler/ReplyToCustomerThreadHandler.php`
- `src/Core/Domain/CustomerService/Query/GetCustomerThreadForViewing.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- [CustomerMessage Domain](../CustomerMessage/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/CustomerService/` — Behat behavior scenarios
- [GOTCHAS.md](../../GOTCHAS.md) — CustomerService domain vs CustomerThread naming mismatch
