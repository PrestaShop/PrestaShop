# Forms Component

## Purpose

Infrastructure for building, populating, and handling back-office forms tied to identifiable entities: data providers, data handlers, command builders, and choice providers. Does not contain Symfony form type definitions — those live in `src/PrestaShopBundle/Form/Admin/`.

## Layers

| Layer | Path |
|-------|------|
| Core contracts (configuration/settings forms) | `src/Core/Form/FormHandlerInterface.php`, `FormDataProviderInterface.php` |
| IdentifiableObject sub-layer (entity forms) | `src/Core/Form/IdentifiableObject/` |
| CommandBuilder sub-layer | `src/Core/Form/IdentifiableObject/CommandBuilder/` |
| Choice providers (Core, 61+) | `src/Core/Form/ChoiceProvider/` |
| Choice providers (Adapter, 26) | `src/Adapter/Form/ChoiceProvider/` |
| Symfony form types | `src/PrestaShopBundle/Form/Admin/` |
| Form extensions | `src/PrestaShopBundle/Form/Extension/` — add custom options (e.g. `external_link`, `modify_all_shops`) to all form types globally |
| Form utilities | `src/PrestaShopBundle/Form/FormBuilderModifier.php`, `FormCloner.php`, `FormHelper.php` — tools for modifying and cloning form builders at runtime |

## Non-obvious patterns

- Two distinct patterns coexist: configuration/settings `FormHandlerInterface` (settings pages, no `CommandBus`) and modern `IdentifiableObject` layer (entity forms, dispatches CQRS commands)
- Settings pages typically have a pair: one `FormDataProviderInterface` (reads/writes configuration values) + one `FormHandlerInterface` (builds the form and delegates save to the provider). The final `FormHandler` wrapper orchestrates them — you implement the interfaces, not the wrapper.
- `CommandBuilder` bridges raw form `array` → typed CQRS commands; Product domain has 16 builders, Combination has 6 — one per form section, not one per entity
- `FormDataHandlerInterface` has two methods: `create(array $data)` and `update($id, array $data)` — both return the entity ID
- `FormOptionsProviderInterface` supplies **dynamic** form options (carriers, tax rules) evaluated at render time, distinct from static choice providers
- Form extensions in `src/PrestaShopBundle/Form/Extension/` add custom options to all form types globally — check existing extensions before adding new form options

## Canonical examples

- `src/Core/Form/IdentifiableObject/DataHandler/TaxFormDataHandler.php` — simple entity form data handler (typical use case)
- `src/Core/Form/IdentifiableObject/DataHandler/ProductFormDataHandler.php` — complex handler delegating to CommandBuilders (advanced use case)
- `src/PrestaShopBundle/Form/Admin/Configure/AdvancedParameters/Security/FormDataProvider.php` — settings page data provider

## Related

- [CQRS Component](../CQRS/CONTEXT.md) — `FormDataHandler` implementations dispatch commands via `CommandBus`
- [Grid Component](../Grid/CONTEXT.md) — filter forms for grids use `FormChoiceProviderInterface`
- [Product Domain](../../Domain/Product/CONTEXT.md) — heaviest consumer; 16 CommandBuilders + dedicated DataHandler/DataProvider/OptionsProvider
