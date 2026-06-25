# Twig Component

## Purpose

Back-office templating infrastructure: Twig extensions that expose PrestaShop-specific functions and filters (hooks, localization, admin links, grid columns, translations) to templates. Does not define page templates — those live in `src/PrestaShopBundle/Resources/views/Admin/`. Front-office uses Smarty, not Twig.

## Layers

| Layer | Path |
|-------|------|
| Twig extensions (12+) | `src/PrestaShopBundle/Twig/Extension/`, `src/PrestaShopBundle/Twig/` |

## Non-obvious patterns

- `LayoutExtension` implements `GlobalsInterface` — its variables (`theme`, `default_currency`, `root_url`, `js_translatable`, `rtl_suffix`) are available in **every** back-office template without injection
- `HookExtension` is the only bridge from Twig to `HookDispatcherInterface` — every `{% renderhook 'hookName' %}` call goes through it; never call the dispatcher directly from a template
- `GridExtension` renders column content by column type — it is called from `@PrestaShop/Admin/Common/Grid/Columns/` partials, not from feature templates directly

## Canonical examples

- `src/PrestaShopBundle/Twig/HookExtension.php`
- `src/PrestaShopBundle/Twig/Extension/GridExtension.php`
- `src/PrestaShopBundle/Twig/LayoutExtension.php`

## Admin template conventions

- **Base layout:** all admin page templates extend `@PrestaShop/Admin/layout.html.twig` — never copy HTML structure
- **Template location:** `src/PrestaShopBundle/Resources/views/Admin/{Section}/{Domain}/` (e.g. `Tax/`, `Manufacturer/`)
- **Toolbar buttons:** override `{% block page_header_toolbar %}` to add "Add new" button linking to `path('admin_{domain}s_create')`
- **Flash messages:** handled by the layout automatically — no need to include them manually in most cases. Types: `success`, `error`, `warning`, `info`. Always use translatable strings with PS domains (`Admin.Notifications.Success`, etc.)
- **Grid rendering:** use `{% include '@PrestaShop/Admin/Common/Grid/grid_panel.html.twig' with {grid: grid} %}` — custom column rendering only needed for non-standard columns
- **Form rendering:** always use a single `{{ form_widget(form) }}` to render the entire form — never split into multiple `form_widget` calls for individual fields. This is critical because modules hook into the form builder to add extra fields, and a single `form_widget(form)` ensures those fields are rendered automatically. When specific parts need custom display, create a dedicated `FormType` and customize its rendering via the global PS UI kit form theme (for shared generic types) or a dedicated form theme (for very specific types). When using `NavigationTabType`, `form_widget(form)` handles tab rendering automatically — no manual tab HTML needed
- **File uploads:** forms with file fields must have `enctype="multipart/form-data"` on the `<form>` tag
- **Routes in templates:** always use `path('admin_{domain}s_create')` — never hardcode URLs
- **Page titles:** must be translatable via `trans()` filter or function
- **Form themes:** scoped via `{% form_theme form 'path/to/_widgets.html.twig' %}` — no global side effects. Use only when a field needs custom rendering beyond Symfony defaults (e.g. image preview next to upload field). Block naming: `{% block _{field_id}_widget %}` for field-level overrides
- **JS assets:** enqueue via `{% block javascripts %}{{ parent() }}{{ encore_entry_script_tags('entry_name') }}{% endblock %}` — the entry name must match the webpack config key

## Skills

| Skill | Trigger |
|-------|---------|
| [`create-twig-index-template`](skills/create-twig-index-template/SKILL.md) | "create index template for {Domain}" |
| [`create-twig-form-template`](skills/create-twig-form-template/SKILL.md) | "create form template for {Domain}" |

## Related

- [Locale Component](../Locale/CONTEXT.md) — `LocalizationExtension` uses `LocaleRepository`
- [Smarty Component](../Smarty/CONTEXT.md) — front-office counterpart (coexist during migration)
