<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Constraint;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\UnknownExtraPropertyConstraintException;
use ReflectionClass;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Composite;

/**
 * Vocabulary of the extra property constraint DSL: which Symfony constraints may appear in a
 * definition and under which alias, which options are refused, and the bounds a definition must
 * respect. It knows nothing about the text itself (see ExtraPropertyConstraintParser) nor about
 * rendering (see ExtraPropertyConstraintRenderer): both directions read their rules here, so they
 * cannot drift apart.
 *
 * The allowlist is also the security boundary of the format. The DSL is persisted in the registry
 * table and parsed back on every request, so a class name never comes from the text: an alias is
 * resolved against this table or refused. Options Symfony invokes or resolves at validation time
 * (callables, property paths) are refused in both directions for the same reason.
 */
class ExtraPropertyConstraintGrammar
{
    private const ALLOWED_CONSTRAINTS = [
        // Presence
        'NotBlank' => Assert\NotBlank::class,
        'NotNull' => Assert\NotNull::class,
        'Blank' => Assert\Blank::class,
        'IsNull' => Assert\IsNull::class,
        // String / format
        'Email' => Assert\Email::class,
        'Url' => Assert\Url::class,
        'Json' => Assert\Json::class,
        'Uuid' => Assert\Uuid::class,
        'Ulid' => Assert\Ulid::class,
        'Ip' => Assert\Ip::class,
        'Cidr' => Assert\Cidr::class,
        'Hostname' => Assert\Hostname::class,
        'CssColor' => Assert\CssColor::class,
        'NoSuspiciousCharacters' => Assert\NoSuspiciousCharacters::class,
        'Length' => Assert\Length::class,
        // Regex takes a user-supplied pattern, so a pathological pattern is a theoretical ReDoS
        // at validation time. Accepted: the DSL is only writable by BO admins (or module code),
        // and PCRE's backtracking limit aborts a runaway match instead of hanging the request.
        'Regex' => Assert\Regex::class,
        // Date / time
        'Date' => Assert\Date::class,
        'DateTime' => Assert\DateTime::class,
        'Time' => Assert\Time::class,
        'Timezone' => Assert\Timezone::class,
        // Numbers
        'Positive' => Assert\Positive::class,
        'PositiveOrZero' => Assert\PositiveOrZero::class,
        'Negative' => Assert\Negative::class,
        'NegativeOrZero' => Assert\NegativeOrZero::class,
        'Luhn' => Assert\Luhn::class,
        'Range' => Assert\Range::class,
        // Comparison (value coerced to int/float when numeric)
        'EqualTo' => Assert\EqualTo::class,
        'NotEqualTo' => Assert\NotEqualTo::class,
        'IdenticalTo' => Assert\IdenticalTo::class,
        'NotIdenticalTo' => Assert\NotIdenticalTo::class,
        'LessThan' => Assert\LessThan::class,
        'LessThanOrEqual' => Assert\LessThanOrEqual::class,
        'GreaterThan' => Assert\GreaterThan::class,
        'GreaterThanOrEqual' => Assert\GreaterThanOrEqual::class,
        'DivisibleBy' => Assert\DivisibleBy::class,
        // Boolean
        'IsTrue' => Assert\IsTrue::class,
        'IsFalse' => Assert\IsFalse::class,
        // Banking / identifiers
        'Iban' => Assert\Iban::class,
        'Bic' => Assert\Bic::class,
        'Isbn' => Assert\Isbn::class,
        'Issn' => Assert\Issn::class,
        'Isin' => Assert\Isin::class,
        'CardScheme' => Assert\CardScheme::class,
        // Locale (ISO)
        'Country' => Assert\Country::class,
        'Language' => Assert\Language::class,
        'Locale' => Assert\Locale::class,
        'Currency' => Assert\Currency::class,
        // Common parametric
        'Choice' => Assert\Choice::class,
        'Count' => Assert\Count::class,
        'Type' => Assert\Type::class,
        // Composites (nested constraints between brackets)
        'All' => Assert\All::class,
        'AtLeastOneOf' => Assert\AtLeastOneOf::class,
        'Collection' => Assert\Collection::class,
        'Sequentially' => Assert\Sequentially::class,
        // PrestaShop custom
        'TypedRegex' => TypedRegex::class,
        'DefaultLanguage' => DefaultLanguage::class,
        'CleanHtml' => CleanHtml::class,
    ];

    /**
     * Wrappers Symfony generates inside a Collection for each of its fields. They are part of the
     * grammar — a Collection cannot round-trip without them — but they are NOT public constraints:
     * they never appear in getAllowedConstraints(), so the BO builder catalog does not offer them,
     * and the parser only accepts them directly under a Collection.
     */
    private const INTERNAL_CONSTRAINTS = [
        'Optional' => Assert\Optional::class,
        'Required' => Assert\Required::class,
    ];

    /**
     * Options whose value Symfony invokes as a callable at validation time. A stored DSL string is
     * attacker-reachable through the database, so these are refused wherever a constraint is built
     * or rendered.
     */
    private const CALLABLE_OPTIONS = ['callback', 'normalizer'];

    /**
     * Options that exist on every constraint and never belong in the DSL: an extra property
     * validates a single value in the default group, so they carry no meaning here.
     */
    private const NON_RENDERABLE_OPTIONS = ['groups', 'payload'];

    /**
     * Bounds applied to any DSL string being parsed. The value stored in the registry is parsed on
     * read, so a tampered row must not be able to exhaust the stack or the request budget.
     */
    public const MAX_NESTING_DEPTH = 16;
    public const MAX_RAW_LENGTH = 65535;
    public const MAX_TOKENS = 256;

    /**
     * Static-only class.
     */
    private function __construct()
    {
    }

    /**
     * The allowlist itself (alias => constraint FQCN), e.g. to build a machine-readable catalog.
     *
     * @return array<string, class-string<Constraint>>
     */
    public static function getAllowedConstraints(): array
    {
        return self::ALLOWED_CONSTRAINTS;
    }

    /**
     * @return list<string>
     */
    public static function getAllowedNames(): array
    {
        return array_keys(self::ALLOWED_CONSTRAINTS);
    }

    /**
     * Resolves a DSL alias to its constraint class. The internal Collection wrappers are only
     * resolvable when the caller says so (i.e. directly under a Collection).
     *
     * @return class-string<Constraint>
     *
     * @throws UnknownExtraPropertyConstraintException
     */
    public static function resolveName(string $alias, bool $allowInternal = false): string
    {
        if ($allowInternal && isset(self::INTERNAL_CONSTRAINTS[$alias])) {
            return self::INTERNAL_CONSTRAINTS[$alias];
        }

        if (!isset(self::ALLOWED_CONSTRAINTS[$alias])) {
            throw new UnknownExtraPropertyConstraintException(sprintf(
                'Unknown extra property constraint "%s". Allowed constraints: %s.',
                $alias,
                implode(', ', self::getAllowedNames())
            ));
        }

        return self::ALLOWED_CONSTRAINTS[$alias];
    }

    /**
     * The DSL alias of a constraint class (internal Collection wrappers included), or null when the
     * class is outside the grammar. Rendering resolves the alias through this map instead of the
     * class short name, so a class outside the grammar is structurally unrenderable rather than
     * emitted as a name the parser would later reject.
     *
     * @param class-string $fqcn
     */
    public static function aliasOf(string $fqcn): ?string
    {
        return self::aliasesByClass()[$fqcn] ?? null;
    }

    /**
     * The public aliases whose token uses the composite bracket shape ("Name[ children ]") instead
     * of the parenthesis shape.
     *
     * @return list<string>
     */
    public static function compositeNames(): array
    {
        return array_keys(array_filter(self::ALLOWED_CONSTRAINTS, self::isComposite(...)));
    }

    /**
     * @param class-string $fqcn
     */
    public static function isComposite(string $fqcn): bool
    {
        return is_subclass_of($fqcn, Composite::class);
    }

    /**
     * Whether an option is one Symfony invokes as a callable at validation time.
     */
    public static function isCallableOption(string $option): bool
    {
        return in_array($option, self::CALLABLE_OPTIONS, true);
    }

    /**
     * Whether an option is refused wherever a constraint is built or rendered: callable options
     * Symfony invokes at validation time, and property-path options that traverse the validated
     * object (an extra property constraint validates a single value).
     */
    public static function isForbiddenOption(string $option): bool
    {
        return self::isCallableOption($option) || str_ends_with(strtolower($option), 'propertypath');
    }

    /**
     * Whether an option may appear in the DSL at all. groups and payload exist on every constraint
     * but carry no meaning for an extra property: the parser refuses them in a token, the renderer
     * refuses non-default values on an object instead of dropping them silently, and the BO builder
     * never offers them.
     */
    public static function isRenderableOption(string $option): bool
    {
        return !in_array($option, self::NON_RENDERABLE_OPTIONS, true);
    }

    /**
     * The option fed by the positional shape ("GreaterThan(5)"), read without invoking the
     * constructor (the method returns a constant and touches no instance state).
     *
     * @param class-string<Constraint> $fqcn
     */
    public static function defaultOptionOf(string $fqcn): ?string
    {
        /** @var Constraint $prototype */
        $prototype = (new ReflectionClass($fqcn))->newInstanceWithoutConstructor();

        return $prototype->getDefaultOption();
    }

    /**
     * The option through which a composite carries its nested constraints — the one that travels in
     * the "[...]" tail and must never be rendered or accepted as a regular option.
     *
     * @param class-string<Constraint> $fqcn
     */
    public static function childrenOptionOf(string $fqcn): string
    {
        return Assert\Collection::class === $fqcn
            ? 'fields'
            : (self::defaultOptionOf($fqcn) ?? 'constraints');
    }

    /**
     * Reverse map FQCN => alias, covering public constraints and the internal Collection wrappers.
     *
     * @return array<class-string, string>
     */
    private static function aliasesByClass(): array
    {
        static $aliases = null;

        return $aliases ??= array_flip(self::ALLOWED_CONSTRAINTS + self::INTERNAL_CONSTRAINTS);
    }
}
