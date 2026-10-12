<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ArrayObject;
use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use ReflectionNamedType;
use ReflectionProperty;

/**
 * Types the request body properties left untyped by the CQRS command (a legacy constructor parameter without type
 * declaration, like AddCustomerCommand::$firstName), using the native type of the matching API resource property.
 */
class ResourcePropertyTypeAdapter implements OpenApiSchemaAdapterInterface
{
    protected const OPENAPI_TYPES = [
        'int' => 'integer',
        'float' => 'number',
        'string' => 'string',
        'bool' => 'boolean',
        'array' => 'array',
    ];

    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (null === $operation || empty($definition['properties'])) {
            return;
        }

        // Only the request body built from the CQRS command, the resource schema is typed from the resource itself
        $commandClass = is_array($operation->getInput()) ? ($operation->getInput()['class'] ?? null) : null;
        $resourceClass = $operation->getClass();
        if (null === $commandClass || null === $resourceClass || $commandClass === $resourceClass) {
            return;
        }

        $properties = $definition['properties'];
        foreach ($properties as $propertyName => $propertySchema) {
            if ($this->isTyped($propertySchema) || !property_exists($resourceClass, $propertyName)) {
                continue;
            }

            $type = (new ReflectionProperty($resourceClass, $propertyName))->getType();
            if (!$type instanceof ReflectionNamedType || null === ($openApiType = $this->getOpenApiType($type->getName()))) {
                continue;
            }

            $propertySchema['type'] = $type->allowsNull() ? [$openApiType, 'null'] : $openApiType;
            if (is_subclass_of($type->getName(), DateTimeInterface::class) || DateTimeInterface::class === $type->getName()) {
                $propertySchema['format'] = 'date-time';
            }
            $properties[$propertyName] = $propertySchema;
        }
        $definition['properties'] = $properties;
    }

    protected function isTyped(mixed $propertySchema): bool
    {
        foreach (['type', '$ref', 'anyOf', 'allOf', 'oneOf'] as $typeKey) {
            if (isset($propertySchema[$typeKey])) {
                return true;
            }
        }

        return false;
    }

    protected function getOpenApiType(string $phpType): ?string
    {
        if (isset(self::OPENAPI_TYPES[$phpType])) {
            return self::OPENAPI_TYPES[$phpType];
        }
        if (DecimalNumber::class === $phpType || is_subclass_of($phpType, DecimalNumber::class)) {
            return 'number';
        }
        if (DateTimeInterface::class === $phpType || is_subclass_of($phpType, DateTimeInterface::class)) {
            return 'string';
        }

        return null;
    }
}
