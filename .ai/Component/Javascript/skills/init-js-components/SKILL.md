---
name: init-js-components
description: >
  Initialize and use PrestaShop's global JS components via initComponents(). Covers
  TranslatableInput, ChoiceTree, TinyMCEEditor, TaggableField, and all other
  available components. Components activate based on data-* attributes in the DOM.
  Trigger: "initialize components for {Domain}", "enable components for {Domain}",
  "add components to {Domain} form".
needs: [create-ts-entry-point]
produces: "initComponents() calls in the form entry point with the right component list"
---

# init-js-components

Read `@.ai/Component/Javascript/CONTEXT.md` for the full component list and initialization pattern.

## How initComponents works

```typescript
window.prestashop.component.initComponents([
  'TranslatableInput',
  'TinyMCEEditor',
]);
```

- Components are registered on `window.prestashop.component`
- `initComponents` creates instances and stores them in `window.prestashop.instance`
- Components activate automatically by scanning the DOM for matching `data-*` attributes
- Only initialize components the page actually uses

## Common form components

### TranslatableInput / TranslatableField
For multilingual fields. The Symfony `TranslatableType` renders the DOM with proper `data-*` attributes; this component adds the language tab switching UI.

### TinyMCEEditor
For rich text fields (`FormattedTextareaType`). Initializes the WYSIWYG editor on matching textareas.

### ChoiceTree
For hierarchical selectors (categories, groups). Call `.enableAutoCheckChildren()` if parent selection should auto-select children.

```typescript
new window.prestashop.component.ChoiceTree('#category-tree-selector').enableAutoCheckChildren();
```

### TaggableField
For tag inputs with autocomplete. Used with `TaggableType` form type.

### EntitySearchInput
For autocomplete search fields that reference other entities (e.g. search for a product by name).

### GeneratableInput
For fields auto-generated from another (e.g. URL slug from name). Uses `TextToLinkRewriteCopier` pattern.

### MultistoreConfigField / ModifyAllShopsCheckbox
For multistore-scoped configuration fields. Shows per-shop override controls.

### FormFieldToggler / DisablingSwitch
For conditional field visibility — shows/hides fields based on another field's value.

## Full component catalogue

| Component | Purpose |
|---|---|
| `TranslatableField` / `TranslatableInput` | Multilingual input with language tabs |
| `TinyMCEEditor` | Rich text editor |
| `TaggableField` | Tag input with autocomplete |
| `ChoiceTable` / `MultipleChoiceTable` | Checkbox/radio table selection |
| `ChoiceTree` | Hierarchical tree selector (categories, groups) |
| `EntitySearchInput` | Autocomplete entity search |
| `GeneratableInput` | Auto-generate from another field (e.g. slug from name) |
| `ColorPicker` | Color selection input |
| `DateRange` | Date range picker |
| `DeltaQuantityInput` | Quantity change input (stock) |
| `DisablingSwitch` | Toggle that disables related fields |
| `FormFieldToggler` | Show/hide fields based on another field's value |
| `TextWithLengthCounter` / `TextWithRecommendedLengthCounter` | Character count display |
| `CountryStateSelectionToggler` / `CountryDniRequiredToggler` | Country-dependent field logic |
| `MultistoreConfigField` | Multistore-scoped config field |
| `ModifyAllShopsCheckbox` | "Apply to all shops" checkbox |
| `PreviewOpener` | Preview popup |
| `Grid` | Grid component (see grid extensions) |
| `Router` | FOS JS router integration |
| `EventEmitter` | Cross-component event communication |
| `IframeClient` | Iframe communication |

Pass only the names the page actually needs to `initComponents([...])` — don't load the whole catalogue.

## Using components directly (without initComponents)

Some components can be instantiated directly for more control:

```typescript
const choiceTree = new window.prestashop.component.ChoiceTree(selector);
choiceTree.enableAutoCheckChildren();
```

## Custom components

Modules can register custom components:

```typescript
EventEmitter.on('PSComponentsInitiated', () => {
  window.prestashop.component.MyCustomComponent = MyCustomComponent;
});
```

## Rules

Conventions (jQuery ready pattern, direct instantiation, EventEmitter module registration) are in [Javascript/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Only initialize components the page needs — don't load everything
- Components rely on DOM `data-*` attributes rendered by Symfony form types — they are not standalone
- `initComponents` is called once at page load — no need to call it again after DOM changes
