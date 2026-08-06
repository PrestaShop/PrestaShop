# Country Domain

## Purpose

Manages country records including creation, editing, deletion, and zip code format validation. Does not manage states/regions — those are handled by the `State` domain.

## Layers

| Layer | Path |
|-------|------|
| Core CQRS | `src/Core/Domain/Country/` — Commands, Queries, QueryResults, ValueObjects, Exceptions |
| Adapter | `src/Adapter/Country/` — handler implementations, repository, validator, data providers |
| Legacy ObjectModel | `classes/Country.php` (504 lines) — do not add logic here |
| Back-office UI | `src/PrestaShopBundle/Controller/Admin/Improve/International/CountryController.php`, `src/Core/Grid/Definition/Factory/CountryGridDefinitionFactory.php` |

## Non-obvious patterns

- `ZipCodePatternResolverInterface` and its implementation live at the domain root level (not under `ValueObject/`); they convert country-specific zip code format strings into regex patterns usable for validation.
- `CountryId` has a companion `NoCountryId` value object and a `CountryIdInterface` — the interface accommodates contexts where a country may not be set (e.g., optional country fields).
- `src/Adapter/Country/Validate/CountryValidator.php` is a standalone validator consumed by handlers — not a Symfony constraint.
- `src/Adapter/Country/` includes `CountryRequiredFieldsProvider` and `CountryZipCodeRequirementsProvider`, which drive address form field visibility based on per-country configuration.

## Canonical examples

- `src/Core/Domain/Country/Command/AddCountryCommand.php` + `src/Adapter/Country/CommandHandler/` (AddCountryHandler)

## Related

- [CQRS Component](../../Component/CQRS/CONTEXT.md)
- [Forms Component](../../Component/Forms/CONTEXT.md)
- [Grid Component](../../Component/Grid/CONTEXT.md)
- `tests/Integration/Behaviour/Features/Scenario/Country/` — Behat behavior scenarios
