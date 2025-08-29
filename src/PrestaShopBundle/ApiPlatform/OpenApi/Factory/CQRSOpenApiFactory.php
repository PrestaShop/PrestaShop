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
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
use DateTimeInterface;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShopBundle\ApiPlatform\DomainObjectDetector;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

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
 */
class CQRSOpenApiFactory implements OpenApiFactoryInterface
{
    use ResourceMetadataTrait;

    protected PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        protected readonly OpenApiFactoryInterface $decorated,
        protected readonly ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        protected readonly DefinitionNameFactoryInterface $definitionNameFactory,
        protected readonly ClassMetadataFactoryInterface $classMetadataFactory,
        protected readonly DomainObjectDetector $domainObjectDetector,
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

                // Adapt localized value schema for API resource schema (mostly for read schema)
                if ($parentOpenApi->getComponents()->getSchemas()->offsetExists($resourceDefinitionName)) {
                    $this->adaptLocalizedValues($resourceMetadata->getClass(), $parentOpenApi->getComponents()->getSchemas()->offsetGet($resourceDefinitionName));
                    $this->adaptDecimalNumbers($resourceMetadata->getClass(), $parentOpenApi->getComponents()->getSchemas()->offsetGet($resourceDefinitionName));
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

                    $this->adaptMultiParametersSetters($operation, $definition);
                    $this->applyCommandMapping($operation, $definition);

                    // Adapt localized value schema for operation definition (for valid input example)
                    $this->adaptLocalizedValues($operation->getClass(), $definition);
                    $this->adaptDecimalNumbers($operation->getClass(), $definition);
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

                    $setterMethod = 'with' . ucfirst($httpMethod);
                    $updatedPathItem = $updatedPathItem->$setterMethod($updatedOperation);
                }
            }
            $updatedPaths->addPath($path, $updatedPathItem);
        }

        $updatedOpenApi = new OpenApi(
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

        return $updatedOpenApi;
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
     *
     * @param HttpOperation $operation
     *
     * @return string|null
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

    /**
     * Localized values are arrays indexed by locales (or objects with properties matching the locale in JSON), this
     * method adapts the expected format along with an example to indicate the user that the key to use is the locale.
     *
     * @param string $class
     * @param ArrayObject $definition
     *
     * @return void
     */
    protected function adaptLocalizedValues(string $class, ArrayObject $definition): void
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
                // Adapt the schema of localized values, they must be an object index by the Language's locale (not an array)
                if ($attribute->getName() === LocalizedValue::class || is_subclass_of($attribute->getName(), LocalizedValue::class)) {
                    $definition['properties'][$propertyName]['type'] = 'object';
                    $definition['properties'][$propertyName]['example'] = [
                        'en-US' => 'value',
                        'fr-FR' => 'valeur',
                    ];
                    unset($definition['properties'][$propertyName]['items']);
                }
            }
        }
    }

    /**
     * Internally we rely on DecimalNumber for float values because they are more accurate, but in the JSON format
     * they should be considered as float, so we update the schema for these types.
     *
     * @param string $class
     * @param ArrayObject $definition
     *
     * @return void
     */
    protected function adaptDecimalNumbers(string $class, ArrayObject $definition): void
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
            if ($property->hasType() && $property->getType() instanceof ReflectionNamedType) {
                $propertyType = $property->getType()->getName();
                if ($propertyType === DecimalNumber::class || is_subclass_of($propertyType, DecimalNumber::class)) {
                    $definition['properties'][$propertyName]['type'] = 'number';
                    $definition['properties'][$propertyName]['example'] = 42.99;
                    unset($definition['properties'][$propertyName]['$ref']);
                    unset($definition['properties'][$propertyName]['allOf']);
                }
            }
        }
    }

    /**
     * Some CQRS commands rely on multi-parameters setters, this is usually done to force specifying related parameters
     * all together because only one is not enough. For such setters we expect the method parameters to be provided in
     * a sub object, so this method transforms the schema to match this expected sib object.
     *
     * Example:
     *   UpdateProductCommand::setRedirectOption(string $redirectType, int $redirectTarget)
     *      => expected input ['redirectOption' => ['redirectType' => '301-category', 'redirectTarget' => 42]]
     *
     * @param Operation $operation
     * @param ArrayObject $definition
     *
     * @return void
     */
    protected function adaptMultiParametersSetters(Operation $operation, ArrayObject $definition): void
    {
        $operationClass = ($operation->getInput()['class'] ?? null) ?: $operation->getClass();
        // We only handle the special case of multi-parameters setters for classes that belong in our Domain
        if (!class_exists($operationClass) || !$this->classMetadataFactory->hasMetadataFor($operationClass) || !$this->domainObjectDetector->isDomainObject($operationClass)) {
            return;
        }

        $operationClassMetadata = $this->classMetadataFactory->getMetadataFor($operationClass);
        $operationReflectionClass = $operationClassMetadata->getReflectionClass();
        $methodsWithMultipleArguments = $this->findMethodsWithMultipleArguments($operationReflectionClass);
        if (empty($methodsWithMultipleArguments)) {
            return;
        }

        foreach ($methodsWithMultipleArguments as $methodPropertyName => $setterMethod) {
            $methodSchema = new ArrayObject([
                'type' => 'object',
                'properties' => [
                ],
            ]);
            foreach ($setterMethod->getParameters() as $methodParameter) {
                // If one of the parameters cannot be handled we skip the whole method
                if (!$methodParameter->getType() instanceof ReflectionNamedType) {
                    continue 2;
                }

                // If one of the parameters is not a built-in value we skip it (too complex to handle, but could be improved someday)
                if ($this->isDateTime($methodParameter->getType())) {
                    $methodParameterSchema = new ArrayObject([
                        'format' => 'date-time',
                        'type' => 'string',
                    ]);
                } elseif ($methodParameter->getType()->isBuiltin()) {
                    $methodParameterSchema = new ArrayObject([
                        'type' => $this->getSchemaType($methodParameter->getType()->getName()),
                    ]);
                } else {
                    continue 2;
                }
                $methodSchema['properties'][$methodParameter->getName()] = $methodParameterSchema;
            }

            // Method parameters are now in a sub-object, so they are removed from the top level
            // They must be removed before we add the methodSchema in case one of the parameter name matches the method name
            // (or it would unset it right after it was updated)
            foreach (array_keys($methodSchema['properties']) as $propertyName) {
                unset($definition['properties'][$propertyName]);
            }
            $definition['properties'][$methodPropertyName] = $methodSchema;
        }
    }

    protected function getSchemaType(string $builtInType): string
    {
        return match ($builtInType) {
            'int' => 'integer',
            'float' => 'number',
            'bool' => 'boolean',
            default => $builtInType,
        };
    }

    protected function isDateTime(ReflectionNamedType $methodParameter): bool
    {
        if (!class_exists($methodParameter->getName()) && !interface_exists($methodParameter->getName())) {
            return false;
        }

        $implements = class_implements($methodParameter->getName());
        if (empty($implements)) {
            return false;
        }

        return in_array(DateTimeInterface::class, $implements);
    }

    /**
     * @param ReflectionClass $reflectionClass
     *
     * @return array<string, ReflectionMethod>
     */
    protected function findMethodsWithMultipleArguments(ReflectionClass $reflectionClass): array
    {
        $methodsWithMultipleArguments = [];
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $reflectionMethod) {
            // We only look into public method that can be setters with multiple parameters
            if (
                $reflectionMethod->getNumberOfRequiredParameters() <= 1
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

    /**
     * Updates the schema property names based on the mapping specified, if for example the CQRS commands has a localizedNames
     * property that was renamed via the mapping into names then the schema won't use localizedNames but names for the final
     * schema output so that it matches the actual expected format.
     *
     * @param Operation $operation
     * @param ArrayObject $definition
     *
     * @return void
     */
    protected function applyCommandMapping(Operation $operation, ArrayObject $definition): void
    {
        if (empty($operation->getExtraProperties()['CQRSCommandMapping'])) {
            return;
        }

        foreach ($operation->getExtraProperties()['CQRSCommandMapping'] as $apiPath => $cqrsPath) {
            // Replace properties that are scanned from CQRS command to their expected API path
            if ($this->propertyAccessor->isReadable($definition['properties'], $cqrsPath)) {
                // Automatic value from context are simply removed from the schema, the others are "moved" to match the expected property path
                if (!str_starts_with($apiPath, '[_context]') && $this->propertyAccessor->isWritable($definition['properties'], $apiPath)) {
                    $this->propertyAccessor->setValue($definition['properties'], $apiPath, $this->propertyAccessor->getValue($definition['properties'], $cqrsPath));
                }

                // Use property path to set null, the null values will then be cleaned in a second loop (because unset cannot use property path as an input)
                if ($this->propertyAccessor->isWritable($definition['properties'], $cqrsPath)) {
                    $this->propertyAccessor->setValue($definition['properties'], $cqrsPath, null);
                }
            }
        }

        // Now clean the values that were set to null by the previous loop
        foreach ($definition['properties'] as $propertyName => $propertyValue) {
            if (null === $propertyValue) {
                unset($definition['properties'][$propertyName]);
            }
        }
    }
}
