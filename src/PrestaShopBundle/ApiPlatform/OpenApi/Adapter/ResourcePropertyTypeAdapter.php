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
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;

/**
 * Types the request body properties left untyped by the CQRS command (a legacy constructor parameter without type
 * declaration, like AddCustomerCommand::$firstName), using the native type of the matching API resource property,
 * and types the items of a scalar list from the resource docblock (`@var int[]`): the command declares a plain
 * array or a list of value objects, which the generator documents as strings.
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

    public function __construct(
        protected readonly PropertyInfoExtractorInterface $propertyInfoExtractor,
    ) {
    }

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
            if (!property_exists($resourceClass, $propertyName)) {
                continue;
            }

            if (!$this->isTyped($propertySchema)) {
                $type = (new ReflectionProperty($resourceClass, $propertyName))->getType();
                if (!$type instanceof ReflectionNamedType || null === ($openApiType = $this->getOpenApiType($type->getName()))) {
                    continue;
                }

                $propertySchema['type'] = $type->allowsNull() ? [$openApiType, 'null'] : $openApiType;
                if (is_subclass_of($type->getName(), DateTimeInterface::class) || DateTimeInterface::class === $type->getName()) {
                    $propertySchema['format'] = 'date-time';
                }
            } elseif ($this->isArray($propertySchema) && empty($propertySchema['items']['$ref'])) {
                $itemType = $this->getScalarItemType($resourceClass, $propertyName);
                if (null === $itemType) {
                    continue;
                }

                $items = $propertySchema['items'] ?? [];
                $items['type'] = $itemType;
                $propertySchema['items'] = $items;
            } else {
                continue;
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

    protected function isArray(mixed $propertySchema): bool
    {
        $type = $propertySchema['type'] ?? null;

        return 'array' === $type || (is_array($type) && in_array('array', $type, true));
    }

    /**
     * The OpenAPI type of the items of a scalar list (`@var int[]`), null for a list of objects or an untyped list.
     */
    protected function getScalarItemType(string $resourceClass, string $propertyName): ?string
    {
        foreach ($this->propertyInfoExtractor->getTypes($resourceClass, $propertyName) ?? [] as $type) {
            if (!$type->isCollection()) {
                continue;
            }

            foreach ($type->getCollectionValueTypes() as $valueType) {
                $builtinType = $valueType->getBuiltinType();
                if (Type::BUILTIN_TYPE_ARRAY === $builtinType || Type::BUILTIN_TYPE_OBJECT === $builtinType) {
                    return null;
                }
                if (isset(self::OPENAPI_TYPES[$builtinType])) {
                    return self::OPENAPI_TYPES[$builtinType];
                }
            }
        }

        return null;
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
