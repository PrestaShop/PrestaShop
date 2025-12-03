<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
