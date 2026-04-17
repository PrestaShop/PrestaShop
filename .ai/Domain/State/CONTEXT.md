# State Domain

## Purpose

Manages geographic states/regions (e.g. US states, Canadian provinces) used in address forms, linked to countries and zones. It does not manage order states — those belong to the `OrderState` domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/State/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/State/` — handler implementations, repository, `CountryStateByIsoCodeProvider` |
| Legacy ObjectModel | `classes/State.php` (250 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/International/StateController.php` |

## Non-obvious patterns

- Two controllers exist: `International/StateController.php` (geographic states) and `Configure/ShopParameters/OrderStateController.php` (order states) — they are entirely separate domains despite similar names.
- `ValueObject/NoStateId.php` and `StateIdInterface.php` allow nullable state references in address commands where a state is optional (e.g. countries without states).
- `CountryStateByIsoCodeProvider` in the adapter wraps the legacy `State::getIdByIso()` static call for use in import/address scenarios.
- `StateSettings.php` at the domain root defines validation constants (max name length, ISO code pattern) used by form types.

## Canonical examples

- `src/Core/Domain/State/Command/AddStateCommand.php` + `src/Adapter/State/CommandHandler/AddStateHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/State/` — Behat behavior scenarios
