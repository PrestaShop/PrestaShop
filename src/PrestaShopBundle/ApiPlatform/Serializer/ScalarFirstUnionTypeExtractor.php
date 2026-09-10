<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Serializer;

use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Orders the members of a union type so that the scalar ones come before the class ones.
 *
 * PHP normalizes a union type with the class members first ("int|string|DecimalNumber|null" is
 * reflected as "DecimalNumber|string|int|null"), and the serializer tries the members in that
 * order: for 5 it would ask the DecimalNumber normalizer first, which accepts anything numeric,
 * so an int would come back as a decimal and a plain string would fail before "string" is ever
 * tried. With the scalars first, a JSON value that matches a declared scalar member keeps its type
 * and a class member only catches what no scalar accepts (a float for DecimalNumber, for instance).
 *
 * Registered as a property_info type extractor ahead of the others: it only answers for a union
 * mixing scalar and class members (the reflected types, reordered) and stays silent otherwise, so
 * every other property keeps going through the regular extractor chain. Being part of
 * property_info, it applies to every consumer — API Platform property metadata, Symfony's
 * ObjectNormalizer and the CQRS normalizer alike.
 */
final class ScalarFirstUnionTypeExtractor implements PropertyTypeExtractorInterface
{
    public function __construct(
        private readonly PropertyTypeExtractorInterface $reflectionExtractor,
    ) {
    }

    /**
     * @return list<Type>|null the reordered union, or null when there is nothing to reorder (the
     *                         next extractors of the chain then answer as usual)
     */
    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        $types = $this->reflectionExtractor->getTypes($class, $property, $context);
        if (null === $types || count($types) < 2) {
            return null;
        }

        // Stable partition: the relative order inside each group is kept.
        $scalars = [];
        $objects = [];
        foreach ($types as $type) {
            if (Type::BUILTIN_TYPE_OBJECT === $type->getBuiltinType()) {
                $objects[] = $type;
            } else {
                $scalars[] = $type;
            }
        }

        if ([] === $scalars || [] === $objects) {
            return null;
        }

        return [...$scalars, ...$objects];
    }
}
