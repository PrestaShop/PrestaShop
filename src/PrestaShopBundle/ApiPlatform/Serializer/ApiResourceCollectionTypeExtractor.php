<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Serializer;

use ReflectionProperty;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Reads the item type of the array properties of the API resources and their DTOs from the PHPDoc (e.g. @var Foo[]),
 * which the native type can't express, alone or in a union (@var list<string>|DecimalNumber). It is ahead of the
 * reflection extractors, so it only answers for these collections: any other property, or any class outside the
 * ApiPlatform resources, keeps its native type.
 */
class ApiResourceCollectionTypeExtractor implements PropertyTypeExtractorInterface
{
    public function __construct(
        protected readonly ?PropertyTypeExtractorInterface $phpDocExtractor,
    ) {
    }

    public function getTypes(string $class, string $property, array $context = []): ?array
    {
        if (null === $this->phpDocExtractor || !str_contains($class, '\\ApiPlatform\\Resources\\')) {
            return null;
        }

        $types = $this->phpDocExtractor->getTypes($class, $property, $context);
        if (empty($types)) {
            return null;
        }

        // Only the arrays with a documented item type are answered, other union members (e.g. list<string>|DecimalNumber) come along
        $hasTypedArray = false;
        foreach ($types as $type) {
            if (Type::BUILTIN_TYPE_ARRAY !== $type->getBuiltinType()) {
                continue;
            }
            if (!$type->isCollection() || empty($type->getCollectionValueTypes())) {
                return null;
            }
            $hasTypedArray = true;
        }

        if (!$hasTypedArray) {
            return null;
        }

        // A nullable native type (?array) with a non-nullable PHPDoc (Foo[]) stays nullable
        if ($this->isNativeTypeNullable($class, $property)) {
            $types = array_map(
                static fn (Type $type): Type => $type->isNullable() ? $type : new Type(
                    $type->getBuiltinType(),
                    true,
                    $type->getClassName(),
                    $type->isCollection(),
                    $type->getCollectionKeyTypes(),
                    $type->getCollectionValueTypes(),
                ),
                $types
            );
        }

        return $types;
    }

    protected function isNativeTypeNullable(string $class, string $property): bool
    {
        if (!property_exists($class, $property)) {
            return false;
        }

        return (bool) (new ReflectionProperty($class, $property))->getType()?->allowsNull();
    }
}
