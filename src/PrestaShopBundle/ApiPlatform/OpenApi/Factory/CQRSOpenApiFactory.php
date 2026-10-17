<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform\OpenApi\Factory;

use ApiPlatform\JsonSchema\DefinitionNameFactoryInterface;
use ApiPlatform\JsonSchema\ResourceMetadataTrait;
use ApiPlatform\JsonSchema\Schema;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Components;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\Model\Response;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShopBundle\ApiPlatform\OpenApi\Adapter\SchemaAdapterChain;
use PrestaShopBundle\ApiPlatform\OpenApi\FileUploadRequestBodyBuilder;
use PrestaShopBundle\ApiPlatform\Provider\QueryListProvider;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;

/**
 * This service decorates the main service that builds the Open API schema. It waits for the whole generation
 * to be done so that all types, schemas and example are correctly extracted and then:
 *
 *   - it applies the custom mapping, when defined, so that the schema reflects the expected format for the API,
 *     not the one in the domain logic from CQRS commands
 * . - it groups endpoints by domain and
 *   - it adapts some custom types like DecimalNumber and document them as numbers
 *   - it handles multi parameters setters and split the parameters into a sub object like the Admin API expects
 *   - it detects LocalizedValue fields and adapt their format and example
 *   - it synchronizes the documentation with actual resource properties (removes undocumented fields)
 */
class CQRSOpenApiFactory implements OpenApiFactoryInterface
{
    use ResourceMetadataTrait;

    protected PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        protected readonly OpenApiFactoryInterface $decorated,
        protected readonly ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        protected readonly DefinitionNameFactoryInterface $definitionNameFactory,
        protected readonly MultistoreFeature $multistoreFeature,
        protected readonly SchemaAdapterChain $resourceSchemaAdapterChain,
        protected readonly SchemaAdapterChain $operationSchemaAdapterChain,
        protected readonly FileUploadRequestBodyBuilder $fileUploadRequestBodyBuilder,
        protected readonly PropertyTypeExtractorInterface $propertyTypeExtractor,
        // No property promotion for this one since it's already defined in the ResourceMetadataTrait
        ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory,
    ) {
        $this->resourceMetadataFactory = $resourceMetadataFactory;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidIndex()
            ->disableExceptionOnInvalidPropertyPath()
            ->getPropertyAccessor()
        ;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $parentOpenApi = $this->decorated->__invoke($context);
        $domainsByUri = [];
        $scopesByUri = [];
        $integerUriVariablesByUri = [];
        $uploadedFilesByUri = [];
        $collectionsByUri = [];
        $adaptedClasses = [];

        foreach ($this->resourceNameCollectionFactory->create() as $resourceClass) {
            $resourceMetadataCollection = $this->resourceMetadataFactory->create($resourceClass);
            foreach ($resourceMetadataCollection as $resourceMetadata) {
                $resourceDefinitionName = $this->definitionNameFactory->create($resourceMetadata->getClass());

                // Adapt schema for API resource schema (mostly for read schema)
                if ($parentOpenApi->getComponents()->getSchemas()->offsetExists($resourceDefinitionName)) {
                    $resourceSchema = $parentOpenApi->getComponents()->getSchemas()->offsetGet($resourceDefinitionName);
                    $this->resourceSchemaAdapterChain->adapt($resourceMetadata->getClass(), $resourceSchema, null);
                    $adaptedClasses[$resourceMetadata->getClass()] = true;
                    $this->adaptSubObjectSchemas($parentOpenApi, $resourceMetadata->getClass(), $adaptedClasses);
                }

                /** @var Operation $operation */
                foreach ($resourceMetadata->getOperations() as $operation) {
                    // For each URI define the expected domain (we want to avoid splitting domains because they are based on multiple API resource classes)
                    if ($operation instanceof HttpOperation && !empty($operation->getUriTemplate())) {
                        $operationDomain = $this->getOperationDomain($operation);
                        if (!empty($operationDomain) && empty($domainsByUri[$operation->getUriTemplate()])) {
                            $domainsByUri[$operation->getUriTemplate()] = $this->getOperationDomain($operation);
                        }
                    }
                    if ($operation instanceof HttpOperation && !empty($operation->getUriTemplate())) {
                        $integerUriVariables = $this->getIntegerUriVariables($operation);
                        if (!empty($integerUriVariables)) {
                            $integerUriVariablesByUri[$operation->getUriTemplate()][strtolower($operation->getMethod())] = $integerUriVariables;
                        }
                    }
                    if ($operation instanceof HttpOperation && !empty($operation->getExtraProperties()['scopes'])) {
                        $scopesByUri[$operation->getUriTemplate()][strtolower($operation->getMethod())] = $operation->getExtraProperties()['scopes'];
                    }
                    if ($operation instanceof HttpOperation && $operation instanceof CollectionOperationInterface && !empty($operation->getUriTemplate())) {
                        $collectionsByUri[$operation->getUriTemplate()][strtolower($operation->getMethod())] = QueryListProvider::class === $operation->getProvider();
                    }
                    // Detect uploaded file properties for multipart/form-data operations, they are documented in the paths loop below
                    if ($operation instanceof HttpOperation && !empty($operation->getUriTemplate())) {
                        $uploadedFileProperties = $this->fileUploadRequestBodyBuilder->getUploadedFileProperties($operation);
                        if (!empty($uploadedFileProperties)) {
                            $uploadedFilesByUri[$operation->getUriTemplate()][strtolower($operation->getMethod())] = $uploadedFileProperties;
                        }
                    }

                    $definition = $this->getSchemaDefinition($parentOpenApi, $operation);
                    if (!$definition) {
                        continue;
                    }

                    $this->operationSchemaAdapterChain->adapt($operation->getClass(), $definition, $operation);
                }
            }
        }

        // Rebuild all the paths so that they are grouped by domain, the tags must be updated but since the Object are immutable
        // we have to loop through all the existing paths and create modified clones
        $updatedPaths = new Paths();
        /** @var PathItem $pathItem */
        foreach ($parentOpenApi->getPaths()->getPaths() as $path => $pathItem) {
            // Get operations that are defined (not null)
            $operations = array_filter([
                'get' => $pathItem->getGet(),
                'post' => $pathItem->getPost(),
                'put' => $pathItem->getPut(),
                'patch' => $pathItem->getPatch(),
                'delete' => $pathItem->getDelete(),
            ], fn ($operation) => null !== $operation);

            $updatedPathItem = $pathItem;
            if (!empty($operations)) {
                /** @var OpenApiOperation $operation */
                foreach ($operations as $httpMethod => $operation) {
                    $updatedOperation = $operation;
                    // Update tag to group by domain
                    if (!empty($domainsByUri[$path])) {
                        $updatedOperation = $operation->withTags([$domainsByUri[$path]]);
                    }
                    // Add security scopes
                    if (!empty($scopesByUri[$path][$httpMethod])) {
                        $updatedOperation = $updatedOperation->withSecurity([['oauth' => $scopesByUri[$path][$httpMethod]]]);
                    }

                    // The identifiers in the path are integers, ApiPlatform documents every path parameter as a string
                    if (!empty($integerUriVariablesByUri[$path][$httpMethod]) && !empty($updatedOperation->getParameters())) {
                        $updatedOperation = $updatedOperation->withParameters(array_map(
                            fn (Parameter $parameter) => 'path' === $parameter->getIn() && in_array($parameter->getName(), $integerUriVariablesByUri[$path][$httpMethod], true)
                                ? $parameter->withSchema(['type' => 'integer'])
                                : $parameter,
                            $updatedOperation->getParameters()
                        ));
                    }

                    if (isset($collectionsByUri[$path][$httpMethod])) {
                        $updatedOperation = $this->adaptCollectionOperation($updatedOperation, $collectionsByUri[$path][$httpMethod]);
                    }

                    // Add multishop context parameters if needed
                    if ($this->multistoreFeature->isActive()) {
                        $existingParameters = $updatedOperation->getParameters() ?? [];
                        $updatedOperation = $updatedOperation->withParameters(array_merge($existingParameters, $this->getMultiShopParameters()));
                    }

                    // Document uploaded file properties in the multipart/form-data request body
                    if (!empty($uploadedFilesByUri[$path][$httpMethod])) {
                        $updatedOperation = $this->fileUploadRequestBodyBuilder->adaptOperation(
                            $updatedOperation,
                            $parentOpenApi->getComponents()->getSchemas(),
                            $uploadedFilesByUri[$path][$httpMethod],
                        );
                    }

                    $setterMethod = 'with' . ucfirst($httpMethod);
                    $updatedPathItem = $updatedPathItem->$setterMethod($updatedOperation);
                }
            }
            $updatedPaths->addPath($path, $updatedPathItem);
        }

        return new OpenApi(
            $parentOpenApi->getInfo(),
            [
                new Server('/admin-api'),
            ],
            $updatedPaths,
            $this->removeUnusedSchemas($parentOpenApi->getComponents(), $updatedPaths),
            $parentOpenApi->getSecurity(),
            $parentOpenApi->getTags(),
            $parentOpenApi->getExternalDocs(),
            $parentOpenApi->getJsonSchemaDialect(),
            $parentOpenApi->getWebhooks(),
        );
    }

    /**
     * The sub objects of a resource (DTOs typed on its properties, or in a Foo[] list) have their own schema, it is
     * adapted like the resource one (e.g. a DecimalNumber documented as a number).
     */
    protected function adaptSubObjectSchemas(OpenApi $openApi, string $class, array &$adaptedClasses): void
    {
        foreach ((new ReflectionClass($class))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            foreach ($this->propertyTypeExtractor->getTypes($class, $property->getName()) ?? [] as $type) {
                $subObjectClass = $type->getClassName() ?? ($type->getCollectionValueTypes()[0] ?? null)?->getClassName();
                if (null === $subObjectClass || isset($adaptedClasses[$subObjectClass]) || !str_contains($subObjectClass, '\\ApiPlatform\\Resources\\')) {
                    continue;
                }

                $adaptedClasses[$subObjectClass] = true;
                $definitionName = $this->definitionNameFactory->create($subObjectClass);
                if ($openApi->getComponents()->getSchemas()->offsetExists($definitionName)) {
                    $this->resourceSchemaAdapterChain->adapt($subObjectClass, $openApi->getComponents()->getSchemas()->offsetGet($definitionName), null);
                    $this->adaptSubObjectSchemas($openApi, $subObjectClass, $adaptedClasses);
                }
            }
        }
    }

    /**
     * The collections are not paginated by API Platform: its page parameter is ignored. The paginated lists (based on
     * QueryListProvider) read limit, offset, orderBy, sortOrder and filters, and wrap their items in an envelope.
     */
    protected function adaptCollectionOperation(OpenApiOperation $operation, bool $paginated): OpenApiOperation
    {
        $parameters = array_values(array_filter(
            $operation->getParameters() ?? [],
            fn (Parameter $parameter) => !('query' === $parameter->getIn() && 'page' === $parameter->getName())
        ));
        if (!$paginated) {
            return $operation->withParameters($parameters);
        }

        $parameters = array_merge($parameters, [
            new Parameter('limit', 'query', 'Maximum number of items returned', false, false, false, ['type' => 'integer']),
            new Parameter('offset', 'query', 'Number of items skipped', false, false, false, ['type' => 'integer', 'default' => 0]),
            new Parameter('orderBy', 'query', 'Field used to sort the items', false, false, false, ['type' => 'string']),
            new Parameter('sortOrder', 'query', 'Sort direction', false, false, false, ['type' => 'string', 'enum' => ['asc', 'desc'], 'default' => 'asc']),
            new Parameter('filters', 'query', 'Filters on the item fields, e.g. filters[fieldName]=value', false, false, false, ['type' => 'object', 'additionalProperties' => true], 'deepObject', true),
        ]);
        $operation = $operation->withParameters($parameters);

        $responses = $operation->getResponses() ?? [];
        foreach ($responses as $statusCode => $response) {
            if (!$response instanceof Response || null === $response->getContent()) {
                continue;
            }

            $content = clone $response->getContent();
            foreach ($content as $mediaTypeName => $mediaType) {
                $itemsSchema = $mediaType->getSchema();
                if (null === $itemsSchema || 'array' !== ($itemsSchema['type'] ?? null)) {
                    continue;
                }

                $content[$mediaTypeName] = $mediaType->withSchema(new ArrayObject([
                    'type' => 'object',
                    'properties' => [
                        'totalItems' => ['type' => 'integer'],
                        'orderBy' => ['type' => ['string', 'null']],
                        'sortOrder' => ['type' => ['string', 'null']],
                        'limit' => ['type' => 'integer'],
                        'offset' => ['type' => 'integer'],
                        'filters' => ['type' => 'object'],
                        'items' => $itemsSchema->getArrayCopy(),
                    ],
                ]));
            }
            $responses[$statusCode] = $response->withContent($content);
        }

        return $operation->withResponses($responses);
    }

    /**
     * Some schemas are generated but never referenced by the documented operations (value objects of the CQRS commands,
     * input of the operations only accepting multipart/form-data whose body is documented inline): they are removed.
     */
    protected function removeUnusedSchemas(Components $components, Paths $paths): Components
    {
        $schemas = $components->getSchemas();
        $usedSchemas = [];
        $schemasToScan = $this->getReferencedSchemas($paths->getPaths());
        while (!empty($schemasToScan)) {
            $schemaName = array_pop($schemasToScan);
            if (isset($usedSchemas[$schemaName]) || !$schemas->offsetExists($schemaName)) {
                continue;
            }
            $usedSchemas[$schemaName] = true;
            array_push($schemasToScan, ...$this->getReferencedSchemas($schemas->offsetGet($schemaName)));
        }

        $filteredSchemas = new ArrayObject();
        foreach ($schemas as $schemaName => $schema) {
            if (isset($usedSchemas[$schemaName])) {
                $filteredSchemas[$schemaName] = $schema;
            }
        }

        return $components->withSchemas($filteredSchemas);
    }

    /**
     * @return string[]
     */
    protected function getReferencedSchemas(mixed $data): array
    {
        if (is_string($data)) {
            return str_starts_with($data, '#/components/schemas/') ? [substr($data, strlen('#/components/schemas/'))] : [];
        }
        if (!is_array($data) && !is_object($data)) {
            return [];
        }

        // The OpenAPI model objects have private properties, an array cast exposes them (and the ArrayObject storage)
        $referencedSchemas = [];
        foreach ((array) $data as $value) {
            array_push($referencedSchemas, ...$this->getReferencedSchemas($value));
        }

        return $referencedSchemas;
    }

    /**
     * Returns the URI variables that are integers: matching a digits only requirement, or an int property of the resource.
     *
     * @return string[]
     */
    protected function getIntegerUriVariables(HttpOperation $operation): array
    {
        $integerUriVariables = [];
        foreach (array_keys($operation->getUriVariables() ?? []) as $uriVariable) {
            $requirement = $operation->getRequirements()[$uriVariable] ?? null;
            if (null !== $requirement) {
                if (in_array($requirement, ['\\d+', '[0-9]+'], true)) {
                    $integerUriVariables[] = $uriVariable;
                }

                continue;
            }

            $class = $operation->getClass();
            if (null !== $class && property_exists($class, $uriVariable)) {
                $type = (new ReflectionProperty($class, $uriVariable))->getType();
                if ($type instanceof ReflectionNamedType && 'int' === $type->getName()) {
                    $integerUriVariables[] = $uriVariable;
                }
            }
        }

        return $integerUriVariables;
    }

    /**
     * Returns the list of multishop context parameters added to each operation.
     *
     * @return Parameter[]
     */
    protected function getMultiShopParameters(): array
    {
        return [
            new Parameter('shopId', 'query', 'Shop identifier for multistore context', false, false, false, ['type' => 'integer']),
            new Parameter('shopGroupId', 'query', 'Shop group identifier for multistore context', false, false, false, ['type' => 'integer']),
            new Parameter('shopIds', 'query', 'Comma separated list of shop identifiers for multistore context', false, false, false, ['type' => 'string']),
            new Parameter('allShops', 'query', 'Use all shops context', false, false, false, ['type' => 'integer', 'enum' => ['1']]),
        ];
    }

    protected function getSchemaDefinition(OpenApi $openApi, Operation $operation): ?ArrayObject
    {
        $inputClass = $this->findOutputClass($operation->getClass(), Schema::TYPE_INPUT, $operation, []);
        if (null === $inputClass) {
            return null;
        }

        // Build the operation name like SchemaFactory does so that we have the proper definition in the schema matching this operation
        $operationSchemaDefinitionName = $this->definitionNameFactory->create($operation->getClass(), 'json', $inputClass, $operation, []);
        if (!$openApi->getComponents()->getSchemas()->offsetExists($operationSchemaDefinitionName)) {
            return null;
        }

        /** @var ArrayObject $definition */
        $definition = $openApi->getComponents()->getSchemas()->offsetGet($operationSchemaDefinitionName);
        if (empty($definition['properties'])) {
            return null;
        }

        return $definition;
    }

    /**
     * Deduce the domain from the FQCN, for classes that are at the root of ApiPlatform\Resources the class name is used,
     * but if the domain was placed in a subspace with multiple classes in it we use the last sub namespace.
     *
     * ex:
     *      PrestaShopBundle\ApiPlatform\Resources\ApiClient => ApiClient
     *      PrestaShop\Module\APIResources\ApiPlatform\Resources\ApiClient\ApiClient => ApiClient
     *      PrestaShop\Module\APIResources\ApiPlatform\Resources\ApiClient\ApiClientList => ApiClient
     *      PrestaShop\Module\APIResources\ApiPlatform\Resources\Product\Product => Product
     *      PrestaShop\Module\APIResources\ApiPlatform\Resources\Product\ProductList => Product
     */
    protected function getOperationDomain(HttpOperation $operation): ?string
    {
        // All our domain API resources (in the core and in the module) are in a sub namespace ending with ApiPlatform\Resources
        if (!str_contains($operation->getClass(), 'ApiPlatform\Resources\\')) {
            return null;
        }

        // Get the last part of the FQCN after ApiPlatform\Resources
        $domainEnd = substr($operation->getClass(), strrpos($operation->getClass(), 'ApiPlatform\Resources\\') + strlen('ApiPlatform\Resources\\'));
        if (empty($domainEnd)) {
            return null;
        }

        // If the remaining part is only the class name we can use it for the domain
        if (!str_contains($domainEnd, '\\')) {
            return $domainEnd;
        }

        // If not we use the last namespace
        $splitDomain = explode('\\', $domainEnd);

        return $splitDomain[count($splitDomain) - 2];
    }
}
