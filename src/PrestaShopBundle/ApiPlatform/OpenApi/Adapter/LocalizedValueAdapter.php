<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ArrayObject;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Adapts localized values in OpenAPI schema.
 * Localized values are arrays indexed by locales (or objects with properties matching the locale in JSON),
 * this adapter adapts the expected format along with an example to indicate the user that the key to use is the locale.
 */
class LocalizedValueAdapter implements OpenApiSchemaAdapterInterface
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
                if ($attribute->getName() === LocalizedValue::class || is_subclass_of($attribute->getName(), LocalizedValue::class)) {
                    if (!($propertySchema instanceof ArrayObject)) {
                        $definition['properties'][$propertyName] = new ArrayObject();
                    }
                    unset($definition['properties'][$propertyName]['type']);
                    $definition['properties'][$propertyName]['type'] = 'object';
                    if (!isset($definition['properties'][$propertyName]['example'])) {
                        $definition['properties'][$propertyName]['example'] = [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ];
                    }
                    unset($definition['properties'][$propertyName]['items']);
                    unset($definition['properties'][$propertyName]['format']);
                    if (isset($definition['properties'][$propertyName]['enum'])) {
                        unset($definition['properties'][$propertyName]['enum']);
                    }
                }
            }
        }
    }
}
