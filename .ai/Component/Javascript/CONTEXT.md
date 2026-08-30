# Javascript Component

## Purpose

Back-office JavaScript/TypeScript infrastructure: page entry points, reusable global components, grid extensions, and Vue integration for complex UI sections. Front-office JS is separate (in themes).

## Layers

| Layer | Path |
|-------|------|
| Page entry points | `admin-dev/themes/new-theme/js/pages/{domain}/` |
| Global components | `admin-dev/themes/new-theme/js/components/` |
| Grid extensions | `admin-dev/themes/new-theme/js/components/grid/extension/` |
| App utilities | `admin-dev/themes/new-theme/js/app/` |
| Vue integration | `admin-dev/themes/new-theme/js/vue/`, `js/pages/{domain}/` (Vue SFCs) |
| Webpack config | `admin-dev/themes/new-theme/.webpack/common.js` |
| Output bundles | `admin-dev/themes/new-theme/public/*.bundle.js` |

Path aliases: `@app`, `@js`, `@pages`, `@components`, `@scss`, `@PSVue`, `@PSTypes`.

## Non-obvious patterns

- **Per-page entry points:** every admin page has its own TypeScript entry point under `js/pages/{domain}/` (one for listing, one for form). Webpack registers each as a named bundle. See [`create-ts-entry-point`](skills/create-ts-entry-point/SKILL.md) for the procedure.
- **Global components live on `window.prestashop.component`:** each component activates automatically based on `data-*` attributes rendered by the matching Symfony form type. The DOM is the wiring — the component does not know which form it belongs to.
- **Component initialisation runs once per page load:** call `initComponents([...])` inside `$(() => { ... })` (jQuery ready). No re-initialisation on later DOM mutations.
- **Direct instantiation as the escape hatch:** for fine-grained control, components can be instantiated explicitly (`new window.prestashop.component.ChoiceTree(selector)`). This is exceptional — prefer `initComponents` whenever possible.
- **Module-registered components:** modules register custom components by listening to the `PSComponentsInitiated` event via `EventEmitter` (i.e. components are extensible from outside the core).
- **Grid is also a component:** instantiate `new window.prestashop.component.Grid('{grid_id}')` and chain extensions onto it. The grid ID **must** match the PHP `GRID_ID` constant — mismatch silently breaks extension binding.
- **Multiple grids on one page:** create a separate `new Grid()` instance per grid with its own extension chain (e.g. Manufacturer page has manufacturers + addresses).
- **Vue is the exception, not the default:** most pages use `initComponents` with standard form types. Vue is reserved for sections with complex UX that standard components cannot deliver (combination listing, dynamic range tables). When Vue is justified: Composition API only (`<script setup>`), `vue-i18n` initialised from `data-*` attributes on the mount element, and Vue state synced back to the Symfony form via hidden `<input>` fields — Vue is a UI layer, not the form.

## Canonical examples

- `admin-dev/themes/new-theme/js/pages/tax/index.ts` — simple listing entry point
- `admin-dev/themes/new-theme/js/pages/manufacturer/index.ts` — listing with multiple grids
- `admin-dev/themes/new-theme/js/pages/category/index.ts` — listing with position management
- `admin-dev/themes/new-theme/js/pages/attribute/form/index.ts` — simple form entry point
- `admin-dev/themes/new-theme/js/pages/product/edit/index.ts` — complex form with Vue components
- `admin-dev/themes/new-theme/js/pages/product/combination/` — Vue integration reference

## Skills

| Skill | Trigger |
|-------|---------|
| [`create-ts-entry-point`](skills/create-ts-entry-point/SKILL.md) | "create JS entry point for {Domain}" |
| [`init-js-components`](skills/init-js-components/SKILL.md) | "initialize components for {Domain}" |
| [`init-grid-extensions`](skills/init-grid-extensions/SKILL.md) | "add grid extensions for {Domain}" |
| [`create-vue-component`](skills/create-vue-component/SKILL.md) | "add Vue component for {Domain}" |
