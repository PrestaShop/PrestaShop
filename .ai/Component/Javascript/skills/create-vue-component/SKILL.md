---
name: create-vue-component
description: >
  Integrate a Vue 3 component into a Symfony admin page for complex UX sections.
  This is an exception pattern — most pages use initComponents with standard
  components. Vue is only needed when a section requires rich interactivity that
  standard form types cannot provide (e.g. combination listing, dynamic range
  tables). Trigger: "add Vue component for {Domain}".
needs: [create-ts-entry-point, create-crud-form-type]
produces: "Vue 3 SFC + initialization function mounted on a specific DOM section"
conditional: "only for sections with complex UX requiring rich interactivity"
---

# create-vue-component

Read `@.ai/Component/Javascript/CONTEXT.md` for the Vue integration overview.

> **This is NOT the default pattern.** Most admin pages use `initComponents()` with
> standard PS components. Only use Vue when a page section requires complex interactivity
> that standard form types and JS components cannot provide.

## When to use Vue

- Dynamic row tables (add/remove rows, reorder) — e.g. carrier ranges
- Complex filtering/search UIs with real-time updates — e.g. combination filters
- Sections with heavy state synchronization between multiple fields
- Interactive modals with multi-step workflows

When NOT to use Vue: simple forms, standard CRUD, toggle switches, translatable fields — all handled by `initComponents`.

## Integration pattern

Vue components are mounted on **one section of a Symfony page**, not the entire page. The rest of the page remains standard Symfony/Twig + PS components.

### 1. Create the Vue SFC

```
admin-dev/themes/new-theme/js/pages/{domain}/components/{ComponentName}.vue
```

Standard Vue 3 SFC with `<script setup>`, `<template>`, `<style>`. Use Composition API.

### 2. Create the initialization function

```typescript
// admin-dev/themes/new-theme/js/pages/{domain}/components/init{ComponentName}.ts
import {createApp} from 'vue';
import {createI18n} from 'vue-i18n';
import ComponentName from './ComponentName.vue';

export default function initComponentName(
  selector: string,
  eventEmitter: typeof EventEmitter,
  initialData: SomeType,
): void {
  const container = document.querySelector(selector);
  if (!container) return;

  const translations = JSON.parse(container.dataset.translations || '{}');
  const i18n = createI18n({locale: 'en', messages: {en: translations}});

  const app = createApp(ComponentName, {
    eventEmitter,
    initialData,
  });
  app.use(i18n);
  app.mount(selector);
}
```

### 3. Call from the page entry point

```typescript
// In form/index.ts or edit/index.ts
import initComponentName from './components/initComponentName';

$(() => {
  // Standard components first
  window.prestashop.component.initComponents(['TranslatableInput']);

  // Vue section
  const eventEmitter = window.prestashop.instance.eventEmitter;
  initComponentName('#vue-mount-point', eventEmitter, initialData);
});
```

### 4. Symfony/Twig side

The Twig template provides the mount point with initial data as `data-*` attributes:

```twig
<div id="vue-mount-point"
     data-translations="{{ translations|json_encode }}"
     data-initial="{{ initialData|json_encode }}">
</div>
```

A dedicated `FormType` can bridge PHP form data to the Vue component via hidden fields and `data-*` attributes.

**Reference:** `admin-dev/themes/new-theme/js/pages/product/combination/` — combination listing with filters, generator modal, bulk actions

## Communication with the rest of the page

- Use `EventEmitter` for events between Vue and non-Vue sections
- Vue component receives `eventEmitter` as a prop
- Emits events that the TypeScript entry point or other components listen to
- Hidden form fields sync Vue state back to the Symfony form for submission

## Rules

Conventions (Composition API required, `vue-i18n` setup, hidden form field sync, EventEmitter as prop) are in [Javascript/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Vue is the exception, not the default — justify its use
- Mount on a specific DOM selector, not the entire page
