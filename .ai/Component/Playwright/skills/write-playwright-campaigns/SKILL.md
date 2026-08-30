---
name: write-playwright-campaigns
description: >
  Write Playwright test campaigns for a migrated entity: CRUD lifecycle, bulk actions,
  filter/sort, position reorder, and per-tab field verification. Campaigns live in
  the core repo and import page objects from ui-testing-library. Trigger:
  "write Playwright tests for {Domain}".
needs: [create-playwright-page-objects, create-playwright-test-data, create-admin-routing]
produces: "Test campaign files in tests/UI/campaigns/functional/BO/{section}/{subsection}/"
subagent: optional
---

# write-playwright-campaigns

Read `@.ai/Component/Playwright/CONTEXT.md` for conventions (testIdentifier, baseContext, Mocha structure, POM usage).

## 1. Directory and naming

Place campaigns in `tests/UI/campaigns/functional/BO/{XX_section}/{XX_subsection}/`:

- Follow existing numbering convention (`XX_` prefix)
- Each campaign file: `XX_descriptiveName.ts`
- CRUD is typically `01_`, filter is `02_`, bulk is `03_`, position is `04_`, tab-specific start at `05_`

## 2. Campaign boilerplate

Every campaign follows this structure:

```typescript
import testContext from '@utils/testContext';
import {expect} from 'chai';
import {
  boDashboardPage, boLoginPage, bo{Domain}Page, bo{Domain}CreatePage,
  type BrowserContext, Faker{Domain}, type Page, utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_{section}_{subsection}_{campaignName}';

describe('BO - {Section} : {Campaign description}', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);
    // ...login steps...
  });

  // ... test steps ...
});
```

## 3. CRUD campaign (`01_CRUD{Domain}.ts`)

- **Create:** navigate to add page, fill fields using `Faker{Domain}`, save, assert success message
- **Verify in list:** navigate to index, assert entity visible in grid
- **Edit:** navigate to edit, change fields, save, assert success message
- **Verify edit:** confirm updated values in grid
- **Delete:** click delete row action, confirm modal, assert entity removed

## 4. Filter/sort campaign (`02_filterSort{Domain}s.ts`)

- Create entities with distinct values for each filterable field
- For each filter: apply → assert matching rows → reset → assert all rows
- For each sortable column: sort ascending → verify order → sort descending → verify reverse
- Use data-driven patterns with `forEach` for multiple filter tests

## 5. Bulk actions campaign (`03_quickEditAndBulkActions.ts`)

Conditional — only if bulk actions exist:

- Create multiple entities
- **Quick-edit toggle:** click toggle switch, verify status changed without page reload (AJAX)
- **Bulk enable/disable:** select rows, click bulk action, verify status change
- **Bulk delete:** select rows, click bulk delete, confirm modal, verify removed

## 6. Position campaign (`04_changePosition.ts`)

Conditional — only if entity has PositionColumn:

- Create entities with distinct names
- Read initial order
- Drag row to new position using `page.dragAndDrop()`
- Verify new order in DOM
- **Reload page and verify persistence** — critical to confirm DB was updated

## 7. Per-tab campaigns (`05_tabName.ts`, `06_tabName.ts`, ...)

Conditional — only for multi-tab forms:

- One campaign per form tab
- Create entity with minimal data
- Navigate to edit, switch to target tab
- Fill every field in that tab
- Save, reload, verify each field persisted
- Test multilingual fields in at least 2 languages

## 8. Common tests (reusable blocks)

Check `tests/UI/commonTests/BO/` for existing reusable describe blocks:

```typescript
import {createProductTest, deleteProductTest} from '@commonTests/BO/catalog/product';

// Use as pre/post test setup
createProductTest(productData, baseContext);
// ... your test ...
deleteProductTest(productData, baseContext);
```

Import and reuse these instead of duplicating setup/teardown logic.

## Rules

Conventions (testIdentifier, `function()` not arrow, feature flag setup, toggle AJAX, drag-and-drop, per-tab campaigns, campaign numbering) are in [Playwright/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Assert success flash after every create/edit/delete — never assume success
- Use page object methods for interactions — never write raw selectors in campaigns
- Campaigns import from `@prestashop-core/ui-testing` — page objects and data are in the external library
