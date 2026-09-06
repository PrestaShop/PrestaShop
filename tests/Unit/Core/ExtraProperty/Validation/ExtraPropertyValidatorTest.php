<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Validation;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyValidator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Unit tests for the constraint-based ExtraPropertyValidator.
 *
 * Uses a standalone Symfony validator (Validation::createValidator()) with plain, dependency-free constraints
 * (Length, Regex, NotBlank): container-backed constraints such as TypedRegex are exercised by the integration
 * tests instead. The focus here is the facade behaviour — constraint resolution, scope walking and property-path
 * re-basing — not the individual constraint rules.
 */
class ExtraPropertyValidatorTest extends TestCase
{
    private function validator(): ExtraPropertyValidator
    {
        return new ExtraPropertyValidator(Validation::createValidator());
    }

    /**
     * @param list<\Symfony\Component\Validator\Constraint> $constraints
     */
    private function definition(ExtraPropertyScope $scope, array $constraints): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'video_link',
            type: ExtraPropertyType::STRING,
            scope: $scope,
            moduleName: 'demoextrafield',
            constraints: $constraints,
        );
    }

    public function testNoConstraintsYieldsNoViolations(): void
    {
        $violations = $this->validator()->validateValue($this->definition(ExtraPropertyScope::COMMON, []), 'anything');

        $this->assertCount(0, $violations);
    }

    public function testValidScalarValueYieldsNoViolations(): void
    {
        $definition = $this->definition(ExtraPropertyScope::COMMON, [new Assert\Length(['max' => 10])]);

        $this->assertCount(0, $this->validator()->validateValue($definition, 'short'));
    }

    public function testFailingScalarKeepsTheConstraintMessage(): void
    {
        $definition = $this->definition(ExtraPropertyScope::COMMON, [new Assert\Length(['max' => 3])]);

        $violations = $this->validator()->validateValue($definition, 'toolong');

        $this->assertCount(1, $violations);
        $this->assertNotEmpty((string) $violations->get(0)->getMessage());
    }

    public function testMultipleFailingConstraintsProduceMultipleViolations(): void
    {
        $definition = $this->definition(ExtraPropertyScope::COMMON, [
            new Assert\Length(['min' => 5]),
            new Assert\Regex(['pattern' => '/^\d+$/']),
        ]);

        // "ab" is both too short (< 5) and not all-digits → one violation per failing constraint.
        $this->assertCount(2, $this->validator()->validateValue($definition, 'ab'));
    }

    public function testBatchValidationPrefixesPathWithModuleAndProperty(): void
    {
        $definition = $this->definition(ExtraPropertyScope::COMMON, [new Assert\Length(['max' => 3])]);
        $collection = new ExtraPropertyDefinitionCollection([$definition]);

        $violations = $this->validator()->validate(
            ['demoextrafield' => ['video_link' => 'toolong']],
            $collection
        );

        $this->assertCount(1, $violations);
        $this->assertSame('demoextrafield.video_link', $violations->get(0)->getPropertyPath());
    }

    public function testPerLanguageConstraintWithAllTagsTheOffendingLanguage(): void
    {
        // Per-language validation is expressed with Symfony's Assert\All (the value is the whole [id_lang=>value]
        // array). All tags each failing element with a "[<key>]" path → re-based to "<module>.<property>[<key>]".
        $definition = $this->definition(ExtraPropertyScope::LANG, [new Assert\All([new Assert\Length(['max' => 3])])]);
        $collection = new ExtraPropertyDefinitionCollection([$definition]);

        // id_lang 1 is valid ("ok"); id_lang 2 is too long → only the latter must produce a violation, path-tagged.
        $violations = $this->validator()->validate(
            ['demoextrafield' => ['video_link' => [1 => 'ok', 2 => 'toolong']]],
            $collection
        );

        $this->assertCount(1, $violations);
        $this->assertSame('demoextrafield.video_link[2]', $violations->get(0)->getPropertyPath());
    }

    public function testWholeArrayConstraintValidatesTheArrayItself(): void
    {
        // A bare (non-All) constraint validates the whole array — the mechanism that lets DefaultLanguage work on a
        // localized field. Assert\Count is used here as a dependency-free stand-in (DefaultLanguage's validator needs
        // the container). The violation lands on the field itself, not a language key.
        $definition = $this->definition(ExtraPropertyScope::LANG, [new Assert\Count(['min' => 2])]);
        $collection = new ExtraPropertyDefinitionCollection([$definition]);

        $violations = $this->validator()->validate(
            ['demoextrafield' => ['video_link' => [1 => 'only-one-language']]],
            $collection
        );

        $this->assertCount(1, $violations);
        $this->assertSame('demoextrafield.video_link', $violations->get(0)->getPropertyPath());
    }

    public function testObjectModelLangIdScalarValidatesUnwrappedAllAndSkipsWholeArrayConstraints(): void
    {
        // An ObjectModel loaded WITH a langId exposes a LANG value as a single SCALAR, not the [id_lang=>value] array.
        // The per-language rules (unwrapped from Assert\All) must apply to the scalar; whole-array constraints must be
        // skipped (Assert\Count stands in for DefaultLanguage here — it would otherwise hard-error on a scalar value).
        $definition = $this->definition(ExtraPropertyScope::LANG, [
            new Assert\All([new Assert\Length(['max' => 3])]),
            new Assert\Count(['min' => 2]),
        ]);

        // Invalid per-language scalar → exactly one violation, from the unwrapped Length (Count is skipped, no error).
        $this->assertCount(1, $this->validator()->validateValue($definition, 'toolong'));
        // Valid per-language scalar → no violation.
        $this->assertCount(0, $this->validator()->validateValue($definition, 'ok'));
    }

    public function testDefinitionAbsentFromPayloadIsSkipped(): void
    {
        $definition = $this->definition(ExtraPropertyScope::COMMON, [new Assert\NotBlank()]);
        $collection = new ExtraPropertyDefinitionCollection([$definition]);

        // The property is not in the payload at all → it must not be validated (no spurious NotBlank violation).
        $this->assertCount(0, $this->validator()->validate(['demoextrafield' => []], $collection));
    }

    /**
     * The single rule-set shared with the registry's default-value check: what cannot be a
     * default cannot be a stored value either. Null and '' always pass ("no value" is the
     * column's or a declared constraint's concern); runtime-only shapes (DateTimeInterface,
     * decoded JSON structures) are accepted on top of the scalar spellings.
     *
     * @dataProvider valueCompatibilityProvider
     */
    public function testIsValueCompatible(ExtraPropertyType $type, mixed $value, bool $expected, ?array $enumValues = null): void
    {
        $this->assertSame($expected, ExtraPropertyValidator::isValueCompatible($type, $value, $enumValues));
    }

    public static function valueCompatibilityProvider(): iterable
    {
        yield 'null always passes' => [ExtraPropertyType::INT, null, true];
        yield 'empty string always passes' => [ExtraPropertyType::DATE, '', true];

        yield 'int native' => [ExtraPropertyType::INT, 42, true];
        yield 'int string' => [ExtraPropertyType::INT, '-7', true];
        yield 'int refuses letters' => [ExtraPropertyType::INT, 'abc', false];
        yield 'int refuses float string' => [ExtraPropertyType::INT, '1.5', false];

        yield 'float native' => [ExtraPropertyType::FLOAT, 1.5, true];
        yield 'float numeric string' => [ExtraPropertyType::FLOAT, '1.5', true];
        yield 'float refuses letters' => [ExtraPropertyType::FLOAT, 'abc', false];

        yield 'bool native' => [ExtraPropertyType::BOOL, false, true];
        yield 'bool 0/1 spellings' => [ExtraPropertyType::BOOL, '1', true];
        yield 'bool refuses other ints' => [ExtraPropertyType::BOOL, 2, false];

        yield 'date literal date' => [ExtraPropertyType::DATE, '2026-12-24', true];
        yield 'date literal datetime' => [ExtraPropertyType::DATE, '2026-12-24 10:30:00', true];
        yield 'date DateTimeInterface' => [ExtraPropertyType::DATE, new DateTimeImmutable(), true];
        yield 'date refuses relative wording' => [ExtraPropertyType::DATE, 'tomorrow', false];
        yield 'date refuses rolled-over date' => [ExtraPropertyType::DATE, '2026-02-31', false];
        yield 'date refuses garbage' => [ExtraPropertyType::DATE, 'not-a-date', false];

        yield 'choice member' => [ExtraPropertyType::CHOICE, 'a', true, ['a', 'b']];
        yield 'choice refuses outsider' => [ExtraPropertyType::CHOICE, 'z', false, ['a', 'b']];
        yield 'choice without enum passes' => [ExtraPropertyType::CHOICE, 'anything', true];

        yield 'json encoded string' => [ExtraPropertyType::JSON, '{"a":1}', true];
        yield 'json decoded structure' => [ExtraPropertyType::JSON, ['a' => 1], true];
        yield 'json refuses invalid string' => [ExtraPropertyType::JSON, '{invalid', false];

        yield 'string accepts anything scalar' => [ExtraPropertyType::STRING, 'free text', true];
    }

    /**
     * The type rules apply implicitly on every write, even with ZERO declared constraints —
     * previously 'abc' on an INT was silently stored as 0 and 'tomorrow' on a DATE was
     * interpreted. The LANG/SHOP array shape is checked leaf by leaf with the offending
     * key in the violation path.
     */
    public function testImplicitTypeValidationRunsWithoutDeclaredConstraints(): void
    {
        $intDefinition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'stock_alert',
            type: ExtraPropertyType::INT,
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
        );

        $violations = $this->validator()->validateValue($intDefinition, 'abc');
        $this->assertCount(1, $violations);
        $this->assertStringContainsString('"int" field type', $violations->get(0)->getMessage());
        $this->assertCount(0, $this->validator()->validateValue($intDefinition, '42'));

        $langDateDefinition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'seen_at',
            type: ExtraPropertyType::DATE,
            scope: ExtraPropertyScope::LANG,
            moduleName: 'demoextrafield',
        );

        $violations = $this->validator()->validateValue($langDateDefinition, ['en-US' => '2026-12-24', 'fr-FR' => 'tomorrow']);
        $this->assertCount(1, $violations);
        $this->assertSame('[fr-FR]', $violations->get(0)->getPropertyPath());
    }

    /**
     * Declared constraints keep message priority: when a module declares Assert\Type('bool')
     * and the value fails both it and the implicit type rule, the module's constraint
     * message is reported — the implicit check is a safety net, not a replacement.
     */
    public function testDeclaredConstraintMessageWinsOverTheImplicitTypeCheck(): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'api_flag',
            type: ExtraPropertyType::BOOL,
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'demoextrafield',
            constraints: [new Assert\Type('bool')],
        );

        $violations = $this->validator()->validateValue($definition, 'not-a-bool');

        $this->assertCount(1, $violations);
        $this->assertStringNotContainsString('field type', $violations->get(0)->getMessage());
    }
}
