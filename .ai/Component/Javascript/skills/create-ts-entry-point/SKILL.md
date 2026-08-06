---
name: create-ts-entry-point
description: >
  Create the TypeScript entry point files for a new admin page. Covers the directory
  structure, listing and form entry points, and webpack registration. References
  init-js-components and init-grid-extensions for component/extension details.
  Trigger: "create JS entry point for {Domain}".
needs: []
produces: "admin-dev/themes/new-theme/js/pages/{domain}/ — TypeScript entry points + webpack entry"
---

# create-ts-entry-point

Read `@.ai/Component/Javascript/CONTEXT.md` for the page structure and webpack config.

## 1. Directory structure

Create `admin-dev/themes/new-theme/js/pages/{domain}/`:

```
{domain}/
├── index.ts              # Listing page entry point
├── form.ts               # Form page entry point (simple)
│   OR form/index.ts      # Form page entry point (complex, with sub-files)
└── {domain}-map.ts       # DOM selector mappings (optional, for pages with many selectors)
```

For simple pages, `index.ts` and `form.ts` are enough. Only create subdirectories and manager classes for genuinely complex pages.

## 2. Listing entry point (`index.ts`)

Initializes the Grid instance and adds extensions. See `init-grid-extensions` skill for the full extension list and patterns.

**Reference:** `admin-dev/themes/new-theme/js/pages/tax/index.ts` (simple), `admin-dev/themes/new-theme/js/pages/manufacturer/index.ts` (multiple grids)

## 3. Form entry point (`form.ts` or `form/index.ts`)

Initializes the global components the form needs. See `init-js-components` skill for the full component list and usage.

For simple forms, a few `initComponents` calls are all you need. For complex forms with Vue sections, see the `create-vue-integration` skill.

**Reference:** `admin-dev/themes/new-theme/js/pages/attribute/form/index.ts` (simple form)

## 4. Webpack registration

Add the entry point in `admin-dev/themes/new-theme/.webpack/common.js`:

```javascript
entry: {
  // ...existing entries...
  {domain}: './js/pages/{domain}',              // listing
  {domain}_form: './js/pages/{domain}/form',    // form (if separate)
}
```

Output: `admin-dev/themes/new-theme/public/{domain}.bundle.js`

The Twig template must include this bundle via a `<script>` tag (see `create-twig-index-template` / `create-twig-form-template`).

## Rules

- Entry points must be registered in webpack — unregistered files are not compiled
- Grid ID in `new Grid('{domain}')` must match the `GRID_ID` from the PHP grid definition
- For simple pages, the entry point IS the logic. Manager classes are for complex pages only
