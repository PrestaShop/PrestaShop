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
use PrestaShopBundle\ApiPlatform\DomainObjectDetector;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Throwable;

/**
 * Synchronizes the OpenAPI schema with the actual properties available in the resource.
 *
 * This adapter ensures that the OpenAPI documentation accurately reflects the actual
 * properties available in the resource classes. It performs the following operations:
 *
 * 1. **Removes obsolete properties**: Properties that are documented in the schema but
 *    no longer exist in the actual resource class are removed.
 *    Example: If 'ean13' was replaced by 'gtin' in the resource, 'ean13' is removed from the schema.
 *
 * 2. **Validates property mappings**: For CQRS operations, validates that API property names
 *    correctly map to command property names. For example if the CQRS command has a localizedNames
 *    property that was renamed via the mapping into names then the schema won't use localizedNames but names for the final
 *    schema output so that it matches the actual expected format.
 *
 * 3. **Preserves special properties**: Multi-parameter setter properties (e.g., setDate(year, month, day))
 *    are preserved even if they don't match a single property, as they're handled specially. So that multi-parameter adapter can use them.
 *
 * 4. **Synchronizes required fields**: The 'required' array is filtered to only include
 *    properties that actually exist in the resource/command class.
 *
 * This ensures that API consumers only see properties that can actually be used, preventing
 * confusion and errors when properties are renamed or removed during refactoring.
 */
class SchemaSynchronizer implements OpenApiSchemaAdapterInterface
{
    public function __construct(
        protected readonly PropertyInfoExtractorInterface $propertyInfoExtractor,
        protected readonly ClassMetadataFactoryInterface $classMetadataFactory,
        protected readonly DomainObjectDetector $domainObjectDetector
    ) {
    }

    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        // Early return if there is no operation or the class doesn't exist because we only care about write operations
        if (!class_exists($class) || !$operation) {
            return;
        }

        // Determine which class to use for property extraction
        // For write operations (POST, PUT, PATCH), we need to check the input/command class
        // instead of the resource class, as the input class defines what properties can be set
        $inputClass = null;
        $classToUse = $class;
        if (method_exists($operation, 'getInput') && is_array($operation->getInput())) {
            $inputClass = $operation->getInput()['class'] ?? null;
            if ($inputClass && class_exists($inputClass)) {
                // Use the input/command class for write operations
                $classToUse = $inputClass;
            }
        }

        if (!$inputClass || !class_exists($inputClass)) {
            return;
        }

        // Ensure properties array exists (redundant check, but kept for safety)
        if (empty($definition['properties'])) {
            $definition['properties'] = new ArrayObject();
        }

        // Extract all actual properties from the class (command class for writes)
        $actualProperties = $this->getResourceProperties($inputClass);
        if (empty($actualProperties)) {
            return;
        }

        // Build reverse mapping: API property name -> CQRS command property name
        // The CQRSCommandMapping maps API paths to command paths
        // We reverse this to check if an API property maps to a valid command property
        $reverseMapping = [];
        if (!empty($operation->getExtraProperties()['CQRSCommandMapping'])) {
            foreach ($operation->getExtraProperties()['CQRSCommandMapping'] as $apiPath => $cqrsPath) {
                if (str_starts_with($apiPath, '[_context]')) {
                    continue;
                }
                $apiPropertyName = trim($apiPath, '[]');
                $cqrsPropertyName = trim($cqrsPath, '[]');
                $reverseMapping[$apiPropertyName] = $cqrsPropertyName;
            }
        }

        // Identify multi-parameter setter properties
        // These are special setters that accept multiple parameters (e.g., setDate(year, month, day))
        // They cannot be validated against a single property, so we preserve them in the schema
        // regardless of property existence checks
        $multiParameterSetterProperties = [];
        if (class_exists($classToUse) && $this->classMetadataFactory->hasMetadataFor($classToUse) && $this->domainObjectDetector->isDomainObject($classToUse)) {
            $operationClassMetadata = $this->classMetadataFactory->getMetadataFor($classToUse);
            $operationReflectionClass = $operationClassMetadata->getReflectionClass();
            $methodsWithMultipleArguments = $this->findMethodsWithMultipleArguments($operationReflectionClass);
            foreach ($methodsWithMultipleArguments as $methodPropertyName => $setterMethod) {
                $multiParameterSetterProperties[] = $methodPropertyName;
            }
        }

        // Extract properties from the API resource class (not the command class)
        // This is used to validate that properties in the schema actually exist in the resource
        // For example, if a property exists in the command but not in the resource, it shouldn't appear in GET responses
        $apiResourceProperties = [];
        $apiResourceClass = $operation->getClass();
        if (class_exists($apiResourceClass)) {
            $apiResourceProperties = $this->getResourceProperties($apiResourceClass);
        }

        $currentProperties = $definition['properties'];
        $synchronizedProperties = [];

        foreach ($currentProperties as $propertyName => $propertySchema) {
            // Special case: multi-parameter setter properties are always kept
            // These are handled specially by MultiParameterSetterAdapter and cannot be validated
            // against a single property, so we preserve them
            if (in_array($propertyName, $multiParameterSetterProperties)) {
                $synchronizedProperties[$propertyName] = $propertySchema;
                continue;
            }

            // If we have API resource properties, validate that the property exists in the resource
            // This ensures properties that only exist in commands don't appear in GET responses
            if (!in_array($propertyName, $apiResourceProperties)) {
                // Property doesn't exist in the resource, skip it
                continue;
            }

            // Map the API property name to the command property name if a mapping exists
            $commandPropertyName = isset($reverseMapping[$propertyName]) ? $reverseMapping[$propertyName] : $propertyName;

            // Keep the property if:
            // 1. The mapped command property exists in the actual properties, OR
            // 2. There's a mapping defined (meaning it's a valid mapped property, even if the command property name doesn't match)
            if (in_array($commandPropertyName, $actualProperties) || isset($reverseMapping[$propertyName])) {
                $synchronizedProperties[$propertyName] = $propertySchema;
            }
        }
        $definition['properties'] = $synchronizedProperties;

        // Also synchronize the 'required' properties list
        // Remove any required properties that don't exist in the actual resource/command
        if (!empty($definition['required'])) {
            $mappedRequired = [];
            foreach ($definition['required'] as $requiredProperty) {
                $commandPropertyName = isset($reverseMapping[$requiredProperty]) ? $reverseMapping[$requiredProperty] : $requiredProperty;
                if (in_array($commandPropertyName, $actualProperties)) {
                    $mappedRequired[] = $requiredProperty;
                }
            }
            $definition['required'] = array_values($mappedRequired);
        }
    }

    /**
     * Extracts all property names from a resource class.
     * Uses PropertyInfoExtractor first (preferred method), falls back to reflection if that fails.
     *
     * @return array<string> Array of property names (excluding JSON-LD context properties)
     */
    protected function getResourceProperties(string $resourceClass): array
    {
        try {
            $properties = $this->propertyInfoExtractor->getProperties($resourceClass) ?? [];

            if (!empty($properties)) {
                return array_filter($properties, function ($property) {
                    return !in_array($property, ['@context', '@id', '@type']);
                });
            }
        } catch (Throwable $e) {
            return $this->getPropertiesUsingReflection($resourceClass);
        }

        return $this->getPropertiesUsingReflection($resourceClass);
    }

    /**
     * Extracts property names from a class using PHP reflection as a fallback method.
     * This method identifies properties by:
     * 1. Public non-static properties
     * 2. Getter methods (getXxx, isXxx, hasXxx) with no parameters
     *
     * @return array<string> Array of property names found via reflection
     */
    protected function getPropertiesUsingReflection(string $resourceClass): array
    {
        try {
            $reflection = new ReflectionClass($resourceClass);
            $properties = [];

            foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
                if (!$property->isStatic()) {
                    $properties[] = $property->getName();
                }
            }

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $methodName = $method->getName();

                if ($method->isStatic() || $method->isConstructor() || $method->getNumberOfParameters() > 0) {
                    continue;
                }

                if (str_starts_with($methodName, 'get') && strlen($methodName) > 3) {
                    $propertyName = lcfirst(substr($methodName, 3));
                    if (!in_array($propertyName, $properties)) {
                        $properties[] = $propertyName;
                    }
                }

                if (str_starts_with($methodName, 'is') && strlen($methodName) > 2) {
                    $propertyName = lcfirst(substr($methodName, 2));
                    if (!in_array($propertyName, $properties)) {
                        $properties[] = $propertyName;
                    }
                }

                if (str_starts_with($methodName, 'has') && strlen($methodName) > 3) {
                    $propertyName = lcfirst(substr($methodName, 3));
                    if (!in_array($propertyName, $properties)) {
                        $properties[] = $propertyName;
                    }
                }
            }

            return array_unique($properties);
        } catch (ReflectionException $e) {
            return [];
        }
    }

    /**
     * Finds all public methods that accept multiple required parameters.
     * These are typically multi-parameter setters like setDate(year, month, day) or setAddress(street, city, zip).
     * These methods cannot be validated against a single property, so they need special handling.
     *
     * The method name is converted to a property name by:
     * - Removing 'set' prefix: setDate() -> 'date'
     * - Removing 'with' prefix: withDate() -> 'date'
     * - Using full method name if no prefix matches
     *
     * @return array<string, ReflectionMethod> Associative array mapping property names to their ReflectionMethod objects
     */
    protected function findMethodsWithMultipleArguments(ReflectionClass $reflectionClass): array
    {
        $methodsWithMultipleArguments = [];
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            // We only look into public method that can be setters with multiple parameters
            if (
                $reflectionMethod->getNumberOfParameters() <= 1
                || $reflectionMethod->isStatic()
                || $reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
            ) {
                continue;
            }

            // Remove set/with to get the potential matching property in data (use full method name by default)
            if (str_starts_with($reflectionMethod->getName(), 'set')) {
                $methodPropertyName = lcfirst(substr($reflectionMethod->getName(), 3));
            } elseif (str_starts_with($reflectionMethod->getName(), 'with')) {
                $methodPropertyName = lcfirst(substr($reflectionMethod->getName(), 4));
            } else {
                $methodPropertyName = $reflectionMethod->getName();
            }
            $methodsWithMultipleArguments[$methodPropertyName] = $reflectionMethod;
        }

        return $methodsWithMultipleArguments;
    }
}
