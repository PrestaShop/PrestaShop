<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Serializer;

use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyInitializableExtractorInterface;
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
 * Decorates the property_info service, so it reorders the final answer of the extractor chain (a
 * collection type refined by a PHPDoc extractor is kept) and applies to every consumer — API
 * Platform property metadata, Symfony's ObjectNormalizer and the CQRS normalizer alike.
 */
final class ScalarFirstUnionTypeExtractor implements PropertyInfoExtractorInterface, PropertyInitializableExtractorInterface
{
    public function __construct(
        private readonly PropertyInfoExtractorInterface&PropertyInitializableExtractorInterface $decorated,
    ) {
    }

    /**
     * @return list<Type>|null
     */
    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        $types = $this->decorated->getTypes($class, $property, $context);
        if (null === $types || count($types) < 2) {
            return $types;
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

        return [...$scalars, ...$objects];
    }

    public function getProperties(string $class, array $context = []): ?array
    {
        return $this->decorated->getProperties($class, $context);
    }

    public function getShortDescription(string $class, string $property, array $context = []): ?string
    {
        return $this->decorated->getShortDescription($class, $property, $context);
    }

    public function getLongDescription(string $class, string $property, array $context = []): ?string
    {
        return $this->decorated->getLongDescription($class, $property, $context);
    }

    public function isReadable(string $class, string $property, array $context = []): ?bool
    {
        return $this->decorated->isReadable($class, $property, $context);
    }

    public function isWritable(string $class, string $property, array $context = []): ?bool
    {
        return $this->decorated->isWritable($class, $property, $context);
    }

    public function isInitializable(string $class, string $property, array $context = []): ?bool
    {
        return $this->decorated->isInitializable($class, $property, $context);
    }
}
