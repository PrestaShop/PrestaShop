<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ArrayObject;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateImmutable;
use ReflectionNamedType;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * Adapts DateImmutable properties to use 'date' format instead of 'date-time' in OpenAPI schema.
 * Checks both property types and getter/setter method signatures to detect DateImmutable usage.
 * Only adapts API resource classes, not command classes.
 */
class DatePropertyAdapter implements OpenApiSchemaAdapterInterface
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
            $propertyType = null;
            if ($resourceReflectionClass->hasProperty($propertyName)) {
                $property = $resourceReflectionClass->getProperty($propertyName);
                if ($property->hasType() && $property->getType() instanceof ReflectionNamedType) {
                    $propertyType = $property->getType()->getName();
                }
            }

            if (!$propertyType) {
                $camelCasePropertyName = ucfirst($propertyName);
                $getterMethodName = 'get' . $camelCasePropertyName;
                $setterMethodName = 'set' . $camelCasePropertyName;

                if ($resourceReflectionClass->hasMethod($getterMethodName)) {
                    $getterMethod = $resourceReflectionClass->getMethod($getterMethodName);
                    if ($getterMethod->hasReturnType() && $getterMethod->getReturnType() instanceof ReflectionNamedType) {
                        $propertyType = $getterMethod->getReturnType()->getName();
                    }
                } elseif ($resourceReflectionClass->hasMethod($setterMethodName)) {
                    $setterMethod = $resourceReflectionClass->getMethod($setterMethodName);
                    $parameters = $setterMethod->getParameters();
                    if (!empty($parameters) && $parameters[0]->hasType() && $parameters[0]->getType() instanceof ReflectionNamedType) {
                        $propertyType = $parameters[0]->getType()->getName();
                    }
                }
            }

            $isDateProperty = false;
            if ($propertyType !== null) {
                if ($propertyType === DateImmutable::class || (class_exists($propertyType) && is_subclass_of($propertyType, DateImmutable::class))) {
                    $isDateProperty = true;
                }
            }

            if ($isDateProperty) {
                $definition['properties'][$propertyName]['format'] = 'date';

                $example = $definition['properties'][$propertyName]['example'] ?? null;
                if (empty($example)) {
                    $definition['properties'][$propertyName]['example'] = '2025-11-05';
                }
            }
        }
    }
}
