---
name: create-crud-form-type
description: >
  Create the Symfony form type for a CRUD (identifiable) entity's add/edit form.
  Covers standard field types, translatable fields, money fields, file uploads,
  and choice providers. For multi-tab layout with NavigationTabType, see
  create-form-tab-layout. For settings/configuration forms, see create-settings-form.
  Trigger: "create CRUD form type for {Domain}".
needs: [create-cqrs-commands, create-cqrs-queries]
produces: "{Domain}Type.php + choice providers — Symfony form structure for CRUD add/edit"
subagent: optional
---

# create-crud-form-type

> **Scope:** this skill builds the FormType for a **CRUD (identifiable) form** — an entity with an ID, a grid listing, and an `Add`/`Edit` CQRS command. For a **settings form** (options block, `ps_configuration` rows), use [`create-settings-form`](../create-settings-form/SKILL.md) instead. Settings FormTypes are flat, have no `getParent()`, no `_id` field, and no entity binding.

Read `@.ai/Component/Forms/CONTEXT.md` (decision tree, shared concerns) and `@.ai/Component/Forms/CRUD.md` (base FormBuilder/FormHandler factories, hooks, anti-pattern) for the conventions this skill builds on.

## 1. Root form type

Create `src/PrestaShopBundle/Form/Admin/{Section}/{Domain}/{Domain}Type.php`:

- Extend `TranslatorAwareType` (provides `$this->trans()`) or `AbstractType` for simple forms
- `buildForm()`: add all fields for the entity
- `configureOptions()`: set defaults as needed
- Form types define structure and validation only — no knowledge of commands/queries

**Reference:** `src/PrestaShopBundle/Form/Admin/Improve/International/Tax/TaxType.php` (simple), `src/PrestaShopBundle/Form/Admin/Sell/Catalog/Manufacturer/ManufacturerType.php` (with image)

## 2. Standard field types

> The table below is a starter, not the full catalogue. Before picking a Symfony native type, **scan `PrestaShopBundle\Form\Admin\Type\` for a PrestaShop-specific equivalent** — there are 80+ purpose-built types (`SwitchType`, `IpAddressType`, `ColorPickerType`, `CountryChoiceType`, `CurrencyMoneyType`, `EmailType`, `MaterialChoiceTreeType`, etc.). And before inventing a new option on a field, **scan `PrestaShopBundle\Form\Extension\`** for an existing extension that already provides it (`help`, `hint`, `external_link`, `modify_all_shops`, `autocomplete`, `disabling_switch`, …).

| PS field concept | Symfony/PS type | Notes |
|---|---|---|
| Text | `TextType` | Standard input |
| Boolean toggle | `SwitchType` (PS-specific) | On/off switch |
| Select with static options | `ChoiceType` | Inline choices array |
| Select with dynamic options | `ChoiceType` + `ChoiceProvider` | See section 5 |
| Textarea / HTML | `TextareaType` or `FormattedTextareaType` | |

## 3. Translatable fields

For multilingual fields (entity has `_lang` table):

```php
->add('name', TranslatableType::class, [
    'type' => TextType::class,
    'options' => ['constraints' => [new NotBlank()]],
])
```

- `TranslatableType` renders one input per active shop language
- Submitted data: `['name' => [1 => 'English', 2 => 'French']]`
- Map to command's `setLocalizedNames()` setter in the DataHandler

For translatable textareas: wrap `TextareaType` or `FormattedTextareaType`.

## 4. Money / price fields

For monetary fields (see [Forms/CONTEXT.md](../../CONTEXT.md) for decimal scale convention):

- Static currency: `MoneyType::class` with `'currency' => $defaultCurrencyIsoCode`
- Multi-currency: PS-specific `AmountType` if available
- Use appropriate transformers to convert between form display and storage

## 5. Choice providers

For select fields with dynamic options from DB:

- Create `{Domain}{Field}ChoiceProvider.php` implementing `ChoiceProviderInterface`
- Inject repository or DBAL connection
- `getChoices(): array` — return `['Label' => value]` array
- Inject into the form type and pass as `choices` option

**Reference:** `src/Core/Form/ChoiceProvider/` (61+ existing providers)

## 6. File upload fields

For image/logo uploads (see [Forms/CONTEXT.md](../../CONTEXT.md) for file upload conventions):

- Add `FileType::class` with `'mapped' => false, 'required' => false`
- Add `File` constraint with allowed MIME types
- Display existing image in the edit template via custom Twig block

## Rules

Conventions (base classes, file uploads, choice providers, NavigationTabType) are in [Forms/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Add Symfony validation constraints directly on form fields. Use `NotBlank` / `Length` inline; for character-set / format validation prefer **`TypedRegex`** (with a reused or newly-added `TYPE_*`) over an inline `Regex` — never hard-code a raw pattern in the FormType. See the "Field validation" row in [Forms/CONTEXT.md](../../CONTEXT.md)
- For multi-tab layout, use the `create-form-tab-layout` skill instead
- If the page persists into `ps_configuration` (and not into an entity table), this is NOT a CRUD form — switch to [`create-settings-form`](../create-settings-form/SKILL.md)
