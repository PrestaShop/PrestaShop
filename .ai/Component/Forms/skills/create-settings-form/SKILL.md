---
name: create-settings-form
description: >
  Create a PrestaShop settings form (options block writing to ps_configuration):
  DataConfiguration + FormDataProvider + FormType + 4 YAML service entries
  (base Handler reused, never subclassed). For CRUD entity forms, use
  create-crud-form-type instead. Trigger: "create settings form for {Page}",
  "add options block for {Page}", "migrate fields_options for {Page}".
needs: []
produces: "DataConfiguration + FormDataProvider + FormType + 4 YAML service entries — settings form ready to wire into a controller action"
subagent: optional
---

# create-settings-form

Read `@.ai/Component/Forms/CONTEXT.md` (decision tree, shared concerns) and `@.ai/Component/Forms/SETTINGS.md` (base `Handler`, hooks, anti-patterns, allowed exception) first. This skill assumes those conventions; it does not restate them.

If the page is not a settings form per CONTEXT.md's decision section, stop and use [`create-crud-form-type`](../create-crud-form-type/SKILL.md) + [`create-crud-form-data-handling`](../create-crud-form-data-handling/SKILL.md) instead.

## 1. DataConfiguration

Create `src/Adapter/{Domain}/{Name}Configuration.php`. Extend `AbstractMultistoreConfiguration`. Three responsibilities:

- `getConfiguration(): array` — read each `ps_configuration` row via `$this->configuration->get(...)`, return them keyed by the form field name. Cast types here (`(bool)`, `(int)`).
- `updateConfiguration(array $configuration): array` — call `$this->validateConfiguration($configuration)` first; on success, call `$this->updateConfigurationValue('PS_KEY', 'form_field_name', $configuration, $shopConstraint)` per field. Return `[]` on success or a list of error messages.
- protected `buildResolver(): OptionsResolver` — declare every form field with `setDefined()` and `setAllowedTypes()` for option validation.

Use `$this->getShopConstraint()` (provided by `AbstractMultistoreConfiguration`) for the multi-store scope.

**Reference:** `src/Adapter/Country/CountryOptionsConfiguration.php`.

## 2. FormDataProvider

Create `src/PrestaShopBundle/Form/Admin/{Section}/{Name}FormDataProvider.php`. Implements `PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface`. The whole file is a two-method bridge:

```php
public function getData()
{
    return $this->dataConfiguration->getConfiguration();
}

public function setData(array $data)
{
    return $this->dataConfiguration->updateConfiguration($data);
}
```

Inject the DataConfiguration via constructor. **Never** add other dependencies (no `Db`, no repository, no command bus). If you need side effects at save time, the side effects belong in `DataConfiguration::updateConfiguration()`, not here.

**Reference:** `src/PrestaShopBundle/Form/Admin/Improve/International/Locations/CountryOptionsFormDataProvider.php`.

## 3. FormType

Create `src/PrestaShopBundle/Form/Admin/{Section}/{Name}Type.php`. Standard `AbstractType` (or `TranslatorAwareType` for `$this->trans()`).

- **The FormType IS the root form.** No `getParent()`, no nested `add('xxx', ChildType::class)` wrapper to host the real fields. Add fields directly in `buildForm()`.
- Field keys must match the array keys returned by `DataConfiguration::getConfiguration()`.
- For multi-store fields, set `'multistore_configuration_key' => 'PS_FOO_BAR'` on the field options — PrestaShop's form extension renders the per-shop override checkbox automatically.
- Before picking a Symfony native type, **scan `PrestaShopBundle\Form\Admin\Type\` for a PrestaShop-specific equivalent** (`SwitchType`, `IpAddressType`, `ColorPickerType`, `CountryChoiceType`, etc. — 80+ types). Before inventing a new field option, **scan `PrestaShopBundle\Form\Extension\`** for an existing extension (`help`, `hint`, `external_link`, `modify_all_shops`, `autocomplete`, …).

**Reference:** `src/PrestaShopBundle/Form/Admin/Improve/International/Locations/CountryOptionsType.php`, `src/PrestaShopBundle/Form/Admin/Configure/ShopParameters/General/MaintenanceType.php`.

## 4. Service definitions (4 YAML entries)

Add one entry to each of these YAML files. See [Forms/SETTINGS.md](../../SETTINGS.md) for the service-definitions table and the `bundle/form/` vs `core/form/` folder rule — settings services go under `bundle/form/`.

`src/PrestaShopBundle/Resources/config/services/adapter/data_configuration.yml`:
```yaml
prestashop.adapter.{domain}.{name}_configuration:
  class: 'PrestaShop\PrestaShop\Adapter\{Domain}\{Name}Configuration'
  arguments:
    - '@prestashop.adapter.legacy.configuration'
    - '@prestashop.adapter.shop.context'
    - '@prestashop.adapter.multistore_feature'
```

`src/PrestaShopBundle/Resources/config/services/bundle/form/form_data_provider.yml`:
```yaml
prestashop.admin.{domain}.{name}.data_provider:
  class: 'PrestaShopBundle\Form\Admin\{Section}\{Name}FormDataProvider'
  arguments:
    - '@prestashop.adapter.{domain}.{name}_configuration'
```

`src/PrestaShopBundle/Resources/config/services/bundle/form/form_handler.yml`:
```yaml
prestashop.admin.{domain}.{name}.form_handler:
  class: 'PrestaShop\PrestaShop\Core\Form\Handler'
  arguments:
    - '@form.factory'
    - '@prestashop.core.hook.dispatcher'
    - '@prestashop.admin.{domain}.{name}.data_provider'
    - 'PrestaShopBundle\Form\Admin\{Section}\{Name}Type'
    - '{HookName}'          # PascalCase, e.g. CountriesPageOptions — drives action{HookName}Form / action{HookName}Save
    - '{form-name}'         # kebab-case form name, e.g. country-options
```

The `class` line **must** be `PrestaShop\PrestaShop\Core\Form\Handler` — the base class. See [Forms/SETTINGS.md](../../SETTINGS.md) for why custom handler classes break the hook contract.

`src/PrestaShopBundle/Resources/config/services/bundle/form/form_type.yml` — empty entry for auto-discovery:
```yaml
PrestaShopBundle\Form\Admin\{Section}\{Name}Type:
```

**Reference:** PR #41406 (country options block) wires all four files together.

## 5. Next steps

This skill stops at the YAML entries. To finish the page:

- Controller action — invoke [`create-controller-form-actions`](../../../Controller/skills/create-controller-form-actions/SKILL.md) (section "Settings form action").
- Save route — invoke [`create-admin-routing`](../../../Controller/skills/create-admin-routing/SKILL.md) to wire the POST endpoint that the controller's save action handles.
- Twig block — invoke [`create-twig-form-template`](../../../Twig/skills/create-twig-form-template/SKILL.md) (settings block section) to render the form on the page.

## Verification

- `php bin/console debug:container prestashop.admin.{domain}.{name}.form_handler` returns a service whose class is `PrestaShop\PrestaShop\Core\Form\Handler` (not your own).
- Once the controller and template are wired (see "Next steps"), the page renders the form; submit persists into `ps_configuration`; refresh shows the persisted value.
- Hook listeners on `action{HookName}Form` and `action{HookName}Save` fire when registered by a test module.
