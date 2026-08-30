# TinyMCE Component

## Purpose

Integrates the TinyMCE rich-text editor into back-office forms: a Symfony form type (`FormattedTextareaType`) and a JavaScript initialization layer. Does not provide content sanitization — that is the `CleanHtml` Symfony constraint applied on the form field separately.

## Layers

| Layer | Path |
|-------|------|
| Symfony form type | `src/PrestaShopBundle/Form/Admin/Type/FormattedTextareaType.php` |
| JS class (ES6, configures plugins/toolbar) | `admin-dev/themes/new-theme/js/components/tinymce-editor.js` |
| DOM-ready auto-initializer | `js/admin/tinymce_loader.js` |
| Legacy entry point | `js/admin/tinymce.inc.js` |

## Non-obvious patterns

- `autoload: true` option on `FormattedTextareaType` adds the `autoload_rte` CSS class — `tinymce_loader.js` picks it up automatically; no per-form JS setup needed
- UTF-8 character limits mirror MySQL column types: `TEXT=21844` (default), `MEDIUMTEXT=5592415`, `LONGTEXT=1431655765` — the `limit` option must match the DB column
- `TranslatableType` wrapping `FormattedTextareaType` produces **one TinyMCE instance per language tab** — each tab is an independent editor

## Canonical examples

- `src/PrestaShopBundle/Form/Admin/Type/FormattedTextareaType.php`
- `admin-dev/themes/new-theme/js/components/tinymce-editor.js`

## Related

- [Forms Component](../Forms/CONTEXT.md) — `FormattedTextareaType` is a custom form type in the PrestaShopBundle form layer
