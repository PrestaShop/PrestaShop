---
name: create-form-tab-layout
description: >
  Create a multi-tab form layout using PrestaShop's NavigationTabType. This is a
  specific pattern for complex forms with many fields organized by tabs — NOT the
  default form layout. Most forms do not need tabs. Trigger: "create tab layout
  for {Domain} form", "add tabs to {Domain} form".
needs: [create-crud-form-type]
produces: "NavigationTabType-based tab structure with sub-form types per tab"
conditional: "only for complex entities with many fields requiring tab organization"
---

# create-form-tab-layout

> **This is NOT the default form pattern.** Most entity forms are simple single-page forms
> using `TranslatorAwareType` or `AbstractType`. Only use `NavigationTabType` when the entity
> has many fields that benefit from tab organization (e.g. Carrier with general, shipping,
> size/weight tabs).

## Instructions

1. In root `{Domain}Type`, declare `NavigationTabType` as the parent type via `getParent()`:
   ```php
   public function getParent(): string
   {
       return NavigationTabType::class;
   }
   ```
   Then in `buildForm()`, add each tab as a sub-form — each child is rendered as a tab.
2. Create one form type per tab: `{Domain}GeneralTabType.php`, `{Domain}ShippingTabType.php`, etc.
3. Each tab type extends `TranslatorAwareType` with its own `buildForm()` containing only that tab's fields.
4. Tab anchor IDs (used for JS error scrolling) are derived from the tab names.

**Reference:** `src/PrestaShopBundle/Form/Admin/Shipping/Carrier/CarrierType.php`, `src/PrestaShopBundle/Form/Admin/Sell/Product/EditProductFormType.php`

## Rules

Conventions (NavigationTabType is PS-specific, tab anchor IDs for JS error nav) are in [Forms/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Each tab gets its own form type class for maintainability
- Tab titles must use `$this->trans()` for translation
