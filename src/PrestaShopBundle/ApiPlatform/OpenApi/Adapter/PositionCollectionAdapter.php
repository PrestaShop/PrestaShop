<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ArrayObject;
use PrestaShopBundle\ApiPlatform\Metadata\PositionCollection;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Adapts position collection properties in OpenAPI schema.
 * PositionCollection are arrays that contain position updates formatted like, the field of the array
 * can be modified via the attribute configuration. See PositionCollection for more details.
 */
class PositionCollectionAdapter implements OpenApiSchemaAdapterInterface
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
            foreach ($property->getAttributes() as $attribute) {
                if ($attribute->getName() === PositionCollection::class || is_subclass_of($attribute->getName(), PositionCollection::class)) {
                    if (!($propertySchema instanceof ArrayObject)) {
                        $definition['properties'][$propertyName] = new ArrayObject();
                    }
                    unset($definition['properties'][$propertyName]['type']);

                    // The rowIdField is configurable on the Attribute
                    $rowIdField = $attribute->getArguments()['rowIdField'] ?? PositionCollection::DEFAULT_ROW_ID_FIELD;
                    $definition['properties'][$propertyName]['type'] = 'array';
                    $definition['properties'][$propertyName]['items'] = new ArrayObject([
                        'type' => 'object',
                        'properties' => new ArrayObject([
                            $rowIdField => ['type' => 'integer'],
                            'newPosition' => ['type' => 'integer'],
                        ]),
                    ]);
                    $definition['properties'][$propertyName]['example'] = new ArrayObject([
                        [
                            $rowIdField => 5,
                            'newPosition' => 3,
                        ],
                        [
                            $rowIdField => 8,
                            'newPosition' => 1,
                        ],
                    ]);
                }
            }
        }
    }
}
