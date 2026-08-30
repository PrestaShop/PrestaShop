---
name: write-behat-scenarios
description: >
  Write the Gherkin feature file and all scenarios for a domain: CRUD, bulk actions,
  filters, and error cases. Covers the .feature file side of Behat tests. Read
  Component/Behat/CONTEXT.md for conventions. Trigger: "write behat scenarios
  for {Domain}".
needs: [create-behat-context]
produces: "{domain}_management.feature — complete Gherkin scenario suite"
subagent: optional
---

# write-behat-scenarios

Read `@.ai/Component/Behat/CONTEXT.md` for conventions (stateless steps, entity references, Given/When/Then).

## 1. Feature file setup

Create `tests/Integration/Behaviour/Features/Scenario/{Domain}/{domain}_management.feature`:

```gherkin
@restore-all-tables-before-feature
Feature: {Domain} management
  As an admin
  I want to manage {domain}s
  So that ...

  Background:
    Given shop "shop1" with name "test_shop" exists
```

Background section should be minimal — only shared setup that every scenario needs.

**Reference:** `tests/Integration/Behaviour/Features/Scenario/Tax/` (simple)

## 2. Create scenarios

```gherkin
Scenario: Add a new {domain} with required fields
  When I add a {domain} "ref_1" with following properties:
    | name   | Test {Domain} |
    | active | true          |
  Then {domain} "ref_1" should have the following properties:
    | name   | Test {Domain} |
    | active | true          |
```

- Test with minimum required fields
- Test with all fields
- Assertion step independently loads the entity — stateless

## 3. Edit scenarios

```gherkin
Scenario: Edit {domain} name
  Given {domain} "ref_1" exists with name "Original"
  When I edit {domain} "ref_1" with following properties:
    | name | Updated |
  Then {domain} "ref_1" should have the following properties:
    | name | Updated |
```

- Test partial update (only changed fields)
- Verify unchanged fields remain unchanged

## 4. Delete scenarios

```gherkin
Scenario: Delete {domain}
  When I delete {domain} "ref_1"
  Then {domain} "ref_1" should not exist

Scenario: Delete non-existent {domain}
  When I delete {domain} "non_existent_ref"
  Then I should get an error "{Domain}NotFoundException"
```

## 5. Bulk action scenarios (if bulk commands exist)

```gherkin
Scenario: Bulk delete {domain}s
  Given the following {domain}s exist:
    | reference | name    |
    | ref_1     | First   |
    | ref_2     | Second  |
    | ref_3     | Third   |
  When I bulk delete {domain}s "ref_1,ref_2"
  Then {domain} "ref_1" should not exist
  And {domain} "ref_2" should not exist
  And {domain} "ref_3" should exist
```

- Create multiple entities with distinct values
- Test bulk enable/disable if the entity supports status toggle

## 6. Error / constraint scenarios

```gherkin
Scenario: Cannot add {domain} with empty name
  When I add a {domain} "fail_ref" with following properties:
    | name | |
  Then I should get an error "{Domain}ConstraintException"
```

- Test at least one constraint violation per required field
- Test business rule violations (e.g. cannot delete entity in use)

## 7. Filter scenarios (if grid filtering is tested at Behat level)

```gherkin
Scenario: Filter {domain}s by name
  Given the following {domain}s exist:
    | reference | name    |
    | ref_1     | Alpha   |
    | ref_2     | Beta    |
  When I search for {domain}s with name "Alpha"
  Then I should see only {domain} "ref_1" in results
```

Note: grid filtering is often tested at Playwright level instead of Behat.

## 8. Splitting the feature file

A single `.feature` file is the typical case, but split into multiple smaller files when:

- the domain has many concerns (CRUD vs bulk vs filters vs constraints) and the file grows past a few hundred lines
- you want to launch a focused subset (e.g. `behat ... bulk_actions.feature`) for debugging without running the rest

Suggested split: `{domain}_management.feature` (CRUD), `{domain}_bulk_actions.feature`, `{domain}_filters.feature`, `{domain}_constraints.feature`. Keep them in the same `Scenario/{Domain}/` folder.

## Rules

Conventions (stateless steps, string references, typed exceptions, deterministic steps) are in [Behat/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Scenario names must be unique within the feature
- Background section should be minimal — only universally shared setup
- Reuse existing step definitions from other contexts when possible
