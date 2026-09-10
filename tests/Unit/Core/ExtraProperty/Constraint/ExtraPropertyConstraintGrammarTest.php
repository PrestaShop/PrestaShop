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
use PrestaShop\PrestaShop\Core\ExtraProperty\Constraint\ExtraPropertyConstraintGrammar;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\UnknownExtraPropertyConstraintException;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Unit tests for the ExtraPropertyConstraintGrammar: the allowlist that gates which Symfony
 * constraints the DSL may name, the options it refuses, and the bounds a definition must respect.
 * The parser and the renderer both read their rules here, so this is where the vocabulary is pinned.
 */
final class ExtraPropertyConstraintGrammarTest extends TestCase
{
    // -- allowlist -------------------------------------------------------------------------------

    public function testGetAllowedNamesReturnsTheAllowlistKeys(): void
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
                'Length',
                'Regex',
                'Date',
                'DateTime',
                'Time',
                'Timezone',
                'Positive',
                'PositiveOrZero',
                'Negative',
                'NegativeOrZero',
                'Luhn',
                'Range',
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
                'Count',
                'Type',
                'All',
                'AtLeastOneOf',
                'Collection',
                'Sequentially',
                'TypedRegex',
                'DefaultLanguage',
                'CleanHtml',
            ],
            ExtraPropertyConstraintGrammar::getAllowedNames()
        );
    }

    public function testGetAllowedConstraintsMapsEveryAliasToAConstraintClass(): void
    {
        $allowed = ExtraPropertyConstraintGrammar::getAllowedConstraints();

        $this->assertSame(ExtraPropertyConstraintGrammar::getAllowedNames(), array_keys($allowed));
        foreach ($allowed as $alias => $fqcn) {
            $this->assertTrue(
                is_subclass_of($fqcn, Constraint::class),
                sprintf('"%s" must map to a Symfony Constraint subclass, got %s.', $alias, $fqcn)
            );
        }
    }

    // -- resolveName() ---------------------------------------------------------------------------

    public function testResolveNameReturnsTheAllowlistedClass(): void
    {
        $this->assertSame(Assert\NotBlank::class, ExtraPropertyConstraintGrammar::resolveName('NotBlank'));
        $this->assertSame(TypedRegex::class, ExtraPropertyConstraintGrammar::resolveName('TypedRegex'));
    }

    public function testResolveNameIsCaseSensitive(): void
    {
        // The aliases are the exact Symfony short names — "notblank" must not match.
        $this->expectException(UnknownExtraPropertyConstraintException::class);

        ExtraPropertyConstraintGrammar::resolveName('notblank');
    }

    public function testAnUnknownAliasIsRefusedWithTheAllowedNames(): void
    {
        try {
            ExtraPropertyConstraintGrammar::resolveName('Bogus');
            $this->fail('An UnknownExtraPropertyConstraintException was expected.');
        } catch (UnknownExtraPropertyConstraintException $e) {
            $this->assertStringContainsString('Unknown extra property constraint "Bogus"', $e->getMessage());
            $this->assertStringContainsString(
                'Allowed constraints: ' . implode(', ', ExtraPropertyConstraintGrammar::getAllowedNames()),
                $e->getMessage()
            );
        }
    }

    public function testANonAllowlistedSymfonyConstraintIsUnknown(): void
    {
        // Callback/Expression are deliberately kept out of the allowlist (code execution).
        $this->expectException(UnknownExtraPropertyConstraintException::class);

        ExtraPropertyConstraintGrammar::resolveName('Callback');
    }

    // -- internal Collection wrappers ------------------------------------------------------------

    /**
     * The wrappers Collection generates are part of the grammar but must never be offered as
     * constraints a merchant can pick.
     */
    public function testTheCollectionWrappersAreNotPublicConstraints(): void
    {
        $this->assertNotContains('Required', ExtraPropertyConstraintGrammar::getAllowedNames());
        $this->assertNotContains('Optional', ExtraPropertyConstraintGrammar::getAllowedNames());
        $this->assertArrayNotHasKey('Required', ExtraPropertyConstraintGrammar::getAllowedConstraints());
        $this->assertArrayNotHasKey('Optional', ExtraPropertyConstraintGrammar::getAllowedConstraints());
    }

    public function testTheCollectionWrappersResolveOnlyWhenInternalNamesAreAllowed(): void
    {
        $this->assertSame(Assert\Required::class, ExtraPropertyConstraintGrammar::resolveName('Required', true));
        $this->assertSame(Assert\Optional::class, ExtraPropertyConstraintGrammar::resolveName('Optional', true));
    }

    public function testTheCollectionWrappersAreUnknownByDefault(): void
    {
        $this->expectException(UnknownExtraPropertyConstraintException::class);
        $this->expectExceptionMessageMatches('/Unknown extra property constraint "Required"/');

        ExtraPropertyConstraintGrammar::resolveName('Required', false);
    }

    public function testAllowingInternalNamesDoesNotWidenTheAllowlist(): void
    {
        $this->expectException(UnknownExtraPropertyConstraintException::class);

        ExtraPropertyConstraintGrammar::resolveName('Callback', true);
    }

    // -- aliasOf() -------------------------------------------------------------------------------

    public function testAliasOfCoversPublicAndInternalConstraints(): void
    {
        $this->assertSame('NotBlank', ExtraPropertyConstraintGrammar::aliasOf(Assert\NotBlank::class));
        $this->assertSame('TypedRegex', ExtraPropertyConstraintGrammar::aliasOf(TypedRegex::class));
        $this->assertSame('Required', ExtraPropertyConstraintGrammar::aliasOf(Assert\Required::class));
        $this->assertSame('Optional', ExtraPropertyConstraintGrammar::aliasOf(Assert\Optional::class));
    }

    public function testAliasOfIsNullOutsideTheGrammar(): void
    {
        $this->assertNull(ExtraPropertyConstraintGrammar::aliasOf(Assert\Callback::class));
        $this->assertNull(ExtraPropertyConstraintGrammar::aliasOf(Constraint::class));
    }

    public function testAliasOfIsTheInverseOfTheAllowlist(): void
    {
        foreach (ExtraPropertyConstraintGrammar::getAllowedConstraints() as $alias => $fqcn) {
            $this->assertSame($alias, ExtraPropertyConstraintGrammar::aliasOf($fqcn));
        }
    }

    // -- composites ------------------------------------------------------------------------------

    public function testCompositeNamesAreTheBracketShapedAliases(): void
    {
        $this->assertSame(
            ['All', 'AtLeastOneOf', 'Collection', 'Sequentially'],
            ExtraPropertyConstraintGrammar::compositeNames()
        );
    }

    public function testIsCompositeFollowsTheSymfonyCompositeHierarchy(): void
    {
        $this->assertTrue(ExtraPropertyConstraintGrammar::isComposite(Assert\All::class));
        $this->assertTrue(ExtraPropertyConstraintGrammar::isComposite(Assert\Collection::class));
        // The internal wrappers nest constraints too, even though they are not public composites.
        $this->assertTrue(ExtraPropertyConstraintGrammar::isComposite(Assert\Required::class));
        $this->assertFalse(ExtraPropertyConstraintGrammar::isComposite(Assert\NotBlank::class));
        $this->assertFalse(ExtraPropertyConstraintGrammar::isComposite(Assert\Choice::class));
    }

    public function testChildrenOptionOfIsFieldsForCollectionAndConstraintsElsewhere(): void
    {
        $this->assertSame('fields', ExtraPropertyConstraintGrammar::childrenOptionOf(Assert\Collection::class));
        $this->assertSame('constraints', ExtraPropertyConstraintGrammar::childrenOptionOf(Assert\All::class));
        $this->assertSame('constraints', ExtraPropertyConstraintGrammar::childrenOptionOf(Assert\AtLeastOneOf::class));
        $this->assertSame('constraints', ExtraPropertyConstraintGrammar::childrenOptionOf(Assert\Sequentially::class));
        $this->assertSame('constraints', ExtraPropertyConstraintGrammar::childrenOptionOf(Assert\Required::class));
    }

    // -- options ---------------------------------------------------------------------------------

    /**
     * @dataProvider forbiddenOptionProvider
     */
    public function testIsForbiddenOptionRefusesCallablesAndPropertyPaths(string $option, bool $expected): void
    {
        $this->assertSame($expected, ExtraPropertyConstraintGrammar::isForbiddenOption($option));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function forbiddenOptionProvider(): iterable
    {
        yield 'callback' => ['callback', true];
        yield 'normalizer' => ['normalizer', true];
        yield 'propertyPath' => ['propertyPath', true];
        yield 'minPropertyPath' => ['minPropertyPath', true];
        yield 'maxPropertyPath' => ['maxPropertyPath', true];
        yield 'ibanPropertyPath' => ['ibanPropertyPath', true];
        yield 'property path suffix is matched case-insensitively' => ['PropertyPath', true];
        yield 'min' => ['min', false];
        yield 'value' => ['value', false];
        yield 'choices' => ['choices', false];
    }

    public function testOnlyCallbackAndNormalizerAreCallableOptions(): void
    {
        $this->assertTrue(ExtraPropertyConstraintGrammar::isCallableOption('callback'));
        $this->assertTrue(ExtraPropertyConstraintGrammar::isCallableOption('normalizer'));
        // Property paths are forbidden too, but for another reason: they traverse the validated object.
        $this->assertFalse(ExtraPropertyConstraintGrammar::isCallableOption('propertyPath'));
        $this->assertFalse(ExtraPropertyConstraintGrammar::isCallableOption('min'));
    }

    public function testGroupsAndPayloadAreNeverRenderable(): void
    {
        $this->assertFalse(ExtraPropertyConstraintGrammar::isRenderableOption('groups'));
        $this->assertFalse(ExtraPropertyConstraintGrammar::isRenderableOption('payload'));
        $this->assertTrue(ExtraPropertyConstraintGrammar::isRenderableOption('min'));
        $this->assertTrue(ExtraPropertyConstraintGrammar::isRenderableOption('value'));
        $this->assertTrue(ExtraPropertyConstraintGrammar::isRenderableOption('message'));
    }

    public function testDefaultOptionOfIsTheOptionFedByThePositionalShape(): void
    {
        $this->assertSame('value', ExtraPropertyConstraintGrammar::defaultOptionOf(Assert\GreaterThan::class));
        $this->assertSame('choices', ExtraPropertyConstraintGrammar::defaultOptionOf(Assert\Choice::class));
        $this->assertSame('type', ExtraPropertyConstraintGrammar::defaultOptionOf(TypedRegex::class));
        $this->assertSame('fieldName', ExtraPropertyConstraintGrammar::defaultOptionOf(DefaultLanguage::class));
    }

    public function testDefaultOptionOfIsNullForAConstraintWithoutOne(): void
    {
        $this->assertNull(ExtraPropertyConstraintGrammar::defaultOptionOf(Assert\NotBlank::class));
        // Length takes min/max/exactly, so it only has the named shape.
        $this->assertNull(ExtraPropertyConstraintGrammar::defaultOptionOf(Assert\Length::class));
    }

    /**
     * Regression guard on the security boundary: every option of an allowlisted constraint (internal
     * wrappers included) whose name says it is invoked or traversed at validation time must be
     * refused by isForbiddenOption(). The distinct option names are pinned so a new constraint
     * bringing a new kind of executable option cannot slip in unnoticed.
     */
    public function testEveryCallableOrPropertyPathOptionOfTheAllowlistIsForbidden(): void
    {
        $classes = ExtraPropertyConstraintGrammar::getAllowedConstraints()
            + ['Required' => Assert\Required::class, 'Optional' => Assert\Optional::class];

        $found = [];
        foreach ($classes as $alias => $fqcn) {
            foreach ((new ReflectionClass($fqcn))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                if ($property->isStatic() || 1 !== preg_match('/callback|normalizer|propertypath/i', $property->getName())) {
                    continue;
                }
                $found[$property->getName()] = true;
                $this->assertTrue(
                    ExtraPropertyConstraintGrammar::isForbiddenOption($property->getName()),
                    sprintf('Option "%s" of "%s" must be forbidden.', $property->getName(), $alias)
                );
            }
        }

        $found = array_keys($found);
        sort($found);
        $this->assertSame(
            ['callback', 'ibanPropertyPath', 'maxPropertyPath', 'minPropertyPath', 'normalizer', 'propertyPath'],
            $found
        );
    }

    // -- bounds ----------------------------------------------------------------------------------

    public function testTheBoundsOfTheFormatArePinned(): void
    {
        // A stored row is parsed on every request: the bounds cap stack depth and request budget.
        $this->assertSame(16, ExtraPropertyConstraintGrammar::MAX_NESTING_DEPTH);
        // The registry column is a TEXT column.
        $this->assertSame(65535, ExtraPropertyConstraintGrammar::MAX_RAW_LENGTH);
        $this->assertSame(256, ExtraPropertyConstraintGrammar::MAX_TOKENS);
    }
}
