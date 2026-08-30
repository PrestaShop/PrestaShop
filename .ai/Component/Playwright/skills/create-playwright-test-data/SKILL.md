---
name: create-playwright-test-data
description: >
  Create Faker data classes and predefined data objects for a new entity in the
  ui-testing-library. These are imported by campaigns for consistent test data
  generation. Trigger: "create test data for {Domain}".
needs: []
produces: "Faker{Domain} class + data{Domain} predefined data in ui-testing-library"
---

# create-playwright-test-data

Read `@.ai/Component/Playwright/CONTEXT.md` for the stack and data patterns.

Faker data classes and predefined data objects live in the **[ui-testing-library](https://github.com/PrestaShop/ui-testing-library)**, not in the core repo.

## 1. Faker data class

Create `Faker{Domain}` in the ui-testing-library:

- Constructor accepts optional overrides: `new FakerTax({enabled: false})`
- Default values use Faker for randomized but realistic data
- Properties match the entity fields (name, active, rate, etc.)
- Multilingual fields include values for at least EN and FR

```typescript
// Usage in campaigns:
const createTaxData: FakerTax = new FakerTax();
const editTaxData: FakerTax = new FakerTax({enabled: false});
```

**Reference:** check existing Faker classes in the ui-testing-library (e.g. `FakerTax`, `FakerProduct`)

## 2. Predefined data objects

Create `data{Domain}` objects referencing demo install fixtures:

```typescript
// Usage in campaigns:
import {dataTaxes} from '@prestashop-core/ui-testing';
dataTaxes.DefaultFrTax.id;
dataTaxes.DefaultFrTax.name;
```

These reference entities that exist in the vanilla PrestaShop installation — useful for tests that filter/sort existing data rather than creating new entities.

## 3. Local data files (if needed)

For campaign-specific data that doesn't fit in the library, create `tests/UI/data/{domain}.ts` in the core repo:

- XML test data in `tests/UI/data/xml/`
- TypeScript data-driven test arrays for forEach patterns

## Rules

Conventions (no random data in assertions, deterministic Faker values) are in [Playwright/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Faker classes and predefined data live in the ui-testing-library, not the core repo
- Faker defaults should produce valid entities — a bare `new Faker{Domain}()` must pass validation
- Predefined data references demo install fixtures — don't assume entities exist that aren't in the standard install
