<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ArrayObject;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use Throwable;

/**
 * Documents the `extraProperties` sub-object in the generated OpenAPI schema.
 *
 * It mirrors the runtime behaviour of the extra property API bridge: a definition is documented on an endpoint
 * only when its associatedApis matches that endpoint's URI template. The sub-object is grouped by module
 * technical name, then by (snake_case) property name; LANG-scope fields are documented as locale-indexed objects.
 *
 * Two phases (see CQRSOpenApiFactory):
 *   - resource phase ($operation === null): the read/output schema — documents the union of definitions matching
 *     any of the resource's operations.
 *   - operation phase ($operation !== null): the input schema — documents definitions matching that write operation.
 *
 * Registered with a very low priority so it runs AFTER SchemaSynchronizer (which strips properties that are not
 * declared on the resource class) — otherwise the synthetic `extraProperties` property would be removed again.
 */
class ExtraPropertiesSchemaAdapter implements OpenApiSchemaAdapterInterface
{
    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH'];

    public function __construct(
        protected readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        protected readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory,
    ) {
    }

    public function adapt(string $class, ArrayObject $definition, ?Operation $operation = null): void
    {
        if (!isset($definition['properties'])) {
            return;
        }

        if (null === $operation) {
            $definitions = $this->definitionsForResource($class);
        } else {
            // The operation phase adapts input (write) schemas; a GET has no request body to document.
            if (!$operation instanceof HttpOperation || !in_array(strtoupper((string) $operation->getMethod()), self::WRITE_METHODS, true)) {
                return;
            }
            $definitions = $this->definitionsForOperation($operation);
        }

        if (null === $definitions || $definitions->isEmpty()) {
            return;
        }

        $properties = $definition['properties'];
        $properties['extraProperties'] = $this->buildExtraPropertiesSchema($definitions);
        $definition['properties'] = $properties;
    }

    protected function definitionsForOperation(HttpOperation $operation): ?ExtraPropertyDefinitionCollection
    {
        $uriTemplate = (string) $operation->getUriTemplate();
        if ('' === $uriTemplate) {
            return null;
        }

        return $this->repository->getAllDefinitions()->filterByApi($uriTemplate, (string) $operation->getMethod());
    }

    /**
     * Returns the union of definitions matching any operation of the given resource (deduplicated).
     */
    protected function definitionsForResource(string $resourceClass): ?ExtraPropertyDefinitionCollection
    {
        $allDefinitions = $this->repository->getAllDefinitions();
        if ($allDefinitions->isEmpty()) {
            return null;
        }

        try {
            $metadataCollection = $this->resourceMetadataFactory->create($resourceClass);
        } catch (Throwable) {
            return null;
        }

        $matched = [];
        $seen = [];
        foreach ($metadataCollection as $resourceMetadata) {
            foreach ($resourceMetadata->getOperations() ?? [] as $operation) {
                if (!$operation instanceof HttpOperation) {
                    continue;
                }
                $uriTemplate = (string) $operation->getUriTemplate();
                if ('' === $uriTemplate) {
                    continue;
                }
                foreach ($allDefinitions->filterByApi($uriTemplate, (string) $operation->getMethod()) as $definition) {
                    $key = $definition->getEntityName() . '|' . $definition->getNormalizedModuleKey() . '|' . $definition->getPropertyName();
                    if (!isset($seen[$key])) {
                        $seen[$key] = true;
                        $matched[] = $definition;
                    }
                }
            }
        }

        return new ExtraPropertyDefinitionCollection($matched);
    }

    protected function buildExtraPropertiesSchema(ExtraPropertyDefinitionCollection $definitions): ArrayObject
    {
        $moduleProperties = [];
        foreach ($definitions as $definition) {
            $moduleKey = $definition->getNormalizedModuleKey();
            if (!isset($moduleProperties[$moduleKey])) {
                $moduleProperties[$moduleKey] = ['type' => 'object', 'properties' => []];
            }
            $moduleProperties[$moduleKey]['properties'][$definition->getPropertyName()] = $this->buildFieldSchema($definition);
            // A definition flagged required is reported in its module object's OpenAPI "required" list — the same
            // flag that marks the BO form field required (ExtraPropertyDefinition::isRequired()).
            if ($definition->isRequired()) {
                $moduleProperties[$moduleKey]['required'][] = $definition->getPropertyName();
            }
        }

        return new ArrayObject([
            'type' => 'object',
            'description' => 'Module-declared extra properties, grouped by module technical name then by property name.',
            'properties' => $moduleProperties,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildFieldSchema(ExtraPropertyDefinition $definition): array
    {
        // LANG-scope values are exposed/expected as locale-indexed objects, whatever the underlying type.
        if (ExtraPropertyScope::LANG === $definition->getScope()) {
            return [
                'type' => 'object',
                'example' => ['en-US' => 'value', 'fr-FR' => 'valeur'],
            ];
        }

        return match ($definition->getType()) {
            ExtraPropertyType::INT => ['type' => 'integer'],
            ExtraPropertyType::BOOL => ['type' => 'boolean'],
            ExtraPropertyType::FLOAT => ['type' => 'number'],
            ExtraPropertyType::DATE => ['type' => 'string', 'format' => 'date-time'],
            ExtraPropertyType::JSON => ['type' => 'object'],
            ExtraPropertyType::CHOICE => $this->buildChoiceSchema($definition),
            default => ['type' => 'string'],
        };
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildChoiceSchema(ExtraPropertyDefinition $definition): array
    {
        $schema = ['type' => 'string'];
        $enumValues = $definition->getEnumValues();
        if (!empty($enumValues)) {
            $schema['enum'] = array_values($enumValues);
        }

        return $schema;
    }
}
