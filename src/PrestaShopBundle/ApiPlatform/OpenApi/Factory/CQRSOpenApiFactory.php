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

namespace PrestaShopBundle\ApiPlatform\OpenApi\Factory;

use ApiPlatform\JsonSchema\DefinitionNameFactoryInterface;
use ApiPlatform\JsonSchema\ResourceMetadataTrait;
use ApiPlatform\JsonSchema\Schema;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShopBundle\ApiPlatform\OpenApi\Adapter\SchemaAdapterChain;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

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

        foreach ($this->resourceNameCollectionFactory->create() as $resourceClass) {
            $resourceMetadataCollection = $this->resourceMetadataFactory->create($resourceClass);
            foreach ($resourceMetadataCollection as $resourceMetadata) {
                $resourceDefinitionName = $this->definitionNameFactory->create($resourceMetadata->getClass());

                // Adapt schema for API resource schema (mostly for read schema)
                if ($parentOpenApi->getComponents()->getSchemas()->offsetExists($resourceDefinitionName)) {
                    $resourceSchema = $parentOpenApi->getComponents()->getSchemas()->offsetGet($resourceDefinitionName);
                    $this->resourceSchemaAdapterChain->adapt($resourceMetadata->getClass(), $resourceSchema, null);
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
                    if ($operation instanceof HttpOperation && !empty($operation->getExtraProperties()['scopes'])) {
                        $scopesByUri[$operation->getUriTemplate()][strtolower($operation->getMethod())] = $operation->getExtraProperties()['scopes'];
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

                    // Add multishop context parameters if needed
                    if ($this->multistoreFeature->isActive()) {
                        $existingParameters = $updatedOperation->getParameters() ?? [];
                        $updatedOperation = $updatedOperation->withParameters(array_merge($existingParameters, $this->getMultiShopParameters()));
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
            $parentOpenApi->getComponents(),
            $parentOpenApi->getSecurity(),
            $parentOpenApi->getTags(),
            $parentOpenApi->getExternalDocs(),
            $parentOpenApi->getJsonSchemaDialect(),
            $parentOpenApi->getWebhooks(),
        );
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
