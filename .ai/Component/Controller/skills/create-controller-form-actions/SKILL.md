---
name: create-controller-form-actions
description: >
  Create form page actions in the admin controller. Covers both patterns: CRUD
  (create/edit via FormBuilder + FormHandler pair) and settings (index + save
  via the base FormHandler). Never builds commands directly. Trigger: "create
  form actions for {Domain}", "create add/edit for {Domain}", "create settings
  save action for {Page}".
needs: [create-cqrs-commands, create-cqrs-queries, create-crud-form-type, create-crud-form-data-handling, create-settings-form]
produces: "createAction, editAction, and entity-specific actions in {Domain}Controller.php (CRUD); index/save actions for settings blocks"
---

# create-controller-form-actions

Read `@.ai/Component/Controller/CONTEXT.md` for controller conventions (base class, DI, security attributes, error mapping).
Read `@.ai/Component/Forms/CONTEXT.md` for the settings-vs-CRUD decision tree. Then, depending on which branch this controller serves, load only the relevant pattern detail:
- CRUD action → `@.ai/Component/Forms/CRUD.md` (FormBuilder + FormHandler pair, IdentifiableObject base classes)
- Settings action → `@.ai/Component/Forms/SETTINGS.md` (base `Handler`, hooks, allowed exception)

> **Two patterns:** CRUD entity forms inject the `FormBuilder` + IdentifiableObject `FormHandler` pair (sections 1–3 below). Settings forms inject the base `Handler` once (section 4 below). For the full settings-form stack (DataConfiguration + FormDataProvider + FormType + 4 YAML entries), the umbrella [`create-settings-form`](../../../Forms/skills/create-settings-form/SKILL.md) skill covers everything.

## 1. Create action

`FormBuilderInterface` and `FormHandlerInterface` are injected as **action arguments** (preferred DI mode — see `Controller/CONTEXT.md`), each with an explicit `#[Autowire(service: '…')]` so the action picks up the domain-specific form services:

```php
#[AdminSecurity("is_granted('create', request.get('_legacy_controller'))")]
public function createAction(
    Request $request,
    #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.{domain}_form_builder')]
    FormBuilderInterface ${domain}FormBuilder,
    #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.{domain}_form_handler')]
    FormHandlerInterface ${domain}FormHandler,
): Response
```

- GET: build form via `FormBuilderInterface::getForm()`, render form template
- POST: handle request via `FormHandlerInterface::handle()` — it internally calls `FormDataHandler::create()` which dispatches the Add command
- Never instantiate commands directly in the controller
- On success: flash message + redirect to index or edit page (return the new entity ID via `getIdentifiableObjectId()`)
- On failure: re-render form with errors

**Reference:** `TaxController::createAction()`, `ManufacturerController::createAction()`

## 2. Edit action

Same injection mode — `FormBuilderInterface` and `FormHandlerInterface` as action arguments:

```php
#[AdminSecurity("is_granted('update', request.get('_legacy_controller'))")]
public function editAction(
    Request $request,
    int ${domain}Id,
    #[Autowire(service: 'prestashop.core.form.identifiable_object.builder.{domain}_form_builder')]
    FormBuilderInterface ${domain}FormBuilder,
    #[Autowire(service: 'prestashop.core.form.identifiable_object.handler.{domain}_form_handler')]
    FormHandlerInterface ${domain}FormHandler,
): Response
```

- GET: `${domain}FormBuilder->getFormFor(${domain}Id)` — `FormDataProvider::getData($id)` is called internally to pre-fill the form
- POST: `${domain}FormHandler->handle($form)` — calls `FormDataHandler::update()` which dispatches Edit command
- Catch `{Domain}NotFoundException` → error flash, redirect to index (entity may have been deleted concurrently)

**Reference:** `TaxController::editAction()`, `ManufacturerController::editAction()`

## 3. Entity-specific actions

Some entities require additional actions beyond create/edit:

- **Image deletion:** `deleteCoverImageAction(int $id)`
- **Export:** `exportAction(Request $request)` — generates CSV/PDF
- **View page:** `viewAction(int $id)` — read-only detail page (e.g. Customer, Order)
- **Position update:** `updatePositionAction(Request $request)` — handled by `PositionDefinition`

For these single-purpose actions, **skip FormBuilder/FormHandler** — they exist to bridge a Symfony form lifecycle to a CQRS command, which is overkill for an action that just dispatches one command or query. Inject the bus (or specific handler) and dispatch directly:

```php
#[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))")]
public function deleteCoverImageAction(int ${domain}Id): RedirectResponse
{
    try {
        $this->dispatchCommand(new DeleteCoverImageCommand(${domain}Id));
        $this->addFlash('success', /* … */);
    } catch ({Domain}NotFoundException $e) {
        $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
    }

    return $this->redirectToRoute('admin_{domain}s_edit', ['{domain}Id' => ${domain}Id]);
}
```

The FormBuilder/FormHandler pair is required for the create/edit form lifecycle — not for every controller action.

## 4. Settings form action

For a **settings form** (options block writing into `ps_configuration`), the controller injects the base `Handler` once and calls `getForm()` / `save()` directly. There is no FormBuilder; there is no FormDataHandler. Both interfaces share the short name `FormHandlerInterface`, so use an `as` alias to disambiguate:

```php
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface as ConfigurationFormHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface; // unaliased — CRUD on this page
```

Index action — render the form alongside the rest of the page:

```php
#[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
public function indexAction(
    Request $request,
    #[Autowire(service: 'prestashop.admin.{domain}.{name}.form_handler')]
    ConfigurationFormHandlerInterface $optionsFormHandler,
): Response {
    return $this->render('@PrestaShop/Admin/.../index.html.twig', [
        'optionsForm' => $optionsFormHandler->getForm()->createView(),
        // … grid, toolbar buttons, etc.
    ]);
}
```

Save action — POST handler:

```php
#[DemoRestricted(redirectRoute: 'admin_{page}_index')]
#[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_{page}_index')]
public function saveOptionsAction(
    Request $request,
    #[Autowire(service: 'prestashop.admin.{domain}.{name}.form_handler')]
    ConfigurationFormHandlerInterface $optionsFormHandler,
): Response {
    $form = $optionsFormHandler->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $errors = $optionsFormHandler->save($form->getData());

        if (0 === count($errors)) {
            $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_{page}_index');
        }

        $this->addFlashErrors($errors);
    }

    return $this->redirectToRoute('admin_{page}_index');
}
```

Key differences from CRUD form actions:

- Single handler injection (not a FormBuilder + FormHandler pair).
- `$formHandler->save($form->getData())` returns an **array of error messages** (empty = success) — no `Result` object, no `getIdentifiableObjectId()`.
- Index action passes the form view alongside other page data (grid, etc.); save action only redirects.
- Reference: `CountryController::indexAction()` and `CountryController::saveOptionsAction()` (PR #41406).

## Rules

- See `@.ai/Component/Controller/CONTEXT.md` for all controller conventions (DI order, security attributes, error mapping)
- Create/edit go through FormBuilder/FormHandler — never build the create/edit commands manually
- Single-purpose actions (toggle, delete sub-resource, export, view) dispatch commands/queries directly
- FormDataProvider transforms query results into form-ready data, not the controller
- Catch NotFoundException on edit — the entity may have been deleted between page load and save
