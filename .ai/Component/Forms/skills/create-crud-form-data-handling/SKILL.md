---
name: create-crud-form-data-handling
description: >
  Create the CRUD form data flow layer: DataProvider (loads entity data for edit
  form), DataHandler (dispatches commands on create/update), error handling, and
  service registration. This bridges the form layer with the CQRS layer. For
  settings/configuration forms (a single FormDataProvider over ps_configuration,
  no DataHandler), use create-settings-form. Trigger: "create CRUD form data
  handling for {Domain}".
needs: [create-cqrs-commands, create-cqrs-queries, create-crud-form-type]
produces: "{Domain}FormDataProvider + {Domain}FormDataHandler + DI registration"
subagent: optional
---

# create-crud-form-data-handling

> **Scope:** this skill is for **CRUD (identifiable) forms** — entity add/edit pages backed by CQRS commands. Settings forms do NOT have a DataHandler — they use a single `FormDataProvider` over a `DataConfiguration`. Use [`create-settings-form`](../create-settings-form/SKILL.md) for that pattern.

Read `@.ai/Component/Forms/CONTEXT.md` (decision tree, shared concerns) and `@.ai/Component/Forms/CRUD.md` (IdentifiableObject pattern, service folders, hooks) for the conventions this skill builds on.

## 1. DataProvider

Create `src/Core/Form/IdentifiableObject/DataProvider/{Domain}FormDataProvider.php` implementing `FormDataProviderInterface`:

- `getData(int $id): array` — dispatch `Get{Domain}ForEditing` query via query bus, map the result DTO to the form's expected array structure
- `getDefaultData(): array` — return sensible defaults for the create form (empty strings, null IDs, `active => true`)
- Both methods must return the same array structure — the form type cannot distinguish create from edit
- Multilingual fields: return arrays keyed by integer language ID

**Reference:** `src/Core/Form/IdentifiableObject/DataProvider/TaxFormDataProvider.php` (simple)

## 2. DataHandler

Create `src/Core/Form/IdentifiableObject/DataHandler/{Domain}FormDataHandler.php` implementing `FormDataHandlerInterface`:

- `create(array $data): mixed` — build `Add{Domain}Command` from `$data`, dispatch via command bus, return new entity ID
- `update(int $id, array $data): void` — build `Edit{Domain}Command($id)`, call fluent setters for each field from `$data`, dispatch
- Map form array keys to command setters: `$command->setName($data['name'])`
- Multilingual: `$command->setLocalizedNames($data['name'])` where value is lang-keyed array
- Sub-resource commands dispatched separately (see [Forms/CONTEXT.md](../../CONTEXT.md) for dispatch order)

**Reference:** `src/Core/Form/IdentifiableObject/DataHandler/TaxFormDataHandler.php` (simple)

## 3. Error handling

- When `!$form->isValid()`, re-render the form — Twig displays errors automatically via `{{ form_errors(field) }}`
- Error handling conventions (server-side validation as source of truth, tab error nav) are in [Forms/CONTEXT.md](../../CONTEXT.md)

## 4. Service registration

Register in the appropriate DI YAML file:

- `{Domain}Type` — tagged with `form.type` (usually auto-discovered)
- `{Domain}FormDataProvider` — with `autowire: true`, `autoconfigure: true`
- `{Domain}FormDataHandler` — with `autowire: true`, `autoconfigure: true`
- Service ID naming convention is in [Forms/CONTEXT.md](../../CONTEXT.md)
- Verify: `php bin/console debug:container | grep {domain}_form`

## Rules

Conventions (DataProvider/DataHandler roles, service registration, IdentifiableObject pattern) are in [Forms/CONTEXT.md](../../CONTEXT.md). Skill-specific reminder:

- All three services (type, provider, handler) must be registered before wiring the controller
