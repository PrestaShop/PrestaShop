# Zone Domain

## Purpose

Manages geographical zones (e.g. Europe, Americas) used to group countries for shipping, tax, and carrier range pricing. Does NOT manage countries or states directly — those reference ZoneId as a foreign key.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Zone/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Zone/` — handler implementations, `ZoneRepository` |
| Legacy ObjectModel | `classes/Zone.php` (94 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/International/ZoneController.php`, `src/Core/Grid/Definition/Factory/ZoneGridDefinitionFactory.php` |

## Non-obvious patterns

- `ZoneId` is a heavily shared foreign key: referenced by `Country`, `State` (including `BulkUpdateStateZoneCommand`), and `Carrier` (range pricing via `CarrierRangeZone`) — changes to a zone affect shipping cost calculation across those domains.

## Canonical examples

- `src/Core/Domain/Zone/Command/AddZoneCommand.php` + `src/Adapter/Zone/CommandHandler/AddZoneHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Zone/` — Behat behavior scenarios
