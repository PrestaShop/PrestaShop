# Settings forms — pattern detail

> Sub-context of [`Forms/CONTEXT.md`](CONTEXT.md). Read the root first for the decision tree and shared concerns; this file documents only what is settings-specific.

## Required layers

| Layer | Path | Role |
|---|---|---|
| DataConfiguration | `src/Adapter/{Domain}/{Name}Configuration.php` | extends `AbstractMultistoreConfiguration`. Reads/writes config rows. Implements `getConfiguration(): array`, `updateConfiguration(array): array` (returns array of errors), and protected `buildResolver(): OptionsResolver` for option validation. |
| FormDataProvider | `src/PrestaShopBundle/Form/Admin/{Section}/{Name}FormDataProvider.php` | implements `PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface`. Two-line bridge: `getData()` → `dataConfiguration->getConfiguration()`, `setData($data)` → `dataConfiguration->updateConfiguration($data)`. |
| FormType | `src/PrestaShopBundle/Form/Admin/{Section}/{Name}Type.php` | standard `AbstractType` / `TranslatorAwareType`. **This is the root form** — no `getParent()`, no wrapper, no entity binding. |

## Service definitions (4 YAML entries, no PHP handler class)

All four entries go under `src/PrestaShopBundle/Resources/config/services/bundle/form/` (or `adapter/` for the DataConfiguration). Never under `core/form/` — that folder is reserved for CRUD.

| Service | YAML file | Class |
|---|---|---|
| DataConfiguration | `services/adapter/data_configuration.yml` | your `{Name}Configuration` |
| FormDataProvider | `services/bundle/form/form_data_provider.yml` | your `{Name}FormDataProvider` |
| FormHandler | `services/bundle/form/form_handler.yml` | **`PrestaShop\PrestaShop\Core\Form\Handler`** (the base class — never your own) |
| FormType | `services/bundle/form/form_type.yml` | empty entry for auto-discovery |

The `Handler` service takes 6 ordered arguments: form factory, hook dispatcher, your FormDataProvider, FormType FQCN, **hook name** (PascalCase, e.g. `CountriesPageOptions`), **form name** (kebab-case, e.g. `country-options`). The hook name drives the two module-facing hooks below.

## Hooks dispatched by the base `Handler`

| Hook | When | Parameters | Use |
|---|---|---|---|
| `action{HookName}Form` | inside `Handler::getForm()` | `form_builder` (mutable reference) | modules add/remove/modify fields before render |
| `action{HookName}Save` | inside `Handler::save()` | `errors` and `form_data` (both passed by reference) | modules inject validation errors or rewrite data before persistence |

These hooks are how external modules extend back-office settings forms. Reimplementing `FormHandlerInterface` from scratch silently removes both hooks — see the anti-pattern below.

## Allowed exception — extending `Handler` for save-time side effects

A handful of pages need a side effect at save time that does not belong in `DataConfiguration` (cache invalidation, cross-field coercion): `ProductPreferencesFormHandler`, `PreferencesFormHandler`, `CustomerPreferencesFormHandler`, `TranslationsSettingsFormHandler`, `InvoiceByDateFormHandler`, `InvoiceByStatusFormHandler`, `ImportFormHandler`. Rule:

- The class must **`extends Handler`** — never reimplement `FormHandlerInterface` from scratch.
- Overridden `save()` must call **`parent::save()`** so the hook dispatch still fires.
- Cache-clearing or similar plumbing dependencies are wired via service `calls:` in the YAML.

## Anti-patterns (named symptoms)

**"Outer wrapper builder"** — symptom: a visible wrapper label, a `label => false` workaround on the only `add()` call, submitted data shaped like `['xxx' => [...real fields...]]`, the FormType ends up nested as a child instead of being the root form.

```php
// ❌ WRONG — this is what PR #41414 generated:
$this->formFactory->createBuilder()
    ->add('contact_details', ContactDetailsType::class, ['label' => false])
    ->setData(['contact_details' => $this->dataProvider->getData()])
    ->getForm();
```

The correct construction is `Handler::getForm()`'s one-liner (see `src/Core/Form/Handler.php:83`):

```php
$formBuilder = $this->formFactory->createNamedBuilder($this->formName, $this->form);
```

The FormType IS the root form — there is never an extra outer builder.

**"Custom `FormHandlerInterface` implementation"** — symptom: a file like `XxxFormHandler.php` implementing the interface from scratch, with its own `getForm()`/`save()` body. The module hooks `action{HookName}Form` (dispatched at `Handler.php:87`) and `action{HookName}Save` (dispatched at `Handler.php:107`) never fire. Modules silently lose the ability to extend the form. Fix: delete the class, register a `PrestaShop\PrestaShop\Core\Form\Handler` service in `form_handler.yml` instead.

## Canonical examples

| File | Use case |
|---|---|
| `src/Adapter/Country/CountryOptionsConfiguration.php` | minimal `AbstractMultistoreConfiguration` extension |
| `src/PrestaShopBundle/Form/Admin/Improve/International/Locations/CountryOptionsFormDataProvider.php` | minimal `FormDataProviderInterface` bridge |
| `src/PrestaShopBundle/Form/Admin/Configure/ShopParameters/General/MaintenanceType.php` | typical settings FormType with several fields |
| `src/Core/Form/Handler.php` | source-of-truth for the hook-dispatch contract |

PR #41406 — "Migrate country options" — wires all four service entries together and is the textbook example to read end-to-end.
