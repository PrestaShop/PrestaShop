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

### Step deduplication via `resolveXxxId`

When the same action needs to cover a happy-path *and* a "non-existent {domain}" error scenario, do **not** create two parallel methods. Add one step and a private helper that falls back to treating the reference as a raw id when it is not in `SharedStorage`:

```php
private function resolve{Domain}Id(string $reference): int
{
    if (SharedStorage::getStorage()->exists($reference)) {
        return (int) SharedStorage::getStorage()->get($reference)->id;
    }

    return (int) $reference;
}
```

```gherkin
# Happy path
When I delete {domain} "ref_1"

# Not-found — same step, raw id as reference
When I delete {domain} "999999"
```

The single step uses `$this->resolve{Domain}Id($reference)` and wraps the dispatch in try/catch so the error scenario can assert via [the capture-then-assert pattern](#error-steps-capture-then-assert-pattern). Three steps collapse into one.

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
