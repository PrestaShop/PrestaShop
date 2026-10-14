<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module;

/**
 * Builds the regular expressions used to locate a class member declaration (method, property
 * or constant) on a single line of a module override file.
 *
 * Every pattern exposes two capturing groups:
 *  - group 1: what precedes the declaration on the line (indentation and, if any, attributes)
 *  - group 2: the declaration head, from its first modifier up to the member name
 *
 * The forAny*() variants match every declaration of their kind and capture the member name in
 * the `name` group, which makes them suitable for preg_match_all() on a whole file.
 *
 * The head is captured as a whole so that a comment can be injected in front of the complete
 * declaration (before `final public const`, not between `final` and `public`), which is the
 * placement PHP CS Fixer expects.
 *
 * Supported syntax: modifiers in any order, asymmetric visibility (`private(set)`), `readonly`,
 * `final`, `abstract`, `static`, `var`, typed properties and constants (nullable, union,
 * intersection and DNF types, fully qualified class names) and methods returning by reference.
 * A declaration is only matched at the start of a line, so occurrences inside strings, comments
 * or constructor promoted parameters are ignored.
 */
final class ModuleOverrideDeclarationPattern
{
    /**
     * Indentation followed by optional attributes declared on the same line as the member.
     */
    private const PREFIX = '(\s*(?:#\[[^\]]*\]\s*)*)';

    /**
     * A type declaration: nullable, union, intersection, DNF or fully qualified class name.
     */
    private const TYPE = '[\\\\\w?()|&]+';

    /**
     * Any member name, captured in the `name` group.
     */
    private const ANY_NAME = '(?<name>\w+)';

    private const VISIBILITY = '(?:public|protected|private)(?:\(set\))?';

    private const METHOD_MODIFIER = '(?:public|protected|private|static|final|abstract)';

    private const PROPERTY_MODIFIER = '(?:' . self::VISIBILITY . '|static|readonly|final|abstract|var)';

    private const CONSTANT_MODIFIER = '(?:public|protected|private|final)';

    /**
     * Matches `[modifiers] function [&]name` followed by the parameter list.
     */
    public static function forMethod(string $methodName): string
    {
        // Method names are case-insensitive in PHP
        return self::buildMethodPattern('(?i:' . preg_quote($methodName, '/') . ')');
    }

    /**
     * Matches any method declaration, the method name being captured in the `name` group.
     */
    public static function forAnyMethod(): string
    {
        return self::buildMethodPattern(self::ANY_NAME);
    }

    /**
     * Matches `modifiers [type] $name` followed by a default value, the end of the statement or property hooks.
     */
    public static function forProperty(string $propertyName): string
    {
        return self::buildPropertyPattern(preg_quote($propertyName, '/'));
    }

    /**
     * Matches any property declaration, the property name (without `$`) being captured in the `name` group.
     */
    public static function forAnyProperty(): string
    {
        return self::buildPropertyPattern(self::ANY_NAME);
    }

    /**
     * Matches `[modifiers] const [type] NAME` followed by its value.
     */
    public static function forConstant(string $constantName): string
    {
        return self::buildConstantPattern(preg_quote($constantName, '/'));
    }

    /**
     * Matches any constant declaration, the constant name being captured in the `name` group.
     */
    public static function forAnyConstant(): string
    {
        return self::buildConstantPattern(self::ANY_NAME);
    }

    private static function buildMethodPattern(string $name): string
    {
        return '/^' . self::PREFIX
            . '((?:(?i:' . self::METHOD_MODIFIER . ')\s+)*(?i:function)\s+&?\s*' . $name . ')'
            . '(?=\s*\()/m';
    }

    private static function buildPropertyPattern(string $name): string
    {
        return '/^' . self::PREFIX
            . '((?:(?i:' . self::PROPERTY_MODIFIER . ')\s+)+(?:' . self::TYPE . '\s+)?\$' . $name . ')'
            . '(?=\s*[=;{])/m';
    }

    private static function buildConstantPattern(string $name): string
    {
        return '/^' . self::PREFIX
            . '((?:(?i:' . self::CONSTANT_MODIFIER . ')\s+)*(?i:const)\s+(?:' . self::TYPE . '\s+)?' . $name . ')'
            . '(?=\s*=)/m';
    }
}
