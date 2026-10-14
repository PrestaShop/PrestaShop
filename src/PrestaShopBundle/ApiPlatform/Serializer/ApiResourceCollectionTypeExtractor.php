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
 * Reads the item type of the array properties of the API resources and their DTOs from the PHPDoc (e.g. @var Foo[]),
 * which the native type can't express. It is ahead of the reflection extractors, so it only answers for these
 * collections: any other property, or any class outside the ApiPlatform resources, keeps its native type.
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

        foreach ($types as $type) {
            if (!$type->isCollection() || Type::BUILTIN_TYPE_ARRAY !== $type->getBuiltinType() || empty($type->getCollectionValueTypes())) {
                return null;
            }
        }

        return $types;
    }
}
