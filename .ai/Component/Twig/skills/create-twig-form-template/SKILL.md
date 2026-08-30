---
name: create-twig-form-template
description: >
  Create the Twig template for a form page. Covers CRUD add/edit templates (one
  form per page) and settings-form block templates (multiple forms per page,
  each with an explicit action URL). Renders the Symfony form, save buttons,
  and optional form theme overrides for custom field rendering. Trigger:
  "create form template for {Domain}".
needs: [create-crud-form-type, create-settings-form, create-controller-form-actions, create-admin-routing]
produces: "form.html.twig (CRUD) or block template (settings) — Symfony form rendering"
subagent: optional
---

# create-twig-form-template

Read `@.ai/Component/Twig/CONTEXT.md` for template conventions (layout, flash messages, routes, form themes). When the template renders a specific form pattern, load only the relevant Forms sub-context:
- CRUD template → `@.ai/Component/Forms/CRUD.md`
- Settings block → `@.ai/Component/Forms/SETTINGS.md`

> **Two patterns:** CRUD forms render one form per page on a dedicated `form.html.twig`. Settings forms render one or more option blocks on the listing page (or a dedicated config page) — each block is its own `form_start`/`form_end` with an explicit `action` URL. See section 4 below for the settings differences.

## 1. Form template

Create `src/PrestaShopBundle/Resources/views/Admin/{Section}/{Domain}/form.html.twig`:

```twig
{% extends '@PrestaShop/Admin/layout.html.twig' %}

{% block content %}
  {{ form_start(form) }}
    {{ form_widget(form) }}
    <button type="submit" class="btn btn-primary">
      {{ 'Save'|trans({}, 'Admin.Actions') }}
    </button>
  {{ form_end(form) }}
{% endblock %}
```

- **Always use a single `form_widget(form)`** — see [Twig/CONTEXT.md](../../CONTEXT.md) for why (module hook compatibility)
- The same template typically serves both create and edit — the controller passes different form data
- For file uploads, add `enctype="multipart/form-data"` (see CONTEXT.md)

**Reference:** `src/PrestaShopBundle/Resources/views/Admin/Improve/International/Tax/` (simple), `src/PrestaShopBundle/Resources/views/Admin/Sell/Catalog/Manufacturer/` (with image)

## 2. Form theme overrides (only if needed)

When specific fields need custom rendering beyond Symfony defaults:

- **Preferred:** set the `form_theme` option directly on the form (PrestaShop custom option) — usually in the controller when building the form, or in the form type's `configureOptions()`. Symfony picks up the theme automatically; no `{% form_theme %}` directive needed in the Twig file.
- **Fallback:** use the Twig directive when the override is template-specific:
  `{% form_theme form 'Admin/{Section}/{Domain}/{domain}_theme.html.twig' %}`
- Override the field block: `{% block _{field_id}_widget %}...{% endblock %}`
- Common case: image preview next to upload field with a "Remove" checkbox

Form themes are scoped to this form only — no global side effects.

## 3. JS asset inclusion

If the form needs JavaScript (for `initComponents` or Vue):

```twig
{% block javascripts %}
  {{ parent() }}
  <script src="{{ asset('themes/new-theme/public/{domain}.bundle.js') }}"></script>
{% endblock %}
```

The asset path must match the webpack entry name.

## 4. Settings form block templates

Settings forms are different. The template usually lives alongside the index page (or as an included Twig block) and renders **one form per option block**. Multiple blocks per page are common (the *Advanced > Performance* page has six).

```twig
{{ form_start(optionsForm, {action: path('admin_{page}_save_options'), attr: {class: 'form', id: '{name}_form'}}) }}
  <div class="card">
    <h3 class="card-header"><i class="material-icons">settings</i>{{ '{Page} options'|trans }}</h3>
    <div class="card-body">
      <div class="form-wrapper">
        {{ form_widget(optionsForm) }}
      </div>
    </div>
    <div class="card-footer">
      <div class="d-flex justify-content-end">
        <button class="btn btn-primary">{{ 'Save'|trans({}, 'Admin.Actions') }}</button>
      </div>
    </div>
  </div>
{{ form_end(optionsForm) }}
```

Two differences from the CRUD pattern:

- **The `action` URL is explicit.** Settings forms POST to a dedicated save route (e.g. `admin_countries_save_options`) that is distinct from the page route. Without it, the form would POST to the listing route, which is not a save endpoint.
- **The submit button sits outside `form_widget`**, typically in the card footer. The single-`form_widget(form)` rule still applies (for module hook compatibility) — the button is a sibling, not a child of the form widget call.

For pages that mix a grid and one or more settings blocks (e.g. *Improve > International > Locations > Countries*), the index template renders the grid and each settings block as separate top-level elements; each block is its own `form_start`/`form_end`.

**Reference:** `src/PrestaShopBundle/Resources/views/Admin/Improve/International/Country/Blocks/options.html.twig` + `index.html.twig` (PR #41406).

## Rules

Conventions (layout, `path()`, single `form_widget(form)`, NavigationTabType auto-rendering, file upload enctype, form theme block naming, JS asset inclusion) are in [Twig/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Form theme overrides should be minimal — Symfony's default rendering handles most cases
- CSRF token is included automatically by `form_end(form)`
- Settings blocks must set an explicit `action: path('...')` — the page route is not the save route
