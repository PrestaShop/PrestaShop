# Currency Domain

## Purpose

Manages currencies available in the shop, including ISO codes, exchange rates, precision, and enabled/disabled status. Does NOT handle payment processing or price calculation — those are concerns of the Pricing and Order domains.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Currency/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Currency/` — handler implementations, `CurrencyDataProvider.php`, `CurrencyManager.php`, `Repository/CurrencyRepository.php` |
| Legacy ObjectModel | `classes/Currency.php` (1175 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/International/CurrencyController.php`, `src/Core/Grid/Definition/Factory/CurrencyGridDefinitionFactory.php` |

## Non-obvious patterns

- Currencies come in two flavours: official (CLDR-backed, `AddCurrencyCommand`) and unofficial (`AddUnofficialCurrencyCommand`), each with a corresponding edit command and abstract base. All four share `AbstractAddCurrencyCommand` / `AbstractEditCurrencyCommand`.
- `CurrencyManager` in the adapter handles exchange-rate refresh logic separately from the CQRS handlers.
- `ValueObject/CurrencyIdInterface.php` + `NoCurrencyId.php` follow the nullable-ID pattern used across the codebase for optional currency references.
- Both alpha ISO code (`AlphaIsoCode`) and numeric ISO code (`NumericIsoCode`) are distinct value objects — never use raw strings.

## Canonical examples

- `src/Core/Domain/Currency/Command/AddCurrencyCommand.php` + `src/Adapter/Currency/CommandHandler/AddCurrencyCommandHandler.php`

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Currency/` — Behat behavior scenarios
