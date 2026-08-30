---
step: 6
title: "Form page (vertical slice)"
previous: step-05-listing-page.md
next: step-07-playwright-tests.md
deliverable: "A working add/edit form: form type + data handling + controller form actions + form routes + form template + form JS, all reachable via the feature flag"
---

# Step 6 — Form page (vertical slice)

This step produces a complete, demoable add/edit page in one go. Like the listing slice (step 5), every layer needed for the form — type, data flow, controller, routing, template, JS — is built and wired here.

Read `@.ai/Component/Forms/CONTEXT.md`, `@.ai/Component/Controller/CONTEXT.md`, `@.ai/Component/Twig/CONTEXT.md`, `@.ai/Component/Javascript/CONTEXT.md` for the conventions of each layer.

## Slice ordering reminder

If listing (step 5) ran first, the controller class and routing YAML already exist — this slice **extends** them with form actions and form routes. If this slice runs first (rare), it creates the controller class and routing YAML; step 5 then extends them later. Both invocations of `create-controller-*` and `create-admin-routing` are designed to support either case.

## Settings forms vs CRUD forms

Before picking skills, classify each form block on the page (a single admin page can carry both):

- **Settings form** (a.k.a. options block, `fields_options`): persists into `ps_configuration`. No entity ID. No CQRS Add/Edit command. Use the [`create-settings-form`](../../../Forms/skills/create-settings-form/SKILL.md) umbrella skill — it produces the DataConfiguration + FormDataProvider + FormType + service entries in one go. Do **not** invoke `create-crud-form-*` skills for this block.
- **CRUD form** (a.k.a. identifiable form): entity add/edit with a grid listing. Use the CRUD chain below.

Pages with **both** patterns (e.g. *Shop parameters > Contact > Stores* has a CRUD grid for store entities and a settings form for global contact details) need both flows — run them independently.

## Skills to invoke — CRUD form

In suggested order:

| Skill | Produces |
|---|---|
| `create-crud-form-type` | `{Domain}Type` extending `TranslatorAwareType`, with each field mapped from the manifest |
| `create-form-tab-layout` | `NavigationTabType`-based tab structure — **conditional, complex pages only**. Most pages do not use tabs. |
| `create-crud-form-data-handling` | `DataProvider` (loads `Editable{Domain}` for edit) + `DataHandler` (dispatches Add/Edit + sub-resource commands) + DI registration |
| `create-controller-form-actions` | `createAction`, `editAction` using `FormBuilder` + `FormHandler` injected as action arguments. Creates the controller class on first slice, extends it on second. |
| `create-admin-routing` | Form routes (`create`, `edit`, plus any custom action). Carries `_legacy_feature_flag: {domain}` on each route. Creates YAML on first slice, extends on second. |
| `create-twig-form-template` | `form.html.twig` and any form theme overrides |
| `create-ts-entry-point` | `js/pages/{domain}/form.ts` + webpack entry for the form |
| `init-js-components` | `initComponents()` calls for translatable inputs, choice trees, TinyMCE editors, etc. — driven by `data-*` attributes in the form template |
| `create-vue-component` | Vue SFC for sections that need rich interactivity beyond standard JS components — **exception only**. Most pages do not need Vue. |

## Skills to invoke — settings form

In suggested order:

| Skill | Produces |
|---|---|
| `create-settings-form` | DataConfiguration (extends `AbstractMultistoreConfiguration`) + FormDataProvider + FormType + 4 service YAML entries — the entire settings stack in one skill. **Never produces a custom `FormHandler` class**: the base `PrestaShop\PrestaShop\Core\Form\Handler` is registered as a service. |
| `create-controller-form-actions` | The settings-form action (index passes `$formHandler->getForm()->createView()` to the view, save action calls `$formHandler->save($form->getData())`). Uses the `use ... FormHandlerInterface as ConfigurationFormHandlerInterface;` aliasing trick to disambiguate from the IdentifiableObject handler. |
| `create-admin-routing` | The save route (POST to `admin_{page}_save_options`). |
| `create-twig-form-template` | The settings block template (one `form_start`/`form_end` per block, explicit `action: path('...')`, submit button outside `form_widget`). |

## Orchestration notes

- `FormBuilder` and `FormHandler` are injected as **action arguments** (Symfony argument resolver), not in the constructor. This is the preferred order across PrestaShop controllers; reserve `getSubscribedServices` for services shared across many actions, and constructor injection only when neither fits.
- The `DataProvider`/`DataHandler` pair is the standard for forms whose data round-trips through CQRS commands. For one-off actions that do not fit this pattern, dispatching a command directly from the controller is acceptable — the pattern is not mandatory for every action.
- Sub-resource commands are dispatched separately by the `DataHandler` after the main Add/Edit command — never inline them in `EditXxxCommand`.
- Form theme overrides may be declared either via `{% form_theme %}` in the Twig file or via the PrestaShop-specific `'form_theme'` form option (preferred today). Pick one location per form.
- Tab layout is the exception: only invoke `create-form-tab-layout` when the manifest's complexity decision called for it. The default form is single-column, no tabs.

## Gate to next step

- [ ] With flag enabled, the create page renders and saves
- [ ] With flag enabled, the edit page renders, prefills, and saves
- [ ] Validation errors map to translatable flash messages
- [ ] Sub-resource updates (if any) persist through the appropriate sub-resource commands
- [ ] File uploads (if any) work and survive a re-edit
- [ ] With flag disabled, the legacy form still loads (no regression)
