# CRUD forms — pattern detail

> Sub-context of [`Forms/CONTEXT.md`](CONTEXT.md). Read the root first for the decision tree and shared concerns; this file documents only what is CRUD-specific.

CRUD form = a.k.a. *identifiable* form. Entity add/edit page backed by CQRS commands.

## Required layers

| Layer | Path | Role |
|---|---|---|
| FormDataProvider | `src/Core/Form/IdentifiableObject/DataProvider/{Domain}FormDataProvider.php` | `getData($id): array` dispatches `Get{Domain}ForEditing`; `getDefaultData(): array` returns create-form defaults |
| FormDataHandler | `src/Core/Form/IdentifiableObject/DataHandler/{Domain}FormDataHandler.php` | `create(array $data): mixed` dispatches `Add{Domain}Command`; `update($id, array $data): void` dispatches `Edit{Domain}Command` |
| FormType | `src/PrestaShopBundle/Form/Admin/{Section}/{Domain}/{Domain}Type.php` | fields, validation constraints. No CQRS knowledge. |
| Base FormBuilder | `PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilder` | **factory-built service — never subclassed**. Controller calls `getForm()` / `getFormFor($id)`. |
| Base FormHandler | `PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandler` | **factory-built service — never subclassed**. Controller calls `handle($form)` / `handleFor($id, $form)`. |

## Service definitions

All entries go under `src/PrestaShopBundle/Resources/config/services/core/form/`. Never under `bundle/form/` — that folder is reserved for settings forms.

| Service | YAML file |
|---|---|
| FormType | `services/core/form/form_type.yml` (or `bundle/form/form_type.yml`) |
| FormDataProvider | `services/core/form/form_data_provider.yml` |
| FormDataHandler | `services/core/form/form_data_handler.yml` |
| FormBuilder | `services/core/form/form_builder.yml` (uses `prestashop.core.form.builder.form_builder_factory` factory) |
| FormHandler | `services/core/form/form_handler.yml` (uses `prestashop.core.form.identifiable_object.handler.form_handler_factory` factory) |

Service-ID convention: `prestashop.core.form.identifiable_object.{builder|handler|data_provider|data_handler}.{domain}_form_{role}`.

## Hooks dispatched by the IdentifiableObject `FormHandler`

- `actionBeforeCreate{FormName}FormHandler`
- `actionAfterCreate{FormName}FormHandler`
- `actionBeforeUpdate{FormName}FormHandler`
- `actionAfterUpdate{FormName}FormHandler`
- plus the `FormBuilderModifier` extension point for in-flight builder mutation by modules

## Anti-pattern

**"Custom FormBuilder or FormHandler class"** — symptom: a hand-rolled class extending the IdentifiableObject base. There is no legitimate use case; the factory-built services support every pattern needed (file uploads, multilingual, tabs, sub-resource commands). If a hand-rolled class appears in a PR, it is a sign the AI invented one — delete it and use the factory services.

## Canonical examples

| File | Use case |
|---|---|
| `src/Core/Form/IdentifiableObject/DataHandler/TaxFormDataHandler.php` | simple DataHandler |
| `src/Core/Form/IdentifiableObject/DataHandler/ProductFormDataHandler.php` | complex DataHandler with `CommandBuilder` delegation |
| `src/PrestaShopBundle/Form/Admin/Improve/International/Tax/TaxType.php` | simple FormType |
| `src/PrestaShopBundle/Form/Admin/Sell/Catalog/Manufacturer/ManufacturerType.php` | FormType with image upload |
