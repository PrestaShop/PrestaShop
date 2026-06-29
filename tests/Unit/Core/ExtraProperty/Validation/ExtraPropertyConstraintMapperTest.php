<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Validation;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyConstraintException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\UnknownExtraPropertyConstraintException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Unit tests for the ExtraPropertyConstraintMapper.
 *
 * Covers the two transforms between the BO "Validation" textarea (one constraint per line, or
 * comma-separated) and Symfony Constraint instances — fromNames()/toNames() — plus getAllowedNames().
 * The two directions are intentionally asymmetric: fromNames() is whitelist-gated and rejects unknown
 * names / bad arguments with exceptions, whereas toNames() is lossless and renders ANY constraint
 * (including composites and non-whitelisted ones) so the read-only view page can display module-attached
 * constraints. Names/arguments fromNames() can parse round-trip exactly.
 */
class ExtraPropertyConstraintMapperTest extends TestCase
{
    // -- fromNames(): basics -----------------------------------------------------------------

    public function testFromNamesReturnsNullForNull(): void
    {
        $this->assertNull(ExtraPropertyConstraintMapper::fromNames(null));
    }

    public function testFromNamesReturnsNullForEmptyString(): void
    {
        $this->assertNull(ExtraPropertyConstraintMapper::fromNames(''));
    }

    public function testFromNamesReturnsNullForWhitespaceOnly(): void
    {
        $this->assertNull(ExtraPropertyConstraintMapper::fromNames("  \n\t\n  "));
    }

    public function testFromNamesResolvesASingleNameToTheMatchingConstraintInstance(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('NotBlank');

        $this->assertNotNull($constraints);
        $this->assertCount(1, $constraints);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[0]);
    }

    public function testFromNamesResolvesMultipleNamesPreservingOrder(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames("Email\nUrl\nPositive");

        $this->assertNotNull($constraints);
        $this->assertCount(3, $constraints);
        $this->assertInstanceOf(Assert\Email::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Url::class, $constraints[1]);
        $this->assertInstanceOf(Assert\Positive::class, $constraints[2]);
    }

    public function testFromNamesTrimsWhitespaceAroundEachName(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames("  NotNull  \n\tEmail\t");

        $this->assertNotNull($constraints);
        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\NotNull::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Email::class, $constraints[1]);
    }

    public function testFromNamesSkipsBlankLines(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames("NotBlank\n\n\nEmail\n");

        $this->assertNotNull($constraints);
        $this->assertCount(2, $constraints);
    }

    // -- fromNames(): separators -------------------------------------------------------------

    public function testFromNamesAcceptsCommaSeparatedValues(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('NotBlank, Email, Url');

        $this->assertNotNull($constraints);
        $this->assertCount(3, $constraints);
    }

    public function testFromNamesAcceptsMixedNewlineAndCommaSeparators(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames("NotBlank, Email\nUrl");

        $this->assertNotNull($constraints);
        $this->assertCount(3, $constraints);
    }

    // -- fromNames(): rejection --------------------------------------------------------------

    public function testFromNamesThrowsOnAnUnknownName(): void
    {
        $this->expectException(UnknownExtraPropertyConstraintException::class);

        ExtraPropertyConstraintMapper::fromNames("NotBlank\nBogus\nEmail");
    }

    public function testFromNamesThrowsOnANonWhitelistedConstraint(): void
    {
        // Length/Range/Regex are real Symfony constraints deliberately kept out of the textarea
        // whitelist (Length/Range need keyed options; Regex carries unsafe delimiters).
        $this->expectException(UnknownExtraPropertyConstraintException::class);

        ExtraPropertyConstraintMapper::fromNames('Length');
    }

    public function testFromNamesThrowsOnAMalformedToken(): void
    {
        $this->expectException(UnknownExtraPropertyConstraintException::class);

        ExtraPropertyConstraintMapper::fromNames('NotBlank(');
    }

    public function testTheUnknownExceptionNamesTheOffendingConstraint(): void
    {
        $this->expectException(UnknownExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches('/Bogus/');

        ExtraPropertyConstraintMapper::fromNames('Bogus');
    }

    public function testFromNamesIsCaseSensitive(): void
    {
        // The whitelist keys are the exact Symfony constraint short names — "notblank" must not match.
        $this->expectException(UnknownExtraPropertyConstraintException::class);

        ExtraPropertyConstraintMapper::fromNames('notblank');
    }

    public function testFromNamesThrowsWhenARequiredValueIsMissing(): void
    {
        // TypedRegex requires a `type` — a bare name has no value to satisfy it.
        $this->expectException(InvalidExtraPropertyConstraintException::class);

        ExtraPropertyConstraintMapper::fromNames('TypedRegex');
    }

    public function testFromNamesThrowsWhenAValueIsGivenToAValuelessConstraint(): void
    {
        // NotBlank has no default option, so it cannot accept a parenthesised value.
        $this->expectException(InvalidExtraPropertyConstraintException::class);

        ExtraPropertyConstraintMapper::fromNames('NotBlank(foo)');
    }

    public function testFromNamesResolvesOrRejectsEveryAllowedName(): void
    {
        foreach (ExtraPropertyConstraintMapper::getAllowedNames() as $name) {
            try {
                $constraints = ExtraPropertyConstraintMapper::fromNames($name);
            } catch (InvalidExtraPropertyConstraintException) {
                // Acceptable: the name is whitelisted but requires a value (e.g. TypedRegex, Type).
                continue;
            }

            $this->assertNotNull($constraints, sprintf('"%s" should resolve to a constraint', $name));
            $this->assertCount(1, $constraints);
            $this->assertInstanceOf(Constraint::class, $constraints[0]);
        }
    }

    // -- fromNames(): parametric -------------------------------------------------------------

    public function testFromNamesResolvesAScalarArgumentViaTheDefaultOption(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('TypedRegex(generic_name)');

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(TypedRegex::class, $constraints[0]);
        $this->assertSame('generic_name', $constraints[0]->type);
    }

    public function testFromNamesResolvesTheTypeConstraintArgument(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('Type(integer)');

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(Assert\Type::class, $constraints[0]);
        $this->assertSame('integer', $constraints[0]->type);
    }

    public function testFromNamesResolvesAnOptionalScalarArgument(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('DefaultLanguage(video_link)');

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(DefaultLanguage::class, $constraints[0]);
        $this->assertSame('video_link', $constraints[0]->fieldName);
    }

    public function testFromNamesResolvesAConstraintWithAnOptionalArgumentWhenBare(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('DefaultLanguage');

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(DefaultLanguage::class, $constraints[0]);
        $this->assertSame('', $constraints[0]->fieldName);
    }

    public function testFromNamesResolvesAListArgumentViaTheDefaultOption(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('Choice([a, b, c])');

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(Assert\Choice::class, $constraints[0]);
        $this->assertSame(['a', 'b', 'c'], $constraints[0]->choices);
    }

    public function testTokenizerDoesNotSplitOnCommasInsideAnArgument(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('Choice([a, b, c]), NotBlank');

        $this->assertNotNull($constraints);
        $this->assertCount(2, $constraints);
        $this->assertInstanceOf(Assert\Choice::class, $constraints[0]);
        $this->assertSame(['a', 'b', 'c'], $constraints[0]->choices);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[1]);
    }

    public function testUnquotedNumericListItemsAreCoercedToNumbers(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('Choice([01, 02, 3])');

        $this->assertNotNull($constraints);
        /** @var Assert\Choice $constraint */
        $constraint = $constraints[0];
        $this->assertSame([1, 2, 3], $constraint->choices);
    }

    public function testQuotedListItemsStayStrings(): void
    {
        // Codes like "01"/"02" survive when explicitly quoted; the bare 3 still becomes int.
        $constraints = ExtraPropertyConstraintMapper::fromNames('Choice(["01", "02", 3])');

        $this->assertNotNull($constraints);
        /** @var Assert\Choice $constraint */
        $constraint = $constraints[0];
        $this->assertSame(['01', '02', 3], $constraint->choices);
    }

    public function testSingleAndDoubleQuotedStringListItemsAreEquivalent(): void
    {
        $single = ExtraPropertyConstraintMapper::fromNames("Choice(['a', 'b', 'c'])");
        $double = ExtraPropertyConstraintMapper::fromNames('Choice(["a", "b", "c"])');

        $this->assertNotNull($single);
        $this->assertNotNull($double);
        /** @var Assert\Choice $singleConstraint */
        $singleConstraint = $single[0];
        /** @var Assert\Choice $doubleConstraint */
        $doubleConstraint = $double[0];
        $this->assertSame(['a', 'b', 'c'], $singleConstraint->choices);
        $this->assertSame(['a', 'b', 'c'], $doubleConstraint->choices);
    }

    public function testAQuotedScalarValueStaysAString(): void
    {
        $quoted = ExtraPropertyConstraintMapper::fromNames('EqualTo("5")');
        $bare = ExtraPropertyConstraintMapper::fromNames('EqualTo(5)');

        $this->assertNotNull($quoted);
        $this->assertNotNull($bare);
        /** @var Assert\EqualTo $quotedConstraint */
        $quotedConstraint = $quoted[0];
        /** @var Assert\EqualTo $bareConstraint */
        $bareConstraint = $bare[0];
        $this->assertSame('5', $quotedConstraint->value);
        $this->assertSame(5, $bareConstraint->value);
    }

    public function testADoubleQuotedScalarValueIsAccepted(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('TypedRegex("generic_name")');

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(TypedRegex::class, $constraints[0]);
        $this->assertSame('generic_name', $constraints[0]->type);
        // Double-quoted input is normalized to single quotes on output.
        $this->assertSame("TypedRegex('generic_name')", ExtraPropertyConstraintMapper::toNames($constraints));
    }

    public function testSingleAndDoubleQuotedScalarValuesAreEquivalent(): void
    {
        $single = ExtraPropertyConstraintMapper::fromNames("EqualTo('hello')");
        $double = ExtraPropertyConstraintMapper::fromNames('EqualTo("hello")');

        $this->assertNotNull($single);
        $this->assertNotNull($double);
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
        $constraints = ExtraPropertyConstraintMapper::fromNames('Choice(["a,b", "c]d"]), NotBlank');

        $this->assertNotNull($constraints);
        $this->assertCount(2, $constraints);
        /** @var Assert\Choice $constraint */
        $constraint = $constraints[0];
        $this->assertSame(['a,b', 'c]d'], $constraint->choices);
        $this->assertInstanceOf(Assert\NotBlank::class, $constraints[1]);
    }

    // -- fromNames(): comparison constraints (numeric coercion) ------------------------------

    public function testFromNamesCoercesAnIntegerComparisonValue(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('GreaterThan(5)');

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(Assert\GreaterThan::class, $constraints[0]);
        $this->assertSame(5, $constraints[0]->value);
    }

    public function testFromNamesCoercesAFloatComparisonValue(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('LessThan(5.5)');

        $this->assertNotNull($constraints);
        /** @var Assert\LessThan $constraint */
        $constraint = $constraints[0];
        $this->assertSame(5.5, $constraint->value);
    }

    public function testFromNamesKeepsANonNumericComparisonValueAsString(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('EqualTo(hello)');

        $this->assertNotNull($constraints);
        /** @var Assert\EqualTo $constraint */
        $constraint = $constraints[0];
        $this->assertSame('hello', $constraint->value);
    }

    public function testTheCoercedNumericValueSatisfiesAStrictComparison(): void
    {
        // IdenticalTo uses === — only a coerced int matches an int field value.
        $constraints = ExtraPropertyConstraintMapper::fromNames('IdenticalTo(10)');

        $this->assertNotNull($constraints);
        /** @var Assert\IdenticalTo $constraint */
        $constraint = $constraints[0];
        $this->assertSame(10, $constraint->value);
        $this->assertCount(0, Validation::createValidator()->validate(10, $constraint));
    }

    // -- toNames(): basics -------------------------------------------------------------------

    public function testToNamesReturnsNullForNull(): void
    {
        $this->assertNull(ExtraPropertyConstraintMapper::toNames(null));
    }

    public function testToNamesReturnsNullForEmptyArray(): void
    {
        $this->assertNull(ExtraPropertyConstraintMapper::toNames([]));
    }

    public function testToNamesFormatsASingleConstraintToItsShortName(): void
    {
        $this->assertSame('NotBlank', ExtraPropertyConstraintMapper::toNames([new Assert\NotBlank()]));
    }

    public function testToNamesFormatsMultipleConstraintsOnePerLinePreservingOrder(): void
    {
        $names = ExtraPropertyConstraintMapper::toNames([
            new Assert\Email(),
            new Assert\Url(),
            new Assert\PositiveOrZero(),
        ]);

        $this->assertSame("Email\nUrl\nPositiveOrZero", $names);
    }

    // -- toNames(): lossless display of non-whitelisted constraints --------------------------

    public function testToNamesRendersConstraintsOutsideTheWhitelist(): void
    {
        $names = ExtraPropertyConstraintMapper::toNames([
            new Assert\NotBlank(),
            new Assert\Length(['max' => 10]),
            new Assert\Email(),
        ]);

        // Length has no default option → bare name; nothing is dropped any more.
        $this->assertSame("NotBlank\nLength\nEmail", $names);
    }

    public function testToNamesRendersANonWhitelistedConstraintWithItsPrimaryOption(): void
    {
        $names = ExtraPropertyConstraintMapper::toNames([new Assert\Regex(['pattern' => '/^\d+$/'])]);

        $this->assertSame("Regex('/^\d+$/')", $names);
    }

    public function testToNamesReturnsRenderedStringWhenAllConstraintsAreNonWhitelisted(): void
    {
        $names = ExtraPropertyConstraintMapper::toNames([
            new Assert\Length(['max' => 10]),
            new Assert\Regex(['pattern' => '/^\d+$/']),
        ]);

        $this->assertSame("Length\nRegex('/^\d+$/')", $names);
    }

    // -- toNames(): parametric rendering -----------------------------------------------------

    public function testToNamesRendersAScalarPrimaryOptionQuoted(): void
    {
        $this->assertSame(
            "TypedRegex('generic_name')",
            ExtraPropertyConstraintMapper::toNames([new TypedRegex(['type' => 'generic_name'])])
        );
    }

    public function testToNamesRendersAListPrimaryOptionWithQuotedStrings(): void
    {
        $this->assertSame(
            "Choice(['a', 'b', 'c'])",
            ExtraPropertyConstraintMapper::toNames([new Assert\Choice(['choices' => ['a', 'b', 'c']])])
        );
    }

    public function testToNamesRendersNumericListItemsWithoutQuotes(): void
    {
        $this->assertSame(
            "Choice(['01', '02', 3])",
            ExtraPropertyConstraintMapper::toNames([new Assert\Choice(['choices' => ['01', '02', 3]])])
        );
    }

    public function testToNamesRendersTheTypeConstraintScalarAndList(): void
    {
        $this->assertSame(
            "Type('integer')",
            ExtraPropertyConstraintMapper::toNames([new Assert\Type(['type' => 'integer'])])
        );
        $this->assertSame(
            "Type(['integer', 'string'])",
            ExtraPropertyConstraintMapper::toNames([new Assert\Type(['type' => ['integer', 'string']])])
        );
    }

    public function testToNamesRendersAnEmptyOptionalArgumentAsBareName(): void
    {
        // DefaultLanguage with an empty fieldName must not render "DefaultLanguage()".
        $this->assertSame('DefaultLanguage', ExtraPropertyConstraintMapper::toNames([new DefaultLanguage()]));
        $this->assertSame(
            "DefaultLanguage('video_link')",
            ExtraPropertyConstraintMapper::toNames([new DefaultLanguage(fieldName: 'video_link')])
        );
    }

    // -- toNames(): composites ---------------------------------------------------------------

    public function testToNamesRendersACompositeWithIndentedNestedConstraints(): void
    {
        $names = ExtraPropertyConstraintMapper::toNames([
            new Assert\All([new TypedRegex(['type' => 'generic_name']), new Assert\NotBlank()]),
        ]);

        $expected = "All[\n  TypedRegex('generic_name'),\n  NotBlank\n]";
        $this->assertSame($expected, $names);
    }

    // -- round-trip --------------------------------------------------------------------------

    public function testRoundTripsBareWhitelistedNames(): void
    {
        $raw = "NotBlank\nEmail\nUrl\nIsTrue\nCleanHtml";

        $constraints = ExtraPropertyConstraintMapper::fromNames($raw);
        $this->assertNotNull($constraints);
        $this->assertInstanceOf(CleanHtml::class, $constraints[4]);

        $this->assertSame($raw, ExtraPropertyConstraintMapper::toNames($constraints));
    }

    public function testRoundTripsParametricConstraints(): void
    {
        // Canonical (quoted) form round-trips exactly.
        $raw = "TypedRegex('generic_name')\nDefaultLanguage('video_link')\nChoice(['a', 'b', 'c'])\nType('integer')";

        $constraints = ExtraPropertyConstraintMapper::fromNames($raw);
        $this->assertNotNull($constraints);

        $this->assertSame($raw, ExtraPropertyConstraintMapper::toNames($constraints));
    }

    public function testRoundTripsMixedStringAndNumberList(): void
    {
        $raw = "Choice(['01', '02', 3])";

        $constraints = ExtraPropertyConstraintMapper::fromNames($raw);
        $this->assertNotNull($constraints);

        $this->assertSame($raw, ExtraPropertyConstraintMapper::toNames($constraints));
    }

    public function testToNamesRendersAComparisonConstraint(): void
    {
        $this->assertSame(
            'GreaterThan(5)',
            ExtraPropertyConstraintMapper::toNames([new Assert\GreaterThan(['value' => 5])])
        );
    }

    public function testRoundTripsComparisonConstraints(): void
    {
        $raw = "GreaterThan(5)\nLessThanOrEqual(100)\nDivisibleBy(3)";

        $constraints = ExtraPropertyConstraintMapper::fromNames($raw);
        $this->assertNotNull($constraints);

        $this->assertSame($raw, ExtraPropertyConstraintMapper::toNames($constraints));
    }

    public function testFromNamesResolvesNewlyWhitelistedSymfonyConstraints(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames("Date\nCssColor\nIban");

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(Assert\Date::class, $constraints[0]);
        $this->assertInstanceOf(Assert\CssColor::class, $constraints[1]);
        $this->assertInstanceOf(Assert\Iban::class, $constraints[2]);
    }

    public function testFromNamesResolvesBareIdentifierAndFormatConstraints(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames("Ulid\nCidr\nIsin\nNoSuspiciousCharacters\nBlank\nIsNull");

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(Assert\Ulid::class, $constraints[0]);
        $this->assertInstanceOf(Assert\Cidr::class, $constraints[1]);
        $this->assertInstanceOf(Assert\Isin::class, $constraints[2]);
        $this->assertInstanceOf(Assert\NoSuspiciousCharacters::class, $constraints[3]);
        $this->assertInstanceOf(Assert\Blank::class, $constraints[4]);
        $this->assertInstanceOf(Assert\IsNull::class, $constraints[5]);
    }

    public function testCardSchemeParsesAndRendersAsAList(): void
    {
        $constraints = ExtraPropertyConstraintMapper::fromNames('CardScheme([VISA, MASTERCARD])');

        $this->assertNotNull($constraints);
        $this->assertInstanceOf(Assert\CardScheme::class, $constraints[0]);
        $this->assertSame(['VISA', 'MASTERCARD'], $constraints[0]->schemes);
        $this->assertSame("CardScheme(['VISA', 'MASTERCARD'])", ExtraPropertyConstraintMapper::toNames($constraints));
    }

    public function testCardSchemeRequiresItsListValue(): void
    {
        $this->expectException(InvalidExtraPropertyConstraintException::class);

        ExtraPropertyConstraintMapper::fromNames('CardScheme');
    }

    // -- getAllowedNames() -------------------------------------------------------------------

    public function testGetAllowedNamesReturnsTheWhitelistKeys(): void
    {
        $this->assertSame(
            [
                'NotBlank',
                'NotNull',
                'Blank',
                'IsNull',
                'Email',
                'Url',
                'Json',
                'Uuid',
                'Ulid',
                'Ip',
                'Cidr',
                'Hostname',
                'CssColor',
                'NoSuspiciousCharacters',
                'Date',
                'DateTime',
                'Time',
                'Timezone',
                'Positive',
                'PositiveOrZero',
                'Negative',
                'NegativeOrZero',
                'Luhn',
                'EqualTo',
                'NotEqualTo',
                'IdenticalTo',
                'NotIdenticalTo',
                'LessThan',
                'LessThanOrEqual',
                'GreaterThan',
                'GreaterThanOrEqual',
                'DivisibleBy',
                'IsTrue',
                'IsFalse',
                'Iban',
                'Bic',
                'Isbn',
                'Issn',
                'Isin',
                'CardScheme',
                'Country',
                'Language',
                'Locale',
                'Currency',
                'Choice',
                'Type',
                'TypedRegex',
                'DefaultLanguage',
                'CleanHtml',
            ],
            ExtraPropertyConstraintMapper::getAllowedNames()
        );
    }
}
