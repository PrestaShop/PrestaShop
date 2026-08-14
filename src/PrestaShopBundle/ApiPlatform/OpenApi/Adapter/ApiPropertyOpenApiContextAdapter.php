<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use ArrayObject;
use Throwable;

/**
 * Applies the openapiContext declared on the ApiProperty attributes of the API resource, it always wins over the
 * schema detected automatically.
 *
 * ApiPlatform natively applies that context on the resource schema, the one used by the read operations, but not on
 * the schemas of the write operations: those are built from the CQRS command, whose properties carry no ApiProperty
 * attribute, so the format detected from the command was used even when the resource documented an explicit one. A
 * collection of objects declared via openapiContext, for example, ended up documented as a collection of strings in
 * the request body although the response documented it correctly.
 *
 * This adapter runs after the ones detecting formats automatically (localized values, decimal numbers, dates, ...) so
 * that an explicit context is never overridden by a guessed format.
 */
class ApiPropertyOpenApiContextAdapter implements OpenApiSchemaAdapterInterface
{
    public function __construct(
        protected readonly PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        protected readonly PropertyMetadataFactoryInterface $propertyMetadataFactory,
    ) {
    }

    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (!class_exists($class) || empty($definition['properties'])) {
            return;
        }

        foreach ($this->getOpenApiContexts($class) as $propertyName => $openApiContext) {
            $properties = $definition['properties'];
            if (!isset($properties[$propertyName])) {
                continue;
            }

            // The property definition is either an ArrayObject modified in place or an array assigned back
            $property = $properties[$propertyName];
            if ($property instanceof ArrayObject) {
                foreach ($openApiContext as $contextKey => $contextValue) {
                    $property[$contextKey] = $contextValue;
                }

                continue;
            }

            $properties[$propertyName] = array_merge((array) $property, $openApiContext);
            $definition['properties'] = $properties;
        }
    }

    /**
     * @return array<string, array<string, mixed>> the non empty openapiContext of each property of the resource
     */
    protected function getOpenApiContexts(string $resourceClass): array
    {
        try {
            $propertyNames = $this->propertyNameCollectionFactory->create($resourceClass);
        } catch (Throwable) {
            // The class is not an API resource, it has no documented property of its own
            return [];
        }

        $openApiContexts = [];
        foreach ($propertyNames as $propertyName) {
            $openApiContext = $this->propertyMetadataFactory->create($resourceClass, $propertyName)->getOpenapiContext();
            if (!empty($openApiContext)) {
                $openApiContexts[$propertyName] = $openApiContext;
            }
        }

        return $openApiContexts;
    }
}
