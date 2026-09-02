---
name: create-twig-index-template
description: >
  Create the Twig template for the entity listing (grid) page. Includes grid panel,
  toolbar buttons, flash messages, and optional custom column rendering. Trigger:
  "create index template for {Domain}".
needs: [create-grid-definition, create-controller-listing, create-admin-routing]
produces: "index.html.twig — back-office listing page template"
---

# create-twig-index-template

Read `@.ai/Component/Twig/CONTEXT.md` for template conventions (layout, flash messages, routes, form themes).

## 1. Index template

Create `src/PrestaShopBundle/Resources/views/Admin/{Section}/{Domain}/index.html.twig`:

```twig
{% extends '@PrestaShop/Admin/layout.html.twig' %}

{% block content %}
  {% include '@PrestaShop/Admin/Common/Grid/grid_panel.html.twig' with {grid: grid} %}
{% endblock %}

{% block page_header_toolbar %}
  <div class="toolbar-icons">
    <a href="{{ path('admin_{domain}s_create') }}" class="btn btn-primary">
      {{ 'Add new'|trans({}, 'Admin.Actions') }}
    </a>
  </div>
{% endblock %}
```

The `grid` variable is passed from the controller's `indexAction` via the grid presenter.

**Reference:** `src/PrestaShopBundle/Resources/views/Admin/Improve/International/Tax/index.html.twig` (simple), `src/PrestaShopBundle/Resources/views/Admin/Sell/Catalog/Manufacturer/index.html.twig` (two grids)

## 2. Custom grid column rendering (only if needed)

Most grids work with default column rendering. Only add custom blocks when a column needs domain-specific HTML:

- Override via `{% block column_{column_id}_content %}` in the grid panel include
- Common case: image thumbnail, custom badge, formatted compound value
- Leave default columns alone — only override what's necessary

## Rules

Conventions (layout extension, `path()` for routes, flash messages auto-handling, toolbar buttons) are in [Twig/CONTEXT.md](../../CONTEXT.md). Skill-specific reminder:

- The `grid` variable name must match what the controller passes to `render()`
