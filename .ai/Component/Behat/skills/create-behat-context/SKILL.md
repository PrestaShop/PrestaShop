---
name: create-behat-context
description: >
  Create the PHP feature context class that implements step definitions for a domain,
  and register it in behat.yml. Covers the PHP implementation side of Behat tests.
  Trigger: "create behat context for {Domain}".
needs: [create-cqrs-commands, create-cqrs-queries]
produces: "{Domain}FeatureContext.php + behat.yml registration"
---

# create-behat-context

Read `@.ai/Component/Behat/CONTEXT.md` for conventions (base class, entity references, stateless steps, bus access).

## 1. Context class

Create `tests/Integration/Behaviour/Features/Context/Domain/{Domain}/{Domain}FeatureContext.php`:

- Extend `AbstractDomainFeatureContext`
- Implement step definitions as methods with `@Given`, `@When`, `@Then` annotations
- Use `$this->getCommandBus()->handle(...)` for write operations
- Use `$this->getQueryBus()->handle(...)` for read/verification
- Use `$this->referenceToId($reference)` to resolve string references to integer IDs
- After creating an entity, store reference: `$this->getSharedStorage()->set($reference, $newId)`

**Reference:** `tests/Integration/Behaviour/Features/Context/Domain/Tax/TaxFeatureContext.php` (simple)

## 2. Step implementation patterns

### Action steps (@When)

```php
/**
 * @When I add a {domain} :reference with following properties:
 */
public function iAddDomainWithProperties(string $reference, TableNode $table): void
{
    $data = $this->localizeByRows($table);
    $command = new Add{Domain}Command(
        $data['name'],
        (bool) $data['active'],
    );
    $id = $this->getCommandBus()->handle($command);
    $this->getSharedStorage()->set($reference, $id->getValue());
}
```

### Assertion steps (@Then) — must be stateless

```php
/**
 * @Then {domain} :reference should have the following properties:
 */
public function domainShouldHaveProperties(string $reference, TableNode $table): void
{
    $id = $this->referenceToId($reference);
    $result = $this->getQueryBus()->handle(new Get{Domain}ForEditing($id));
    // Assert each field independently
}
```

The assertion loads the entity fresh from the database — it does NOT rely on state from a previous step.

**Always go through the query bus, never bypass it.** Reading state directly from the ObjectModel (`new {Domain}($id)`) or from a raw SQL query inside an assertion is a recurring PR review trap: it hides bugs in the read side (missing fields on the result DTO, broken handler logic), and it defeats the purpose of running scenarios through the bus. If the field you need to assert is not exposed on the result DTO, fix the DTO — see [create-cqrs-queries](../../../CQRS/skills/create-cqrs-queries/SKILL.md#3-result-dto).

### Store ids in SharedStorage, not entities

`SharedStorage::set()` should receive the **identifier** (an int or, for compound keys, a small array of scalars) — never the full ObjectModel or DTO. Storing an entity couples every later step to its shape, surfaces stale data when the underlying row mutates between scenarios, and confuses static analysis when the storage value type drifts.

```php
// Preferred — store the id
$entity = new {Domain}();
$entity->name = 'Example';
$entity->add();
$this->getSharedStorage()->set($reference, (int) $entity->id);

// Avoid — storing the entity
$this->getSharedStorage()->set($reference, $entity);
```

Readers then pull the id back as an `int`:

```php
$id = (int) SharedStorage::getStorage()->get($reference);
$this->getCommandBus()->handle(new Delete{Domain}Command($id));
```

When the entity *must* be created via the ObjectModel directly (e.g. a thread that the front-office contact form would normally produce and for which no CQRS command exists), the ObjectModel construction is acceptable — but the value persisted to `SharedStorage` is still just the id.

### Reading ids — use `referenceToId`, never read `SharedStorage` directly

Every step that needs an id from a reference should call the base class helper `$this->referenceToId($reference)` — it unwraps the int from `SharedStorage` and throws a clear `RuntimeException` if the reference is missing. Do not read `SharedStorage` manually inside step bodies; the abstraction is there precisely so the storage shape can evolve without touching every context.

```php
// Preferred
$this->getCommandBus()->handle(new Delete{Domain}Command($this->referenceToId($reference)));

// Avoid
$id = (int) SharedStorage::getStorage()->get($reference);
$this->getCommandBus()->handle(new Delete{Domain}Command($id));
```

`SharedStorage::set()` (via `$this->getSharedStorage()->set($reference, $id)`) is only called when the entity is created in a `@Given` / `@When` step; reads always go through `referenceToId`.

### Not-found scenarios — dedicated step, not a polymorphic reference

Error scenarios that operate on a missing entity get their **own step** that accepts the raw id directly. Do **not** overload the regular step by passing a numeric reference and falling back to `(int) $reference` — `referenceToId` is allowed to throw on a missing reference, so the polymorphism creates ambiguity.

```php
/**
 * @When I delete {domain} :reference
 */
public function delete{Domain}(string $reference): void
{
    $this->dispatchDelete{Domain}($this->referenceToId($reference));
}

/**
 * @When /^I delete non-existent {domain} with id (\d+)$/
 */
public function deleteNonExistent{Domain}(int $id): void
{
    $this->dispatchDelete{Domain}($id);
}

private function dispatchDelete{Domain}(int $id): void
{
    try {
        $this->getCommandBus()->handle(new Delete{Domain}Command($id));
    } catch ({Domain}Exception $e) {
        $this->setLastException($e);
    }
}
```

```gherkin
# Happy path
When I delete {domain} "ref_1"

# Not-found — dedicated step with the raw id
When I delete non-existent {domain} with id 999999
```

The two entry points share the dispatch body via a small private helper — no code duplication, the regex enforces a numeric id at the Gherkin level, and the happy-path step stays predictable (a missing reference is always an error, never silently coerced).

### Error steps (capture-then-assert pattern)

Errors are tested in two paired steps: the `@When` step **catches** the domain exception and stores it via `$this->setLastException(...)`; the next `@Then` step **asserts** the stored exception via `$this->assertLastErrorIs(...)`.

```php
/**
 * @When I add a {domain} :reference with invalid name
 */
public function addWithInvalidName(string $reference): void
{
    try {
        $this->getCommandBus()->handle(new Add{Domain}Command(''));
    } catch ({Domain}ConstraintException $e) {
        $this->setLastException($e);
    }
}

/**
 * @Then I should get error that {domain} name is invalid
 */
public function assertLastErrorIsInvalidName(): void
{
    $this->assertLastErrorIs(
        {Domain}ConstraintException::class,
        {Domain}ConstraintException::INVALID_NAME, // optional error code
    );
}
```

Two safety nets enforced by `CommonFeatureContext`:

- A captured exception **must** be asserted by a following step. If the next step ends without calling `assertLastErrorIs`, the `checkExpectedExceptionAfterStep` `@AfterStep` hook re-throws it as a `RuntimeException` — preventing a domain exception from being silently swallowed.
- `cleanStoredExceptionsBeforeScenario` (`@BeforeScenario`) clears any leftover exception so scenarios don't leak state into each other.

So: **never `try/catch` and ignore** — always pair the capture with an assertion in the very next step.

**Always pass the error code to `assertLastErrorIs`** when the domain exception class has codes (`FAILED_TO_UPDATE_STATUS`, `INVALID_NAME`, etc.). The class-only form is too permissive: it matches every exception of that class regardless of cause, so a regression that throws the same class for a different reason silently passes. The two-argument form pins both the class *and* the specific error path under test.

## 3. Registration in behat.yml

Open `tests/Integration/Behaviour/behat.yml` and add the context to the `domain` suite:

```yaml
domain:
    contexts:
        - PrestaShop\Tests\Integration\Behaviour\Features\Context\Domain\{Domain}\{Domain}FeatureContext
```

Verify: `php vendor/bin/behat --dry-run` to confirm all steps are matched.

## Rules

Conventions (stateless steps, referenceToId, deterministic steps, typed exceptions, error scenarios) are in [Behat/CONTEXT.md](../../CONTEXT.md). Skill-specific reminders:

- Use `referenceToId` / `referencesToIds` — not `getSharedStorage()->get()` directly
- Check existing contexts for reusable steps before creating new ones
