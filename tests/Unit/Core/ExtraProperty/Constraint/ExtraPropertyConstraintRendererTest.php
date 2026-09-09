<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Constraint;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Constraint\ExtraPropertyConstraintGrammar;
use PrestaShop\PrestaShop\Core\ExtraProperty\Constraint\ExtraPropertyConstraintParser;
use PrestaShop\PrestaShop\Core\ExtraProperty\Constraint\ExtraPropertyConstraintRenderer;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Unit tests for the ExtraPropertyConstraintRenderer.
 *
 * Covers the transform from Symfony Constraint instances to the constraint DSL — the inverse of
 * the parser and the only way a constraint reaches the registry column. Rendering fails closed: a
 * class outside the grammar, an option the format cannot carry, an executable option or a value
 * whose type would drift on the way back all raise instead of being dropped, so what is rendered
 * always parses back to the very same constraints.
 */
final class ExtraPropertyConstraintRendererTest extends TestCase
{
    // -- render(): basics --------------------------------------------------------------------

    public function testRenderReturnsNullForNull(): void
    {
        $this->assertNull(ExtraPropertyConstraintRenderer::render(null));
    }

    public function testRenderReturnsNullForAnEmptyList(): void
    {
        $this->assertNull(ExtraPropertyConstraintRenderer::render([]));
    }

    public function testRenderFormatsASingleConstraintToItsAlias(): void
    {
        $this->assertSame('NotBlank', ExtraPropertyConstraintRenderer::render([new Assert\NotBlank()]));
    }

    public function testRenderFormatsMultipleConstraintsOnePerLinePreservingOrder(): void
    {
        $rendered = ExtraPropertyConstraintRenderer::render([
            new Assert\Email(),
            new Assert\Url(),
            new Assert\PositiveOrZero(),
        ]);

        $this->assertSame("Email\nUrl\nPositiveOrZero", $rendered);
    }

    public function testRenderUsesTheNamedShapeForAConstraintWithoutADefaultOption(): void
    {
        $rendered = ExtraPropertyConstraintRenderer::render([
            new Assert\NotBlank(),
            new Assert\Length(['max' => 10]),
            new Assert\Email(),
        ]);

        // Length has no default option → named-options shape.
        $this->assertSame("NotBlank\nLength(max: 10)\nEmail", $rendered);
    }

    public function testRenderEscapesBackslashesInAPrimaryOption(): void
    {
        $rendered = ExtraPropertyConstraintRenderer::render([new Assert\Regex(['pattern' => '/^\d+$/'])]);

        // The backslash is escaped so the rendered value parses back to the same pattern.
        $this->assertSame("Regex('/^\\\\d+$/')", $rendered);
    }

    public function testRenderCombinesNamedAndPositionalShapes(): void
    {
        $rendered = ExtraPropertyConstraintRenderer::render([
            new Assert\Length(['max' => 10]),
            new Assert\Regex(['pattern' => '/^\d+$/']),
        ]);

        $this->assertSame("Length(max: 10)\nRegex('/^\\\\d+$/')", $rendered);
    }

    // -- render(): outside the grammar -------------------------------------------------------

    /**
     * The alias comes from the grammar, never from the class short name: a class outside the
     * allowlist is structurally unrenderable rather than emitted as a name the parser would refuse.
     */
    public function testRenderRefusesAConstraintClassOutsideTheGrammar(): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches('/not part of the extra property constraint grammar/');

        ExtraPropertyConstraintRenderer::render([new RendererUnsupportedConstraint()]);
    }

    public function testRenderRefusesASymfonyConstraintOutsideTheAllowlist(): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches('/"' . preg_quote(Assert\Callback::class, '/') . '" is not part of the extra property constraint grammar/');

        ExtraPropertyConstraintRenderer::render([new Assert\Callback('trim')]);
    }

    public function testRenderFailsClosedWhenOneConstraintIsOutsideTheGrammar(): void
    {
        // Nothing partial: the valid siblings are not rendered either.
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches('/not part of the extra property constraint grammar/');

        ExtraPropertyConstraintRenderer::render([new Assert\NotBlank(), new Assert\Callback('trim'), new Assert\Email()]);
    }

    // -- render(): parametric rendering ------------------------------------------------------

    public function testRenderQuotesAScalarPrimaryOption(): void
    {
        $this->assertSame(
            "TypedRegex('generic_name')",
            ExtraPropertyConstraintRenderer::render([new TypedRegex(['type' => 'generic_name'])])
        );
    }

    public function testRenderQuotesStringsInAListPrimaryOption(): void
    {
        $this->assertSame(
            "Choice(['a', 'b', 'c'])",
            ExtraPropertyConstraintRenderer::render([new Assert\Choice(['choices' => ['a', 'b', 'c']])])
        );
    }

    public function testRenderLeavesNumericListItemsBare(): void
    {
        $this->assertSame(
            "Choice(['01', '02', 3])",
            ExtraPropertyConstraintRenderer::render([new Assert\Choice(['choices' => ['01', '02', 3]])])
        );
    }

    /**
     * A keyed "label => value" map (the common shape a module already builds for its own form
     * widget) canonicalizes to its plain values list: nothing downstream (ChoiceValidator included)
     * ever reads the keys, so they are intentionally not preserved.
     */
    public function testRenderCanonicalizesAKeyedScalarMapToItsValues(): void
    {
        $rendered = ExtraPropertyConstraintRenderer::render([new Assert\Choice(['choices' => ['a' => 1, 'b' => 2]])]);

        $this->assertSame('Choice([1, 2])', $rendered);

        $decoded = ExtraPropertyConstraintParser::parse($rendered);
        $this->assertFalse($decoded->hasRejections());
        /** @var Assert\Choice $choice */
        $choice = $decoded->getConstraints()[0];
        $this->assertSame([1, 2], $choice->choices);
    }

    public function testRenderTheTypeConstraintScalarAndList(): void
    {
        $this->assertSame(
            "Type('integer')",
            ExtraPropertyConstraintRenderer::render([new Assert\Type(['type' => 'integer'])])
        );
        $this->assertSame(
            "Type(['integer', 'string'])",
            ExtraPropertyConstraintRenderer::render([new Assert\Type(['type' => ['integer', 'string']])])
        );
    }

    public function testRenderAnEmptyOptionalArgumentAsBareName(): void
    {
        // DefaultLanguage with an empty fieldName must not render "DefaultLanguage()".
        $this->assertSame('DefaultLanguage', ExtraPropertyConstraintRenderer::render([new DefaultLanguage()]));
        $this->assertSame(
            "DefaultLanguage('video_link')",
            ExtraPropertyConstraintRenderer::render([new DefaultLanguage(fieldName: 'video_link')])
        );
    }

    public function testRenderAComparisonConstraint(): void
    {
        $this->assertSame(
            'GreaterThan(5)',
            ExtraPropertyConstraintRenderer::render([new Assert\GreaterThan(['value' => 5])])
        );
    }

    public function testRenderOmitsInternalSentinels(): void
    {
        // PositiveOrZero carries value=0 internally; that is its default, not a configured option.
        $this->assertSame('PositiveOrZero', ExtraPropertyConstraintRenderer::render([new Assert\PositiveOrZero()]));
    }

    public function testRenderOrdersNamedOptionsAlphabetically(): void
    {
        $this->assertSame(
            'Length(max: 64, min: 2)',
            ExtraPropertyConstraintRenderer::render([new Assert\Length(['min' => 2, 'max' => 64])])
        );
    }

    public function testRenderBooleanValuesAsLiterals(): void
    {
        $this->assertSame('EqualTo(true)', ExtraPropertyConstraintRenderer::render([new Assert\EqualTo(['value' => true])]));
        $this->assertSame(
            "Choice(choices: ['a', 'b'], multiple: true)",
            ExtraPropertyConstraintRenderer::render([new Assert\Choice(['choices' => ['a', 'b'], 'multiple' => true])])
        );
    }

    public function testRenderNormalizesDoubleQuotedInputToSingleQuotes(): void
    {
        $constraints = ExtraPropertyConstraintParser::parse('TypedRegex("generic_name")')->getConstraints();

        $this->assertNotNull($constraints);
        $this->assertSame("TypedRegex('generic_name')", ExtraPropertyConstraintRenderer::render($constraints));
    }

    public function testCardSchemeRendersAsAList(): void
    {
        $this->assertSame(
            "CardScheme(['VISA', 'MASTERCARD'])",
            ExtraPropertyConstraintRenderer::render([new Assert\CardScheme(['VISA', 'MASTERCARD'])])
        );
    }

    // -- render(): composites ----------------------------------------------------------------

    public function testRenderACompositeWithIndentedNestedConstraints(): void
    {
        $rendered = ExtraPropertyConstraintRenderer::render([
            new Assert\All([new TypedRegex(['type' => 'generic_name']), new Assert\NotBlank()]),
        ]);

        $this->assertSame("All[\n  TypedRegex('generic_name'),\n  NotBlank\n]", $rendered);
    }

    public function testRenderACompositeWithItsOwnOptionsAheadOfItsChildren(): void
    {
        $rendered = ExtraPropertyConstraintRenderer::render([
            new Assert\AtLeastOneOf([
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
                'includeInternalMessages' => false,
            ]),
        ]);

        $this->assertSame("AtLeastOneOf(includeInternalMessages: false)[\n  NotBlank,\n  Email\n]", $rendered);
    }

    public function testRenderKeysCollectionChildrenByFieldName(): void
    {
        $rendered = ExtraPropertyConstraintRenderer::render([
            new Assert\Collection([
                'fields' => ['name' => new Assert\NotBlank(), 'code' => new Assert\Length(['max' => 5])],
                'allowExtraFields' => true,
            ]),
        ]);

        // Symfony wraps every field in a Required; the wrapper is rendered so the round-trip is exact.
        $this->assertSame(
            "Collection(allowExtraFields: true)[\n  name: Required[\n    NotBlank\n  ],\n  code: Required[\n    Length(max: 5)\n  ]\n]",
            $rendered
        );
    }

    // -- round-trip --------------------------------------------------------------------------

    public function testRoundTripsBareAllowlistedNames(): void
    {
        $raw = "NotBlank\nEmail\nUrl\nIsTrue\nCleanHtml";

        $constraints = $this->parseValid($raw);
        $this->assertInstanceOf(CleanHtml::class, $constraints[4]);

        $this->assertSame($raw, ExtraPropertyConstraintRenderer::render($constraints));
    }

    public function testRoundTripsParametricConstraints(): void
    {
        // Canonical (quoted) form round-trips exactly.
        $raw = "TypedRegex('generic_name')\nDefaultLanguage('video_link')\nChoice(['a', 'b', 'c'])\nType('integer')";

        $this->assertSame($raw, ExtraPropertyConstraintRenderer::render($this->parseValid($raw)));
    }

    public function testRoundTripsMixedStringAndNumberList(): void
    {
        $raw = "Choice(['01', '02', 3])";

        $this->assertSame($raw, ExtraPropertyConstraintRenderer::render($this->parseValid($raw)));
    }

    public function testRoundTripsComparisonConstraints(): void
    {
        $raw = "GreaterThan(5)\nLessThanOrEqual(100)\nDivisibleBy(3)";

        $this->assertSame($raw, ExtraPropertyConstraintRenderer::render($this->parseValid($raw)));
    }

    public function testRoundTripsComposites(): void
    {
        $raw = "DefaultLanguage('Video link')\nAll[\n  Url\n]";

        $this->assertSame($raw, ExtraPropertyConstraintRenderer::render($this->parseValid($raw)));
    }

    public function testRoundTripsNamedOptions(): void
    {
        $constraints = $this->parseValid('Length(min: 2, max: 64)');

        // Options render in deterministic (alphabetical) order, and that shape parses back identically.
        $rendered = ExtraPropertyConstraintRenderer::render($constraints);
        $this->assertSame('Length(max: 64, min: 2)', $rendered);
        $reparsed = $this->parseValid($rendered);
        $this->assertEquals($constraints, $reparsed);
        $this->assertSame($rendered, ExtraPropertyConstraintRenderer::render($reparsed));
    }

    public function testRoundTripsEscapedStrings(): void
    {
        $original = new Assert\EqualTo(['value' => "it's a \\ backslash"]);

        $rendered = ExtraPropertyConstraintRenderer::render([$original]);
        $this->assertSame("EqualTo('it\\'s a \\\\ backslash')", $rendered);

        $parsed = $this->parseValid($rendered);
        /** @var Assert\EqualTo $constraint */
        $constraint = $parsed[0];
        $this->assertSame("it's a \\ backslash", $constraint->value);
        $this->assertEquals([$original], $parsed);
    }

    public function testRoundTripsBooleanValues(): void
    {
        $raw = 'EqualTo(true)';

        $this->assertSame($raw, ExtraPropertyConstraintRenderer::render($this->parseValid($raw)));
    }

    public function testRoundTripsTheMultilangExampleFromTheDemoModule(): void
    {
        // The canonical multilang case: default-language requiredness + per-language Url validation.
        $constraints = [
            new DefaultLanguage(fieldName: 'Video link'),
            new Assert\All([new Assert\Url()]),
        ];

        $rendered = ExtraPropertyConstraintRenderer::render($constraints);
        $this->assertSame("DefaultLanguage('Video link')\nAll[\n  Url\n]", $rendered);

        $parsed = $this->parseValid($rendered);
        $this->assertInstanceOf(DefaultLanguage::class, $parsed[0]);
        $this->assertSame('Video link', $parsed[0]->fieldName);
        $this->assertInstanceOf(Assert\All::class, $parsed[1]);
        $this->assertInstanceOf(Assert\Url::class, array_values($parsed[1]->getNestedConstraints())[0]);
        $this->assertEquals($constraints, $parsed);
    }

    /**
     * @dataProvider losslessConstraintProvider
     *
     * @param list<Constraint> $constraints
     */
    public function testConstraintsSurviveTheStorageRoundTrip(array $constraints): void
    {
        $rendered = ExtraPropertyConstraintRenderer::render($constraints);
        $this->assertIsString($rendered);

        $decoded = $this->parseValid($rendered);

        $this->assertEquals($constraints, $decoded);
        // var_export keeps scalar types apart, which the loose object comparison would not.
        $this->assertSame(var_export($constraints, true), var_export($decoded, true));
    }

    /**
     * @return iterable<string, array{list<Constraint>}>
     */
    public static function losslessConstraintProvider(): iterable
    {
        yield 'plain constraints' => [[new Assert\NotBlank(), new Assert\Length(['min' => 2, 'max' => 64])]];

        yield 'composite with its own options' => [[
            new Assert\Collection([
                'fields' => ['name' => new Assert\NotBlank(), 'code' => new Assert\Length(['max' => 5])],
                'allowExtraFields' => true,
                'allowMissingFields' => true,
            ]),
        ]];

        yield 'composite list option' => [[
            new Assert\AtLeastOneOf([
                'constraints' => [new Assert\NotBlank(), new Assert\Email()],
                'includeInternalMessages' => false,
            ]),
        ]];

        yield 'explicit Optional wrapper' => [[
            new Assert\Collection(['fields' => [
                'a' => new Assert\Required([new Assert\NotBlank()]),
                'b' => new Assert\Optional([new Assert\Email()]),
            ]]),
        ]];

        yield 'nested composites' => [[
            new Assert\Sequentially([new Assert\NotBlank(), new Assert\All([new Assert\Url()])]),
        ]];

        yield 'scalar typing is preserved' => [[
            new Assert\Choice(['choices' => ['1', '2']]),
            new Assert\Count(['max' => 10]),
            new Assert\EqualTo(['value' => '5']),
            new Assert\LessThan(['value' => 5.5]),
        ]];
    }

    public function testEveryPublicConstraintSurvivesTheRoundTrip(): void
    {
        // Minimal valid options for the constraints that cannot be built bare.
        $samples = [
            'Length' => 'Length(max: 10)', 'Regex' => "Regex('/^a$/')", 'Range' => 'Range(min: 0, max: 10)',
            'EqualTo' => 'EqualTo(1)', 'NotEqualTo' => 'NotEqualTo(1)', 'IdenticalTo' => 'IdenticalTo(1)',
            'NotIdenticalTo' => 'NotIdenticalTo(1)', 'LessThan' => 'LessThan(1)', 'LessThanOrEqual' => 'LessThanOrEqual(1)',
            'GreaterThan' => 'GreaterThan(1)', 'GreaterThanOrEqual' => 'GreaterThanOrEqual(1)', 'DivisibleBy' => 'DivisibleBy(2)',
            'CardScheme' => "CardScheme(['VISA'])", 'Count' => 'Count(max: 10)', 'Type' => "Type('string')",
            'All' => 'All[ NotBlank ]', 'AtLeastOneOf' => 'AtLeastOneOf[ NotBlank, NotNull ]',
            'Collection' => 'Collection[ field: NotBlank ]', 'Sequentially' => 'Sequentially[ NotBlank, Length(max: 10) ]',
            'TypedRegex' => "TypedRegex('generic_name')",
        ];

        foreach (ExtraPropertyConstraintGrammar::getAllowedNames() as $name) {
            $constraints = $this->parseValid($samples[$name] ?? $name);

            $rendered = ExtraPropertyConstraintRenderer::render($constraints);
            $this->assertIsString($rendered, sprintf('%s must render.', $name));
            $decoded = ExtraPropertyConstraintParser::parse($rendered);

            $this->assertSame([], $decoded->getRejections(), sprintf('%s must decode without a rejection.', $name));
            $this->assertSame(
                var_export($constraints, true),
                var_export($decoded->getConstraints(), true),
                sprintf('%s must survive the storage round-trip.', $name)
            );
        }
    }

    // -- render(): refusals ------------------------------------------------------------------

    /**
     * @dataProvider refusedConstraintProvider
     */
    public function testConstraintsThatCannotBeStoredSafelyAreRefused(Constraint $constraint, string $expectedMessage): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        ExtraPropertyConstraintRenderer::render([$constraint]);
    }

    /**
     * @return iterable<string, array{Constraint, string}>
     */
    public static function refusedConstraintProvider(): iterable
    {
        // Executable and traversing options survive the rendering itself; the re-parse refuses them.
        $normalizer = new Assert\Length(['max' => 5]);
        $normalizer->normalizer = 'system';
        yield 'callable normalizer' => [$normalizer, '/not readable back.*"normalizer".*may execute a callable/s'];

        $callback = new Assert\Choice(['choices' => ['a']]);
        $callback->callback = 'system';
        yield 'callable callback' => [$callback, '/not readable back.*"callback".*may execute a callable/s'];

        $propertyPath = new Assert\LessThan(1);
        $propertyPath->propertyPath = 'secret';
        yield 'property path' => [$propertyPath, '/not readable back.*"propertyPath".*traverse the validated object/s'];

        $payload = new Assert\NotBlank();
        $payload->payload = ['anything'];
        yield 'payload' => [$payload, '/"payload" option is not supported/'];

        $groups = new Assert\NotBlank();
        $groups->groups = ['Custom'];
        yield 'custom validation group' => [$groups, '/Validation groups are not supported/'];

        yield 'object option' => [new Assert\LessThan(new DateTimeImmutable('2030-01-01')), '/"value" of constraint "LessThan" holds a DateTimeImmutable.*cannot represent/'];

        yield 'constraint outside the grammar' => [new RendererUnsupportedConstraint(), '/not part of the extra property constraint grammar/'];
    }

    public function testTheDefaultGroupIsNotAGroupRestriction(): void
    {
        // Symfony resolves the groups lazily; an explicit default group is what the lazy value would be.
        $constraint = new Assert\NotBlank();
        $constraint->groups = [Constraint::DEFAULT_GROUP];

        $this->assertSame('NotBlank', ExtraPropertyConstraintRenderer::render([$constraint]));
    }

    public function testACustomGroupNextToTheDefaultOneIsRefused(): void
    {
        $constraint = new Assert\NotBlank();
        $constraint->groups = [Constraint::DEFAULT_GROUP, 'custom'];

        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches('/Validation groups are not supported for extra property constraints \(constraint "NotBlank"\)/');

        ExtraPropertyConstraintRenderer::render([$constraint]);
    }

    public function testSelfReferencingCompositeIsRefused(): void
    {
        $composite = new Assert\All([new Assert\NotBlank()]);
        $composite->constraints[] = $composite;

        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessage(sprintf(
            'Constraint nesting exceeds the maximum depth of %d.',
            ExtraPropertyConstraintGrammar::MAX_NESTING_DEPTH
        ));

        ExtraPropertyConstraintRenderer::render([$composite]);
    }

    public function testANonConstraintItemIsRefused(): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessage(sprintf(
            'Extra property constraint at index 1 must extend %s, got string.',
            Constraint::class
        ));

        ExtraPropertyConstraintRenderer::render([new Assert\NotBlank(), 'Email']);
    }

    public function testAKeyedArrayOfConstraintsIsRefused(): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessage('Extra property constraints must be provided as a list.');

        ExtraPropertyConstraintRenderer::render(['first' => new Assert\NotBlank()]); // @phpstan-ignore-line intentionally invalid: keyed array to trigger the "must be a list" refusal
    }

    // -- helpers -----------------------------------------------------------------------------

    /**
     * Parses a DSL expected to be fully valid and returns its constraints.
     *
     * @return list<Constraint>
     */
    private function parseValid(string $raw): array
    {
        $decoded = ExtraPropertyConstraintParser::parse($raw);

        $this->assertFalse($decoded->hasRejections(), implode('; ', $decoded->getRejectionMessages()));
        $constraints = $decoded->getConstraints();
        $this->assertNotNull($constraints);

        return $constraints;
    }
}

/**
 * A perfectly valid Symfony constraint that is simply not part of the grammar.
 */
final class RendererUnsupportedConstraint extends Constraint
{
}
