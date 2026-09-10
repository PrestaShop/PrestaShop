<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Constraint;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Constraint\DecodedConstraints;
use PrestaShop\PrestaShop\Core\ExtraProperty\Constraint\ExtraPropertyConstraintGrammar;
use PrestaShop\PrestaShop\Core\ExtraProperty\Constraint\ExtraPropertyConstraintParser;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Unit tests for the ExtraPropertyConstraintParser.
 *
 * Covers the transform from the constraint DSL (one constraint per line, or comma-separated) to
 * Symfony Constraint instances. Parsing never throws: a token the grammar refuses becomes a
 * rejection carrying its index and line while its valid siblings stay active, so a tampered or
 * corrupt registry row cannot take a front-office page down. The tokenizer and token splitter are
 * public too, so the BO builder can stay pinned to the same quoting and delimiter rules.
 */
final class ExtraPropertyConstraintParserTest extends TestCase
{
    // -- parse(): basics ---------------------------------------------------------------------

    public function testParseReturnsNoConstraintsForNull(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse(null);

        $this->assertNull($decoded->getConstraints());
        $this->assertFalse($decoded->hasRejections());
    }

    public function testParseReturnsNoConstraintsForEmptyString(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('');

        $this->assertNull($decoded->getConstraints());
        $this->assertFalse($decoded->hasRejections());
    }

    public function testParseReturnsNoConstraintsForWhitespaceOnly(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse("  \n\t\n  ");

        $this->assertNull($decoded->getConstraints());
        $this->assertFalse($decoded->hasRejections());
    }

    public function testParseResolvesASingleNameToTheMatchingConstraintInstance(): void
    {
        $constraints = $this->parseValid('NotBlank');

        $this->assertCount(1, $constraints);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[0]);
    }

    public function testParseResolvesMultipleNamesPreservingOrder(): void
    {
        $constraints = $this->parseValid("Email\nUrl\nPositive");

        $this->assertCount(3, $constraints);
        $this->assertInstanceOf(Assert\Email::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Url::class, $constraints[1]);
        $this->assertInstanceOf(Assert\Positive::class, $constraints[2]);
    }

    public function testParseTrimsWhitespaceAroundEachName(): void
    {
        $constraints = $this->parseValid("  NotNull  \n\tEmail\t");

        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\NotNull::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Email::class, $constraints[1]);
    }

    public function testParseSkipsBlankLinesAndEmptyParts(): void
    {
        $this->assertCount(2, $this->parseValid("NotBlank\n\n\nEmail\n"));
        $this->assertCount(2, $this->parseValid("NotBlank\n\n, ,Email"));
    }

    // -- parse(): separators -----------------------------------------------------------------

    public function testParseAcceptsCommaSeparatedValues(): void
    {
        $this->assertCount(3, $this->parseValid('NotBlank, Email, Url'));
    }

    public function testParseAcceptsMixedNewlineAndCommaSeparators(): void
    {
        $this->assertCount(3, $this->parseValid("NotBlank, Email\nUrl"));
    }

    // -- parse(): rejections -----------------------------------------------------------------

    public function testAnUnknownNameIsRejectedAndItsSiblingsAreKept(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse("NotBlank\nBogus\nEmail");

        $constraints = $decoded->getConstraints();
        $this->assertNotNull($constraints);
        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Email::class, $constraints[1]);
        $this->assertSingleRejection($decoded, 1, 2, 'Unknown extra property constraint "Bogus"');
    }

    public function testANonAllowlistedSymfonyConstraintIsRejected(): void
    {
        // Callback/Expression are deliberately kept out of the allowlist (code execution, not serializable).
        $decoded = ExtraPropertyConstraintParser::parse('Callback');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Unknown extra property constraint "Callback"');
    }

    public function testAMultiOptionConstraintMissingItsRequiredOptionsIsRejected(): void
    {
        // Length is allowlisted but needs min/max — a bare name cannot satisfy them.
        $decoded = ExtraPropertyConstraintParser::parse('Length');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Invalid constraint "Length":');
    }

    public function testAMalformedTokenIsRejected(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('NotBlank(');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Malformed constraint "NotBlank("');
    }

    public function testTheUnknownRejectionNamesTheOffendingConstraintAndListsTheAllowedOnes(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('Bogus');

        $this->assertSingleRejection($decoded, 0, 1, '"Bogus"');
        $this->assertStringContainsString(
            'Allowed constraints: ' . implode(', ', ExtraPropertyConstraintGrammar::getAllowedNames()),
            $decoded->getRejections()[0]['reason']
        );
    }

    public function testParseIsCaseSensitive(): void
    {
        // The allowlist keys are the exact Symfony constraint short names — "notblank" must not match.
        $decoded = ExtraPropertyConstraintParser::parse('notblank');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Unknown extra property constraint "notblank"');
    }

    public function testAMissingRequiredValueIsRejected(): void
    {
        // TypedRegex requires a `type` — a bare name has no value to satisfy it.
        $decoded = ExtraPropertyConstraintParser::parse('TypedRegex');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Invalid constraint "TypedRegex":');
        $this->assertStringContainsString('"type"', $decoded->getRejections()[0]['reason']);
    }

    public function testAValueGivenToAValuelessConstraintIsRejected(): void
    {
        // NotBlank has no default option, so it cannot accept a parenthesised value.
        $decoded = ExtraPropertyConstraintParser::parse('NotBlank(foo)');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Constraint "NotBlank" does not accept a value.');
    }

    public function testEveryAllowedNameResolvesBareOrOnlyLacksAValue(): void
    {
        foreach (ExtraPropertyConstraintGrammar::getAllowedNames() as $name) {
            $decoded = ExtraPropertyConstraintParser::parse($name);

            if ($decoded->hasRejections()) {
                // Acceptable: the name is allowlisted but requires a value (e.g. TypedRegex, Type).
                // It must never be refused as unknown or malformed.
                $this->assertNull($decoded->getConstraints());
                $this->assertSingleRejection($decoded, 0, 1, sprintf('Invalid constraint "%s":', $name));

                continue;
            }

            $constraints = $decoded->getConstraints();
            $this->assertNotNull($constraints, sprintf('"%s" should resolve to a constraint', $name));
            $this->assertCount(1, $constraints);
            $this->assertInstanceOf(Constraint::class, $constraints[0]);
        }
    }

    // -- parse(): parametric -----------------------------------------------------------------

    public function testParseResolvesAScalarArgumentViaTheDefaultOption(): void
    {
        $constraints = $this->parseValid('TypedRegex(generic_name)');

        $this->assertInstanceOf(TypedRegex::class, $constraints[0]);
        $this->assertSame('generic_name', $constraints[0]->type);
    }

    public function testParseResolvesTheTypeConstraintArgument(): void
    {
        $constraints = $this->parseValid('Type(integer)');

        $this->assertInstanceOf(Assert\Type::class, $constraints[0]);
        $this->assertSame('integer', $constraints[0]->type);
    }

    public function testParseResolvesAnOptionalScalarArgument(): void
    {
        $constraints = $this->parseValid('DefaultLanguage(video_link)');

        $this->assertInstanceOf(DefaultLanguage::class, $constraints[0]);
        $this->assertSame('video_link', $constraints[0]->fieldName);
    }

    public function testParseResolvesAConstraintWithAnOptionalArgumentWhenBare(): void
    {
        $constraints = $this->parseValid('DefaultLanguage');

        $this->assertInstanceOf(DefaultLanguage::class, $constraints[0]);
        $this->assertSame('', $constraints[0]->fieldName);
    }

    public function testParseResolvesAListArgumentViaTheDefaultOption(): void
    {
        $constraints = $this->parseValid('Choice([a, b, c])');

        $this->assertInstanceOf(Assert\Choice::class, $constraints[0]);
        $this->assertSame(['a', 'b', 'c'], $constraints[0]->choices);
    }

    public function testTokenizerDoesNotSplitOnCommasInsideAnArgument(): void
    {
        $constraints = $this->parseValid('Choice([a, b, c]), NotBlank');

        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\Choice::class, $constraints[0]);
        $this->assertSame(['a', 'b', 'c'], $constraints[0]->choices);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[1]);
    }

    public function testUnquotedNumericListItemsAreCoercedToNumbers(): void
    {
        $constraints = $this->parseValid('Choice([01, 02, 3])');

        /** @var Assert\Choice $constraint */
        $constraint = $constraints[0];
        $this->assertSame([1, 2, 3], $constraint->choices);
    }

    public function testQuotedListItemsStayStrings(): void
    {
        // Codes like "01"/"02" survive when explicitly quoted; the bare 3 still becomes int.
        $constraints = $this->parseValid('Choice(["01", "02", 3])');

        /** @var Assert\Choice $constraint */
        $constraint = $constraints[0];
        $this->assertSame(['01', '02', 3], $constraint->choices);
    }

    public function testSingleAndDoubleQuotedStringListItemsAreEquivalent(): void
    {
        $single = $this->parseValid("Choice(['a', 'b', 'c'])");
        $double = $this->parseValid('Choice(["a", "b", "c"])');

        /** @var Assert\Choice $singleConstraint */
        $singleConstraint = $single[0];
        /** @var Assert\Choice $doubleConstraint */
        $doubleConstraint = $double[0];
        $this->assertSame(['a', 'b', 'c'], $singleConstraint->choices);
        $this->assertSame(['a', 'b', 'c'], $doubleConstraint->choices);
    }

    public function testAQuotedScalarValueStaysAString(): void
    {
        $quoted = $this->parseValid('EqualTo("5")');
        $bare = $this->parseValid('EqualTo(5)');

        /** @var Assert\EqualTo $quotedConstraint */
        $quotedConstraint = $quoted[0];
        /** @var Assert\EqualTo $bareConstraint */
        $bareConstraint = $bare[0];
        $this->assertSame('5', $quotedConstraint->value);
        $this->assertSame(5, $bareConstraint->value);
    }

    public function testADoubleQuotedScalarValueIsAccepted(): void
    {
        $constraints = $this->parseValid('TypedRegex("generic_name")');

        $this->assertInstanceOf(TypedRegex::class, $constraints[0]);
        $this->assertSame('generic_name', $constraints[0]->type);
    }

    public function testSingleAndDoubleQuotedScalarValuesAreEquivalent(): void
    {
        $single = $this->parseValid("EqualTo('hello')");
        $double = $this->parseValid('EqualTo("hello")');

        /** @var Assert\EqualTo $singleConstraint */
        $singleConstraint = $single[0];
        /** @var Assert\EqualTo $doubleConstraint */
        $doubleConstraint = $double[0];
        $this->assertSame('hello', $singleConstraint->value);
        $this->assertSame('hello', $doubleConstraint->value);
    }

    public function testTheTokenizerDoesNotSplitOnSeparatorsInsideAQuotedValue(): void
    {
        // The comma and bracket live inside a quoted string and must not break tokenizing/splitting.
        $constraints = $this->parseValid('Choice(["a,b", "c]d"]), NotBlank');

        $this->assertCount(2, $constraints);
        /** @var Assert\Choice $constraint */
        $constraint = $constraints[0];
        $this->assertSame(['a,b', 'c]d'], $constraint->choices);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[1]);
    }

    // -- parse(): comparison constraints (numeric coercion) ----------------------------------

    public function testParseCoercesAnIntegerComparisonValue(): void
    {
        $constraints = $this->parseValid('GreaterThan(5)');

        $this->assertInstanceOf(Assert\GreaterThan::class, $constraints[0]);
        $this->assertSame(5, $constraints[0]->value);
    }

    public function testParseCoercesAFloatComparisonValue(): void
    {
        $constraints = $this->parseValid('LessThan(5.5)');

        /** @var Assert\LessThan $constraint */
        $constraint = $constraints[0];
        $this->assertSame(5.5, $constraint->value);
    }

    public function testParseKeepsANonNumericComparisonValueAsString(): void
    {
        $constraints = $this->parseValid('EqualTo(hello)');

        /** @var Assert\EqualTo $constraint */
        $constraint = $constraints[0];
        $this->assertSame('hello', $constraint->value);
    }

    public function testTheCoercedNumericValueSatisfiesAStrictComparison(): void
    {
        // IdenticalTo uses === — only a coerced int matches an int field value.
        $constraints = $this->parseValid('IdenticalTo(10)');

        /** @var Assert\IdenticalTo $constraint */
        $constraint = $constraints[0];
        $this->assertSame(10, $constraint->value);
        $this->assertCount(0, Validation::createValidator()->validate(10, $constraint));
    }

    // -- parse(): identifier & format constraints --------------------------------------------

    public function testParseResolvesDateCssColorAndIban(): void
    {
        $constraints = $this->parseValid("Date\nCssColor\nIban");

        $this->assertInstanceOf(Assert\Date::class, $constraints[0]);
        $this->assertInstanceOf(Assert\CssColor::class, $constraints[1]);
        $this->assertInstanceOf(Assert\Iban::class, $constraints[2]);
    }

    public function testParseResolvesBareIdentifierAndFormatConstraints(): void
    {
        $constraints = $this->parseValid("Ulid\nCidr\nIsin\nNoSuspiciousCharacters\nBlank\nIsNull");

        $this->assertInstanceOf(Assert\Ulid::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Cidr::class, $constraints[1]);
        $this->assertInstanceOf(Assert\Isin::class, $constraints[2]);
        $this->assertInstanceOf(Assert\NoSuspiciousCharacters::class, $constraints[3]);
        $this->assertInstanceOf(Assert\Blank::class, $constraints[4]);
        $this->assertInstanceOf(Assert\IsNull::class, $constraints[5]);
    }

    public function testCardSchemeParsesAsAList(): void
    {
        $constraints = $this->parseValid('CardScheme([VISA, MASTERCARD])');

        $this->assertInstanceOf(Assert\CardScheme::class, $constraints[0]);
        $this->assertSame(['VISA', 'MASTERCARD'], $constraints[0]->schemes);
    }

    public function testCardSchemeRequiresItsListValue(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('CardScheme');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Invalid constraint "CardScheme":');
    }

    // -- parse(): named options ------------------------------------------------------------------

    public function testParseResolvesNamedOptions(): void
    {
        $constraints = $this->parseValid('Length(min: 2, max: 64)');

        $this->assertInstanceOf(Assert\Length::class, $constraints[0]);
        $this->assertSame(2, $constraints[0]->min);
        $this->assertSame(64, $constraints[0]->max);
    }

    public function testParseResolvesNamedOptionsWithListAndBoolValues(): void
    {
        $constraints = $this->parseValid("Choice(choices: ['a', 'b'], multiple: true, min: 1)");

        /** @var Assert\Choice $constraint */
        $constraint = $constraints[0];
        $this->assertSame(['a', 'b'], $constraint->choices);
        $this->assertTrue($constraint->multiple);
        $this->assertSame(1, $constraint->min);
    }

    public function testParseResolvesRangeNamedOptions(): void
    {
        $constraints = $this->parseValid('Range(min: 1, max: 10)');

        /** @var Assert\Range $constraint */
        $constraint = $constraints[0];
        $this->assertSame(1, $constraint->min);
        $this->assertSame(10, $constraint->max);
    }

    public function testAnUnknownNamedOptionIsRejected(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('Length(bogus: 2)');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Invalid constraint "Length(bogus: 2)":');
        $this->assertStringContainsString('"bogus"', $decoded->getRejections()[0]['reason']);
    }

    public function testAMalformedNamedOptionIsRejected(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('Length(min: 2, bogus)');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Invalid option "bogus" in constraint "Length(min: 2, bogus)"');
    }

    public function testAQuotedValueContainingAColonIsNotMistakenForANamedOption(): void
    {
        $constraints = $this->parseValid("EqualTo('a:b')");

        /** @var Assert\EqualTo $constraint */
        $constraint = $constraints[0];
        $this->assertSame('a:b', $constraint->value);
    }

    // -- parse(): forbidden options ----------------------------------------------------------------

    /**
     * A tampered registry row must not be able to reintroduce an executable option, and must not
     * take its valid neighbours down with it.
     */
    public function testTamperedStoredValueLosesOnlyTheOffendingConstraint(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse("Url\nLength(max: 5, normalizer: 'system')\nNotBlank");

        $constraints = $decoded->getConstraints();
        $this->assertNotNull($constraints);
        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\Url::class, $constraints[0]);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[1]);
        $this->assertSingleRejection($decoded, 1, 2, 'may execute a callable');
        $this->assertStringContainsString('"normalizer"', $decoded->getRejections()[0]['reason']);
    }

    public function testACallbackOptionIsRejected(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse("Choice(choices: ['a'], callback: 'system')");

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Option "callback" is not supported');
        $this->assertStringContainsString('may execute a callable', $decoded->getRejections()[0]['reason']);
    }

    /**
     * groups and payload are never carried by the format: refusing them at parse time surfaces the
     * error on the offending token (BO row, command) instead of as a save-time registry failure.
     *
     * @dataProvider nonRenderableOptionProvider
     */
    public function testANonRenderableOptionIsRejected(string $raw, string $option, string $reason): void
    {
        $decoded = ExtraPropertyConstraintParser::parse($raw);

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, sprintf('Option "%s" is not supported', $option));
        $this->assertStringContainsString($reason, $decoded->getRejections()[0]['reason']);
    }

    /**
     * @return iterable<string, array{string, string, string}>
     */
    public static function nonRenderableOptionProvider(): iterable
    {
        yield 'validation groups' => ["NotBlank(groups: ['custom'])", 'groups', 'only the default validation group applies'];
        yield 'payload' => ["NotBlank(payload: 'x')", 'payload', 'carries no payload'];
    }

    /**
     * @dataProvider propertyPathOptionProvider
     */
    public function testAPropertyPathOptionIsRejected(string $raw, string $option): void
    {
        $decoded = ExtraPropertyConstraintParser::parse($raw);

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, sprintf('Option "%s" is not supported', $option));
        $this->assertStringContainsString('may traverse the validated object', $decoded->getRejections()[0]['reason']);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function propertyPathOptionProvider(): iterable
    {
        yield 'comparison property path' => ["LessThan(propertyPath: 'secret')", 'propertyPath'];
        yield 'range bound property path' => ["Range(min: 1, minPropertyPath: 'secret')", 'minPropertyPath'];
    }

    // -- parse(): composites -----------------------------------------------------------------------

    public function testParseResolvesACompositeWithNestedConstraints(): void
    {
        $constraints = $this->parseValid('All[Url]');

        $this->assertInstanceOf(Assert\All::class, $constraints[0]);
        $nested = array_values($constraints[0]->getNestedConstraints());
        $this->assertCount(1, $nested);
        $this->assertInstanceOf(Assert\Url::class, $nested[0]);
    }

    public function testParseResolvesACompositeWithMultipleAndParametricChildren(): void
    {
        $constraints = $this->parseValid("All[\n  TypedRegex('generic_name'),\n  NotBlank\n]");

        $this->assertInstanceOf(Assert\All::class, $constraints[0]);
        $nested = array_values($constraints[0]->getNestedConstraints());
        $this->assertCount(2, $nested);
        $this->assertInstanceOf(TypedRegex::class, $nested[0]);
        $this->assertSame('generic_name', $nested[0]->type);
        $this->assertInstanceOf(Assert\NotBlank::class, $nested[1]);
    }

    public function testParseResolvesNestedComposites(): void
    {
        $constraints = $this->parseValid('AtLeastOneOf[Url, All[NotBlank]]');

        $this->assertInstanceOf(Assert\AtLeastOneOf::class, $constraints[0]);
        $nested = array_values($constraints[0]->getNestedConstraints());
        $this->assertInstanceOf(Assert\Url::class, $nested[0]);
        $this->assertInstanceOf(Assert\All::class, $nested[1]);
    }

    public function testParseResolvesACollectionWithKeyedChildren(): void
    {
        $constraints = $this->parseValid('Collection[name: NotBlank, code: Length(max: 5)]');

        $this->assertInstanceOf(Assert\Collection::class, $constraints[0]);
        $fields = $constraints[0]->fields;
        $this->assertArrayHasKey('name', $fields);
        $this->assertArrayHasKey('code', $fields);
        // Symfony wraps every field in a Required unless told otherwise.
        $this->assertInstanceOf(Assert\Required::class, $fields['name']);
        $this->assertInstanceOf(Assert\NotBlank::class, array_values($fields['name']->getNestedConstraints())[0]);
    }

    public function testParseResolvesACompositeCarryingItsOwnOptions(): void
    {
        $constraints = $this->parseValid('Collection(allowExtraFields: true)[ a: NotBlank ]');

        /** @var Assert\Collection $constraint */
        $constraint = $constraints[0];
        $this->assertTrue($constraint->allowExtraFields);
        $this->assertFalse($constraint->allowMissingFields);
        $this->assertArrayHasKey('a', $constraint->fields);
    }

    public function testBracketsOnANonCompositeConstraintAreRejected(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('NotBlank[Url]');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Constraint "NotBlank" does not accept nested constraints');
        $this->assertStringContainsString('(All, AtLeastOneOf, Collection, Sequentially)', $decoded->getRejections()[0]['reason']);
    }

    public function testKeyedChildrenOutsideCollectionAreRejected(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('All[name: NotBlank]');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Constraint "All" does not accept keyed nested constraints');
    }

    public function testAnUnknownNestedConstraintDropsTheWholeComposite(): void
    {
        // A partially decoded composite would silently weaken the validation it describes.
        $decoded = ExtraPropertyConstraintParser::parse('All[Url, Bogus]');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Unknown extra property constraint "Bogus"');
    }

    public function testAPositionalValueOnACompositeIsRejected(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('All(5)[Url]');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Composite constraint "All" only accepts named options');
    }

    public function testNestingBeyondTheBoundIsRejected(): void
    {
        $depth = ExtraPropertyConstraintGrammar::MAX_NESTING_DEPTH + 4;
        $decoded = ExtraPropertyConstraintParser::parse(str_repeat('All[', $depth) . 'NotBlank' . str_repeat(']', $depth));

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, sprintf(
            'Constraint nesting exceeds the maximum depth of %d.',
            ExtraPropertyConstraintGrammar::MAX_NESTING_DEPTH
        ));
    }

    /**
     * The wrappers Collection generates are part of the grammar but must never be accepted outside
     * a Collection field.
     */
    public function testCollectionWrappersStayInternal(): void
    {
        // Accepted where they belong: as the wrappers of a Collection's fields.
        $accepted = $this->parseValid('Collection[ a: Required[ NotBlank ], b: Optional[ Email ] ]');
        $this->assertInstanceOf(Assert\Collection::class, $accepted[0]);
        $this->assertInstanceOf(Assert\Required::class, $accepted[0]->fields['a']);
        $this->assertInstanceOf(Assert\Optional::class, $accepted[0]->fields['b']);

        // Refused anywhere else: they are grammar plumbing, not constraints a merchant picks.
        $topLevel = ExtraPropertyConstraintParser::parse('Required[ NotBlank ]');
        $this->assertNull($topLevel->getConstraints());
        $this->assertSingleRejection($topLevel, 0, 1, 'Unknown extra property constraint "Required"');

        $nestedInAnotherComposite = ExtraPropertyConstraintParser::parse('All[ Optional[ NotBlank ] ]');
        $this->assertNull($nestedInAnotherComposite->getConstraints());
        $this->assertSingleRejection($nestedInAnotherComposite, 0, 1, 'Unknown extra property constraint "Optional"');
    }

    // -- parse(): literals & escapes -------------------------------------------------------------

    public function testUnquotedLiteralsBecomeNativeTypes(): void
    {
        $constraints = $this->parseValid('EqualTo(true)');

        /** @var Assert\EqualTo $constraint */
        $constraint = $constraints[0];
        $this->assertTrue($constraint->value);
    }

    public function testQuotedLiteralsStayStrings(): void
    {
        $constraints = $this->parseValid("EqualTo('true')");

        /** @var Assert\EqualTo $constraint */
        $constraint = $constraints[0];
        $this->assertSame('true', $constraint->value);
    }

    public function testEscapedQuotesInsideAQuotedValueAreHonored(): void
    {
        $constraints = $this->parseValid("EqualTo('it\\'s')");

        /** @var Assert\EqualTo $constraint */
        $constraint = $constraints[0];
        $this->assertSame("it's", $constraint->value);
    }

    public function testUnrecognizedBackslashSequencesAreKeptVerbatim(): void
    {
        // A hand-typed regex keeps its \d — only \\ \' \" are escape sequences.
        $constraints = $this->parseValid("Regex('/^\\d+$/')");

        /** @var Assert\Regex $constraint */
        $constraint = $constraints[0];
        $this->assertSame('/^\d+$/', $constraint->pattern);
    }

    // -- rejections carry the offending line -------------------------------------------------------

    public function testRejectionsReportTheOffendingLine(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse("NotBlank\nEmail\nBogus");

        $this->assertSingleRejection($decoded, 2, 3, 'Unknown extra property constraint "Bogus"');
        // Consumers reporting the whole input get the located message; consumers already pointing
        // at the offending token (e.g. a constraint form row) read the bare reason.
        $this->assertStringStartsWith('Line 3: ', $decoded->getRejectionMessages()[0]);
        $this->assertSame('Line 3: ' . $decoded->getRejections()[0]['reason'], $decoded->getRejectionMessages()[0]);
    }

    public function testMultilineCompositeRejectionsReportItsStartingLine(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse("NotBlank\nAll[\n  Bogus\n]");

        $this->assertCount(1, $decoded->getConstraints() ?? []);
        $this->assertSingleRejection($decoded, 1, 2, 'Unknown extra property constraint "Bogus"');
        $this->assertStringStartsWith('Line 2: ', $decoded->getRejectionMessages()[0]);
    }

    public function testEveryRejectedTokenIsReportedOnItsOwn(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse("Bogus\nNotBlank\nNotBlank(foo)");

        $this->assertCount(1, $decoded->getConstraints() ?? []);
        $this->assertCount(2, $decoded->getRejections());
        $this->assertSame([0, 2], array_column($decoded->getRejections(), 'index'));
        $this->assertSame([1, 3], array_column($decoded->getRejections(), 'line'));
        $this->assertStringStartsWith('Line 1: ', $decoded->getRejectionMessages()[0]);
        $this->assertStringStartsWith('Line 3: ', $decoded->getRejectionMessages()[1]);
    }

    // -- parse(): stored value robustness --------------------------------------------------------------

    public function testMalformedStoredValueIsReportedWithoutThrowing(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse('}{ not a constraint');

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, 0, 1, 'Malformed constraint "}{ not a constraint"');
    }

    public function testOversizedStoredValueIsRefusedBeforeParsing(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse(
            str_repeat('NotBlank,', ExtraPropertyConstraintGrammar::MAX_RAW_LENGTH)
        );

        $this->assertNull($decoded->getConstraints());
        // A bound exceeded rejects the whole definition: no token can be located.
        $this->assertSingleRejection($decoded, null, null, 'maximum length');
        $this->assertSame([$decoded->getRejections()[0]['reason']], $decoded->getRejectionMessages());
    }

    public function testTooManyTopLevelConstraintsAreRefused(): void
    {
        $decoded = ExtraPropertyConstraintParser::parse(
            implode(',', array_fill(0, ExtraPropertyConstraintGrammar::MAX_TOKENS + 1, 'NotBlank'))
        );

        $this->assertNull($decoded->getConstraints());
        $this->assertSingleRejection($decoded, null, null, 'maximum of');
        $this->assertStringStartsNotWith('Line ', $decoded->getRejectionMessages()[0]);
    }

    /**
     * The persisted form is the DSL, so no PHP deserialization primitive may remain on this path.
     */
    public function testNoPhpDeserializationRemainsOnTheConstraintPath(): void
    {
        $sources = [
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Constraint/ExtraPropertyConstraintGrammar.php',
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Constraint/ExtraPropertyConstraintParser.php',
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Constraint/ExtraPropertyConstraintRenderer.php',
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Definition/ExtraPropertyDefinition.php',
            __DIR__ . '/../../../../../src/Core/ExtraProperty/Definition/ExtraPropertyDefinitionRepository.php',
        ];

        foreach ($sources as $source) {
            $this->assertFileExists($source);
            $code = (string) file_get_contents($source);
            // Strip comments so the documentation explaining the absence does not trip the check.
            $code = (string) preg_replace('#/\*.*?\*/|//[^\n]*#s', '', $code);

            $this->assertDoesNotMatchRegularExpression(
                '/\b(un)?serialize\s*\(/',
                $code,
                sprintf('%s must not call serialize()/unserialize().', basename($source))
            );
        }
    }

    // -- parity fixtures with the client-side tail lexer -----------------------------------------

    /**
     * PARITY FIXTURES — mirror of "parity fixtures with the PHP parser" in
     * admin-dev/themes/new-theme/tests/pages/extra-property-definition/constraint-dsl.spec.js.
     * The same tails are lexed by the TS typed-option editor: a quoting/splitting rule changed on
     * one side without the other makes the sibling suite fail, surfacing the drift in CI.
     *
     * @dataProvider tailParityProvider
     */
    public function testTailQuotingParityWithTheClientLexer(string $tail, string $decoded): void
    {
        $constraints = $this->parseValid(sprintf('EqualTo(%s)', $tail));

        $this->assertCount(1, $constraints);
        /** @var Assert\EqualTo $constraint */
        $constraint = $constraints[0];
        $this->assertSame($decoded, $constraint->value);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function tailParityProvider(): array
    {
        return [
            'single quotes' => ["'simple'", 'simple'],
            'double quotes' => ['"double"', 'double'],
            'escaped single quote' => ["'it\\'s'", "it's"],
            'escaped double quotes' => ['"she said \\"hi\\""', 'she said "hi"'],
            'escaped backslash' => ["'back\\\\slash'", 'back\\slash'],
            'separator inside quotes' => ["'a, b'", 'a, b'],
            'delimiters inside quotes' => ["'with (parens) and [brackets]'", 'with (parens) and [brackets]'],
            'unquoted string' => ['unquoted_text', 'unquoted_text'],
        ];
    }

    /**
     * The named-options half of the parity fixtures (see tailParityProvider()).
     */
    public function testNamedOptionsParityWithTheClientLexer(): void
    {
        $constraints = $this->parseValid("Length(min: 2, max: 64)\nChoice(choices: ['a', 'b,c'], multiple: true)");

        /** @var Assert\Length $length */
        $length = $constraints[0];
        $this->assertSame(2, $length->min);
        $this->assertSame(64, $length->max);
        /** @var Assert\Choice $choice */
        $choice = $constraints[1];
        $this->assertSame(['a', 'b,c'], $choice->choices);
        $this->assertTrue($choice->multiple);
    }

    // -- tokenize() --------------------------------------------------------------------------------

    public function testTokenizeReturnsTopLevelTokensWithTheirStartingLine(): void
    {
        $tokens = ExtraPropertyConstraintParser::tokenize("NotBlank\nAll[\n  Url\n],Email\n\n  Length(max: 5)");

        $this->assertSame(
            [
                ['NotBlank', 1],
                ["All[\n  Url\n]", 2],
                ['Email', 4],
                ['Length(max: 5)', 6],
            ],
            $tokens
        );
    }

    public function testTokenizeKeepsSeparatorsInsideQuotedValues(): void
    {
        $tokens = ExtraPropertyConstraintParser::tokenize("EqualTo('a,\nb'), NotBlank");

        $this->assertSame(
            [
                ["EqualTo('a,\nb')", 1],
                ['NotBlank', 2],
            ],
            $tokens
        );
    }

    public function testTokenizeAcceptsADefinitionAtTheTokenBound(): void
    {
        $tokens = ExtraPropertyConstraintParser::tokenize(
            implode(',', array_fill(0, ExtraPropertyConstraintGrammar::MAX_TOKENS, 'NotBlank'))
        );

        $this->assertCount(ExtraPropertyConstraintGrammar::MAX_TOKENS, $tokens);
    }

    public function testTokenizeRefusesAnOversizedDefinition(): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessage(sprintf(
            'Constraint definition exceeds the maximum length of %d characters.',
            ExtraPropertyConstraintGrammar::MAX_RAW_LENGTH
        ));

        ExtraPropertyConstraintParser::tokenize(str_repeat('a', ExtraPropertyConstraintGrammar::MAX_RAW_LENGTH + 1));
    }

    public function testTokenizeRefusesTooManyTopLevelConstraints(): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);
        $this->expectExceptionMessage(sprintf(
            'Constraint definition exceeds the maximum of %d top-level constraints.',
            ExtraPropertyConstraintGrammar::MAX_TOKENS
        ));

        ExtraPropertyConstraintParser::tokenize(
            implode(',', array_fill(0, ExtraPropertyConstraintGrammar::MAX_TOKENS + 1, 'NotBlank'))
        );
    }

    // -- splitToken() ------------------------------------------------------------------------------

    /**
     * @dataProvider splitTokenProvider
     *
     * @param array{name: string, options: string|null, children: string|null} $expected
     */
    public function testSplitTokenSeparatesNameOptionsAndChildren(string $token, array $expected): void
    {
        $this->assertSame($expected, ExtraPropertyConstraintParser::splitToken($token));
    }

    /**
     * @return iterable<string, array{string, array{name: string, options: string|null, children: string|null}}>
     */
    public static function splitTokenProvider(): iterable
    {
        yield 'bare name' => ['NotBlank', ['name' => 'NotBlank', 'options' => null, 'children' => null]];
        yield 'options tail, surrounding whitespace trimmed' => ['  Length(min: 2) ', ['name' => 'Length', 'options' => 'min: 2', 'children' => null]];
        yield 'children tail' => ['All[Url]', ['name' => 'All', 'options' => null, 'children' => 'Url']];
        yield 'both tails' => [
            'Collection(allowExtraFields: true)[ name: NotBlank ]',
            ['name' => 'Collection', 'options' => 'allowExtraFields: true', 'children' => ' name: NotBlank '],
        ];
        yield 'delimiter inside a quoted value' => ["TypedRegex('a)b')", ['name' => 'TypedRegex', 'options' => "'a)b'", 'children' => null]];
    }

    /**
     * @dataProvider malformedTokenProvider
     */
    public function testSplitTokenReturnsNullForAMalformedToken(string $token): void
    {
        $this->assertNull(ExtraPropertyConstraintParser::splitToken($token));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function malformedTokenProvider(): iterable
    {
        yield 'unclosed parenthesis' => ['NotBlank('];
        yield 'unclosed bracket' => ['All[Url'];
        yield 'missing name' => ['(foo)'];
        yield 'trailing text' => ['NotBlank trailing'];
        yield 'empty' => [''];
    }

    // -- helpers -----------------------------------------------------------------------------------

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

    /**
     * Asserts the decoding reported exactly one rejection, located on the given token, whose reason
     * carries the expected substring.
     */
    private function assertSingleRejection(DecodedConstraints $decoded, ?int $index, ?int $line, string $reasonSubstring): void
    {
        $this->assertTrue($decoded->hasRejections());
        $this->assertCount(1, $decoded->getRejections());
        $rejection = $decoded->getRejections()[0];
        $this->assertSame($index, $rejection['index']);
        $this->assertSame($line, $rejection['line']);
        $this->assertStringContainsString($reasonSubstring, $rejection['reason']);
    }
}
