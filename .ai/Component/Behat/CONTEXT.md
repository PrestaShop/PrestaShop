# Behat Component

## Purpose

Integration testing infrastructure using Behat (BDD). Tests exercise the CQRS layer (commands, queries, handlers) through Gherkin scenarios, validating business logic without a browser. Does not test UI — that's Playwright's role.

## Layers

| Layer | Path |
|-------|------|
| Feature files (Gherkin) | `tests/Integration/Behaviour/Features/Scenario/{Domain}/` |
| Feature contexts (PHP) | `tests/Integration/Behaviour/Features/Context/Domain/{Domain}/` |
| Behat config | `tests/Integration/Behaviour/behat.yml` |
| Shared helpers | `tests/Integration/Behaviour/Features/Context/` (base classes, shared contexts) |

## Key concepts

### Feature context classes — two layers

There are two distinct context layers:

- **`Context/Domain/{Domain}/`** — CQRS-based contexts. Built during migration. Use command bus and query bus to exercise the modern domain layer. These are what the migration skills create.
- **`Context/`** (root, non-Domain) — legacy-based contexts. Use ObjectModel and legacy classes directly. These exist for domains that have NOT been migrated yet but need fixture manipulation (e.g. creating a carrier in a test for another domain).

**During migration, always create contexts in `Context/Domain/`.** Never favor a legacy context if a CQRS-based step exists for the same action. Legacy contexts are only acceptable when the domain has no CQRS layer yet and you need fixtures.

All domain contexts:
- Extend `AbstractDomainFeatureContext` (which provides command bus, query bus, shared storage)
- Implement step definitions as PHP methods with `@Given`, `@When`, `@Then` annotations and regex patterns
- One context class per domain is the typical case (e.g. `TaxFeatureContext`, `ManufacturerFeatureContext`). Complex domains with many commands (e.g. Product, Order) can be split into sub-contexts under `Context/Domain/{Domain}/` — register each in `behat.yml`
- Contexts are NOT Symfony services — they are registered in `behat.yml`, not via DI

### Context registration

Contexts are declared in `tests/Integration/Behaviour/behat.yml` under the appropriate suite:

```yaml
domain:
    contexts:
        - PrestaShop\Tests\Integration\Behaviour\Features\Context\Domain\{Domain}\{Domain}FeatureContext
```

Run `php vendor/bin/behat --dry-run` after registration to verify all steps are matched.

### Entity references (referenceToId / referencesToIds)

- Use `$this->referenceToId($reference)` to resolve a string reference (e.g. `"tax_1"`) to an integer ID
- Use `$this->referencesToIds($references)` for lists
- After creating an entity, store it: `$this->shareStorage->set($reference, $newId)`
- Never hardcode integer IDs in step definitions — always use string references

### Stateless steps (critical rule)

**Steps must be stateless.** A step should never perform an action, save intermediate state, and then rely on a separate step to assert the result.

Bad (stateful — assertion depends on state saved by a previous step):
```gherkin
When I add a tax with name "VAT"
Then the last created tax should have name "VAT"
```

Good (stateless — action and assertion are self-contained):
```gherkin
When I add a tax "vat_ref" with name "VAT"
Then tax "vat_ref" name should be "VAT"
```

Bad (stateful — the Then step asserts data stored in memory by the When step):
```gherkin
When I query the product "product1"
Then I get the following properties:
  | name | product name |
```

Good (stateless — single step queries and asserts in one go):
```gherkin
When I query the product "product1" I should get the following properties:
  | name | product name |
```

The key principle: each step must independently load its data from the database. Never store query results in memory for a later step to assert — this hides which data is actually being checked and leads to false positives when a previous step's state leaks into the assertion.

### Command bus / Query bus access

- `$this->getCommandBus()->handle(...)` for write operations
- `$this->getQueryBus()->handle(...)` for read/verification operations
- Never instantiate handlers directly — always go through the bus

### Multilingual data in steps

- Use `$this->localizeByRows($table)` to convert a Gherkin `TableNode` into an array keyed by language ID — this is the standard helper for multilingual step definitions

### Error scenarios

- Error steps catch **typed domain exceptions**, not generic `\Exception`
- Pattern: `When I add a tax with invalid data` / `Then I should get an error "{ExceptionType}"`
- The context stores the caught exception and a subsequent Then step asserts its type

### Bulk action steps

- Pass multiple references as a comma-separated string: `When I bulk delete taxes "ref_1,ref_2,ref_3"`
- Use `$this->referencesToIds($references)` to resolve the list

### Deterministic steps

- Step definitions must be **deterministic** — no random data, no timing dependencies. Use fixed values or references

### Available tags (from CommonFeatureContext)

These tags trigger hooks defined in `CommonFeatureContext`. Add them to your `Feature:` or `Scenario:` line to activate.

**Database restoration:**

| Tag | Level | What it does |
|---|---|---|
| `@restore-all-tables-before-feature` | Feature | Restores all DB tables + cleans SharedStorage before the feature |
| `@restore-all-tables-after-feature` | Feature | Restores all DB tables + cleans SharedStorage after the feature |
| `@reset-all-tables-before-scenario` | Scenario | Restores all DB tables + cleans SharedStorage before each scenario |
| `@reset-all-tables-after-scenario` | Scenario | Restores all DB tables + cleans SharedStorage after each scenario |

**Kernel and cache:**

| Tag | Level | What it does |
|---|---|---|
| `@reboot-kernel-before-feature` | Feature | Reboots Symfony kernel (forces service recreation) |
| `@reboot-kernel-after-feature` | Feature | Reboots Symfony kernel after feature |
| `@reboot-kernel-before-scenario` | Scenario | Reboots Symfony kernel before each scenario |
| `@clear-cache-before-feature` | Feature | Clears all static caches |
| `@clear-cache-after-feature` | Feature | Clears all static caches |
| `@clear-cache-before-scenario` | Scenario | Clears all static caches |
| `@clear-cache-after-scenario` | Scenario | Clears all static caches |

**Resource cleanup:**

| Tag | Level | What it does |
|---|---|---|
| `@reset-downloads-after-feature` | Feature | Resets downloads directory |
| `@reset-img-after-feature` | Feature | Resets images directory |
| `@reset-test-modules-after-feature` | Feature | Resets test modules |

**Context mocking:**

| Tag | Level | What it does |
|---|---|---|
| `@mock-context-on-feature` | Feature | Mocks legacy Context before feature, resets after |
| `@mock-context-on-scenario` | Scenario | Mocks legacy Context before scenario, resets after |

**Automatic hooks (always active, no tag needed):**

- `@BeforeStep`: clears Doctrine EntityManager for fresh data at each step
- `@AfterStep`: checks for unhandled exceptions from previous steps
- `@BeforeScenario`: clears stored exceptions

The most commonly used tag is `@restore-all-tables-before-feature` — use it on any feature that modifies database state.

## Canonical examples

- `tests/Integration/Behaviour/Features/Context/Domain/Tax/TaxFeatureContext.php` — simple domain context
- `tests/Integration/Behaviour/Features/Scenario/Tax/` — simple CRUD scenarios
- `tests/Integration/Behaviour/Features/Context/Domain/Manufacturer/ManufacturerFeatureContext.php` — with sub-resources

## Skills

| Skill | Trigger |
|-------|---------|
| [`create-behat-context`](skills/create-behat-context/SKILL.md) | "create behat context for {Domain}" |
| [`write-behat-scenarios`](skills/write-behat-scenarios/SKILL.md) | "write behat scenarios for {Domain}" |

