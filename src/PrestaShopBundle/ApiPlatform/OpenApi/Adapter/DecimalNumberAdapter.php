<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ArrayObject;
use PrestaShop\Decimal\DecimalNumber;
use ReflectionNamedType;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Adapts DecimalNumber properties in OpenAPI schema.
 * Internally we rely on DecimalNumber for float values because they are more accurate,
 * but in the JSON format they should be considered as float, so we update the schema for these types.
 */
class DecimalNumberAdapter implements OpenApiSchemaAdapterInterface
{
    public function __construct(
        protected readonly ClassMetadataFactoryInterface $classMetadataFactory
    ) {
    }

    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (empty($definition['properties'])) {
            return;
        }

        $resourceClassMetadata = $this->classMetadataFactory->getMetadataFor($class);
        $resourceReflectionClass = $resourceClassMetadata->getReflectionClass();

        foreach ($definition['properties'] as $propertyName => $propertySchema) {
            if (!$resourceReflectionClass->hasProperty($propertyName)) {
                continue;
            }

            $property = $resourceReflectionClass->getProperty($propertyName);
            if ($property->hasType() && $property->getType() instanceof ReflectionNamedType) {
                $propertyType = $property->getType()->getName();
                if ($propertyType === DecimalNumber::class || is_subclass_of($propertyType, DecimalNumber::class)) {
                    $definition['properties'][$propertyName]['type'] = 'number';
                    $definition['properties'][$propertyName]['example'] = 42.99;
                    unset($definition['properties'][$propertyName]['$ref']);
                    unset($definition['properties'][$propertyName]['allOf']);
                }
            }
        }
    }
}
