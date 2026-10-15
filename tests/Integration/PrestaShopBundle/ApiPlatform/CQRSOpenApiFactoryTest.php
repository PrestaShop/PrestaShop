<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\ApiPlatform;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionWriterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CQRSOpenApiFactoryTest extends KernelTestCase
{
    public function testSecuritySchemes(): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();

        $this->assertEquals([new Server('/admin-api')], $openApi->getServers());

        $security = $openApi->getSecurity();
        $this->assertEquals([['oauth' => []]], $security);

        $securitySchemes = $openApi->getComponents()->getSecuritySchemes();
        $oauthSecurityScheme = $securitySchemes['oauth'];
        $this->assertInstanceOf(SecurityScheme::class, $oauthSecurityScheme);
        $this->assertEquals('oauth2', $oauthSecurityScheme->getType());
        $this->assertEquals('OAuth 2.0 client credentials Grant', $oauthSecurityScheme->getDescription());
        $this->assertNotNull($oauthSecurityScheme->getFlows());
        $this->assertNotNull($oauthSecurityScheme->getFlows()->getClientCredentials());
        $clientCredentialsFlow = $oauthSecurityScheme->getFlows()->getClientCredentials();
        $this->assertEmpty($clientCredentialsFlow->getAuthorizationUrl());
        $this->assertEquals('/admin-api/access_token', $clientCredentialsFlow->getTokenUrl());
        $this->assertGreaterThan(0, $clientCredentialsFlow->getScopes()->count());

        // We don't test all the scopes as they are gonna evolve with time, but we can test a minimum of them
        $expectedScopes = [
            'api_client_read' => 'Read ApiClient',
            'api_client_write' => 'Write ApiClient',
            'product_read' => 'Read Product',
            'product_write' => 'Write Product',
        ];
        foreach ($expectedScopes as $scope => $scopeDefinition) {
            $this->assertNotEmpty($clientCredentialsFlow->getScopes()[$scope]);
            $this->assertEquals($scopeDefinition, $clientCredentialsFlow->getScopes()[$scope]);
        }
    }

    public function testVersionedOperationsAreFiltered(): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();

        // The core version compatibility filtering applies in debug mode as well (unless the experimental
        // endpoints feature flag is enabled), so the versioned test operations incompatible with the core
        // version are absent from the OpenApi documentation while the compatible ones are present (this
        // also validates that the minVersion/maxVersion extra properties don't break the OpenApi generation,
        // the full filtering behaviour is asserted in Tests\Integration\ApiPlatform\VersionedEndpointsTest)
        $compatiblePaths = [
            '/test/versioned/min-valid/product/{productId}',
            '/test/versioned/max-valid/product/{productId}',
            '/test/versioned/range-valid/product/{productId}',
        ];
        foreach ($compatiblePaths as $compatiblePath) {
            $this->assertNotNull($openApi->getPaths()->getPath($compatiblePath), sprintf('Path %s not found in the OpenApi documentation', $compatiblePath));
        }

        $incompatiblePaths = [
            '/test/versioned/min-invalid/product/{productId}',
            '/test/versioned/max-invalid/product/{productId}',
            '/test/versioned/range-invalid/product/{productId}',
        ];
        foreach ($incompatiblePaths as $incompatiblePath) {
            $this->assertNull($openApi->getPaths()->getPath($incompatiblePath), sprintf('Path %s should have been filtered from the OpenApi documentation', $incompatiblePath));
        }
    }

    /**
     * @dataProvider provideEndpointScopes
     */
    public function testEndpointScopes(string $uriPath, array $expectedScopes): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();
        $openApiPath = $openApi->getPaths()->getPath($uriPath);
        $this->assertNotNull($openApiPath);
        foreach ($expectedScopes as $httpMethod => $methodScopes) {
            $getterMethod = 'get' . ucfirst(strtolower($httpMethod));
            $methodOperation = $openApiPath->$getterMethod($methodScopes);
            $this->assertInstanceOf(Operation::class, $methodOperation, 'Wrong operation for uri ' . $uriPath . ' method ' . $httpMethod);
            $this->assertEquals([['oauth' => $methodScopes]], $methodOperation->getSecurity());
        }
    }

    public function provideEndpointScopes(): iterable
    {
        yield 'API client entity' => [
            '/api-clients/{apiClientId}',
            [
                'get' => ['api_client_read'],
                'patch' => ['api_client_write'],
                'delete' => ['api_client_write'],
            ],
        ];

        yield 'API client creation' => [
            '/api-clients',
            [
                'post' => ['api_client_write'],
            ],
        ];

        yield 'API client list' => [
            '/api-clients',
            [
                'get' => ['api_client_read'],
            ],
        ];

        yield 'Product entity' => [
            '/products/{productId}',
            [
                'get' => ['product_read'],
                'patch' => ['product_write'],
                'delete' => ['product_write'],
            ],
        ];

        yield 'Product creation' => [
            '/products',
            [
                'post' => ['product_write'],
            ],
        ];

        yield 'Product list' => [
            '/products',
            [
                'get' => ['product_read'],
            ],
        ];
    }

    public function testMultishopParametersAreDocumentedWhenFeatureActive(): void
    {
        $configuration = $this->getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();
        $operation = $openApi->getPaths()->getPath('/products')->getGet();
        $parameterNames = array_map(static fn ($parameter) => $parameter->getName(), $operation->getParameters());
        $this->assertContains('shopId', $parameterNames);
        $this->assertContains('shopGroupId', $parameterNames);
        $this->assertContains('shopIds', $parameterNames);
        $this->assertContains('allShops', $parameterNames);
    }

    public function testMultishopParametersAreNotDocumentedWhenFeatureInactive(): void
    {
        $configuration = $this->getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 0);

        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();
        $operation = $openApi->getPaths()->getPath('/products')->getGet();
        $parameterNames = array_map(static fn ($parameter) => $parameter->getName(), $operation->getParameters());
        $this->assertNotContains('shopId', $parameterNames);
        $this->assertNotContains('shopGroupId', $parameterNames);
        $this->assertNotContains('shopIds', $parameterNames);
        $this->assertNotContains('allShops', $parameterNames);
    }

    /**
     * @dataProvider provideJsonSchemaFactoryCases
     */
    public function testJsonSchemaFactory(string $schemaDefinitionName, ArrayObject $expectedDefinition): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();
        $schemas = $openApi->getComponents()->getSchemas();
        $this->assertArrayHasKey($schemaDefinitionName, $schemas);

        /** @var ArrayObject $resourceDefinition */
        $resourceDefinition = $schemas[$schemaDefinitionName];
        $this->assertEquals($expectedDefinition, $resourceDefinition);
    }

    public function testClassPhpDocIsNotPublished(): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        $openApi = $openApiFactory->__invoke();

        // UpdatePositionResource has a class PHPDoc and no description
        $operation = $openApi->getPaths()->getPath('/test/positions')->getPatch();
        $this->assertStringNotContainsString('Developer note', (string) $operation->getDescription());
        $this->assertSame('', $openApi->getComponents()->getSchemas()['UpdatePositionResource']['description']);
    }

    public function testCollectionsDocumentation(): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        $paths = $openApiFactory->__invoke()->getPaths();

        // A paginated list documents the parameters read by QueryListProvider and its envelope
        $paginatedList = $paths->getPath('/customers')->getGet();
        $this->assertSame(
            ['limit', 'offset', 'orderBy', 'sortOrder', 'filters'],
            array_map(fn (Parameter $parameter) => $parameter->getName(), $paginatedList->getParameters())
        );
        $this->assertSame('deepObject', $paginatedList->getParameters()[4]->getStyle());
        $schema = $paginatedList->getResponses()['200']->getContent()['application/json']->getSchema();
        $this->assertSame('object', $schema['type']);
        $this->assertSame(['totalItems', 'orderBy', 'sortOrder', 'limit', 'offset', 'filters', 'items'], array_keys($schema['properties']));
        $this->assertSame(['type' => 'integer'], $schema['properties']['offset']);
        $this->assertEquals(['type' => 'array', 'items' => ['$ref' => '#/components/schemas/CustomerList']], $schema['properties']['items']);

        // A collection that is not paginated has no page parameter, API Platform doesn't paginate it
        $collection = $paths->getPath('/customers/{customerId}/orders')->getGet();
        $this->assertSame(['customerId'], array_map(fn (Parameter $parameter) => $parameter->getName(), $collection->getParameters()));
    }

    public function testUnusedSchemasAreRemoved(): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        $schemas = $openApiFactory->__invoke()->getComponents()->getSchemas();

        // Value objects of the CQRS commands are never referenced by the documented operations
        $this->assertArrayNotHasKey('ShopConstraint', $schemas);
        // The input of a multipart only operation is documented inline
        $this->assertArrayNotHasKey('Title.AddTitleCommand', $schemas);
        // The input of an operation accepting JSON and multipart is still referenced by the JSON request body
        $this->assertArrayHasKey('Carrier.AddCarrierCommand', $schemas);
        // Sub objects of the resources are referenced by their resource
        $this->assertArrayHasKey('CustomerOrderRow', $schemas);
    }

    public function testMultiParameterSetterWithDecimalNumber(): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        $schemas = $openApiFactory->__invoke()->getComponents()->getSchemas();

        // UpdateDiscountCommand::setReductionAmount(DecimalNumber $amount, int $currencyId, bool $taxIncluded)
        $reductionAmount = (array) $schemas['Discount.UpdateDiscountCommand']['properties']['reductionAmount'];
        $this->assertSame('object', $reductionAmount['type']);
        $this->assertArrayNotHasKey('anyOf', $reductionAmount);
        $this->assertArrayNotHasKey('readOnly', $reductionAmount);
        $this->assertSame('number', $reductionAmount['properties']['amount']['type']);
        $this->assertSame('integer', $reductionAmount['properties']['currencyId']['type']);
        $this->assertSame('boolean', $reductionAmount['properties']['taxIncluded']['type']);

        // UpdateDiscountCommand::setMinimumAmount(?DecimalNumber $amount, ...)
        $minimumAmount = (array) $schemas['Discount.UpdateDiscountCommand']['properties']['minimumAmount'];
        $this->assertSame('object', $minimumAmount['type']);
        $this->assertArrayNotHasKey('anyOf', $minimumAmount);
        $this->assertSame('number', $minimumAmount['properties']['amount']['type']);

        // The value objects are not documented anymore
        $this->assertArrayNotHasKey('Money', $schemas);
        $this->assertArrayNotHasKey('DecimalNumber', $schemas);
    }

    public static function provideJsonSchemaFactoryCases(): iterable
    {
        yield 'Product output is based on the ApiPlatform resource' => [
            'Product',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'productId' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'type' => new ArrayObject([
                        'type' => 'string',
                        'enum' => ['standard', 'pack', 'virtual', 'combinations'],
                    ]),
                    'enabled' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    // Localized fields are documented vie the LocalizedValue attribute
                    'names' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'descriptions' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'shortDescriptions' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'tags' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'priceTaxExcluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'priceTaxIncluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'ecotaxTaxExcluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'ecotaxTaxIncluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'taxRulesGroupId' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'onSale' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'wholesalePrice' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'unitPriceTaxExcluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'unitPriceTaxIncluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'unity' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'unitPriceRatio' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'visibility' => new ArrayObject([
                        'type' => 'string',
                        'enum' => ['catalog', 'search', 'both', 'none'],
                    ]),
                    'availableForOrder' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'onlineOnly' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'showPrice' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'condition' => new ArrayObject([
                        'type' => 'string',
                        'enum' => ['new', 'used', 'refurbished', 'open_box', 'damaged', 'new_with_defects'],
                    ]),
                    'showCondition' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'manufacturerId' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'isbn' => new ArrayObject([
                        'type' => 'string',
                        'maxLength' => 32,
                    ]),
                    'upc' => new ArrayObject([
                        'type' => 'string',
                        'maxLength' => 12,
                    ]),
                    'gtin' => new ArrayObject([
                        'type' => 'string',
                        'maxLength' => 14,
                    ]),
                    'mpn' => new ArrayObject([
                        'type' => 'string',
                        'maxLength' => 40,
                    ]),
                    'reference' => new ArrayObject([
                        'type' => 'string',
                        'maxLength' => 64,
                    ]),
                    'width' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'height' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'depth' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'weight' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'additionalShippingCost' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    // This field is nullable
                    'redirectTarget' => new ArrayObject([
                        'type' => [
                            'integer',
                            'null',
                        ],
                    ]),
                    // Carrier reference IDs are documented via an ApiProperty attribute
                    'carrierReferenceIds' => new ArrayObject([
                        'type' => 'array',
                        'items' => ['type' => 'integer'],
                        'example' => [1, 3],
                    ]),
                    'deliveryTimeNoteType' => new ArrayObject([
                        'type' => 'integer',
                        'enum' => [0, 1, 2],
                    ]),
                    'deliveryTimeInStockNotes' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'deliveryTimeOutOfStockNotes' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'metaTitles' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'metaDescriptions' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'linkRewrites' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'redirectType' => new ArrayObject([
                        'type' => 'string',
                        'enum' => ['default', '404', '410', '301-category', '302-category', '301-product', '302-product', '200-displayed', '404-displayed', '410-displayed'],
                    ]),
                    'packStockType' => new ArrayObject([
                        'type' => 'integer',
                        'enum' => [0, 1, 2, 3],
                    ]),
                    'outOfStockType' => new ArrayObject([
                        'type' => 'integer',
                        'enum' => [1, 0, 2],
                    ]),
                    'quantity' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'minimalQuantity' => new ArrayObject([
                        'type' => 'integer',
                        'exclusiveMinimum' => 0,
                    ]),
                    'lowStockThreshold' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'lowStockAlertEnabled' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'availableNowLabels' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'location' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'availableLaterLabels' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    // Nullable DateImmutable (format is 'date' not 'date-time')
                    'availableDate' => new ArrayObject([
                        'format' => 'date',
                        'type' => [
                            'string',
                            'null',
                        ],
                        'example' => '2025-11-05',
                    ]),
                    'coverThumbnailUrl' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    // Shop IDs are documented via an ApiProperty attribute
                    'shopIds' => new ArrayObject([
                        'type' => 'array',
                        'items' => ['type' => 'integer'],
                        'example' => [1, 3],
                    ]),
                    // Categories use @index replacement, but their definition uses an ApiProperty
                    'categories' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'categoryId' => [
                                    'type' => 'integer',
                                ],
                                'name' => [
                                    'type' => 'string',
                                ],
                                'displayName' => [
                                    'type' => 'string',
                                ],
                            ],
                        ],
                        'example' => [
                            [
                                'categoryId' => 2,
                                'name' => 'Home',
                                'displayName' => 'Home',
                            ],
                        ],
                    ]),
                    'defaultCategoryId' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'virtualProductFile' => new ArrayObject([
                        'type' => 'object',
                        'nullable' => true,
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'fileName' => ['type' => 'string'],
                            'displayName' => ['type' => 'string'],
                            'accessDays' => ['type' => 'integer'],
                            'downloadTimesLimit' => ['type' => 'integer'],
                            'expirationDate' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                            'productId' => ['type' => 'integer'],
                        ],
                    ]),
                ],
            ]),
        ];

        // First productType and shopId must use scalar type, not ShopId and ProductType Value Objects
        // Then shopID is removed because it's automatically feed from the context, and other fields are renamed to
        // match the API format from the Api Resource class naming
        yield 'Product input for creation based on AddProductCommand' => [
            'Product.AddProductCommand',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'type' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'names' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                ],
                // The productType parameter of the command has no default value, the localizedNames one has
                'required' => [
                    'type',
                ],
            ]),
        ];

        yield 'Product patch input output is based on UpdateProductCommand' => [
            'Product.UpdateProductCommand',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    // productId is a URI variable, read from the URL and not from the payload
                    'enabled' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    // Localized fields are documented vie the LocalizedValue attribute
                    'names' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'descriptions' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'shortDescriptions' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'priceTaxExcluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'ecotaxTaxExcluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'taxRulesGroupId' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'onSale' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'wholesalePrice' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'unitPriceTaxExcluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'unity' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'visibility' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'availableForOrder' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'onlineOnly' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'showPrice' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'condition' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'showCondition' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'manufacturerId' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'isbn' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'upc' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'gtin' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'mpn' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'reference' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'width' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'height' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'depth' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'weight' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'additionalShippingCost' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'deliveryTimeNoteType' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'deliveryTimeInStockNotes' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'deliveryTimeOutOfStockNotes' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'metaTitles' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'metaDescriptions' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'linkRewrites' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'redirectOption' => new ArrayObject([
                        'type' => 'object',
                        'properties' => [
                            'redirectType' => new ArrayObject([
                                'type' => 'string',
                            ]),
                            'redirectTarget' => new ArrayObject([
                                'type' => 'integer',
                            ]),
                        ],
                    ]),
                    'packStockType' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'minimalQuantity' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'lowStockThreshold' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'availableNowLabels' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'availableLaterLabels' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    // Nullable DateImmutable
                    'availableDate' => new ArrayObject([
                        'format' => 'date',
                        'type' => 'string',
                        'example' => '2025-11-05',
                    ]),
                ],
            ]),
        ];

        // AddCarrierCommand is the interesting case for the CQRSCommandMapping: its constructor parameters are
        // named differently from the accessors ApiPlatform detects the properties from ($max_width against
        // getMaxWidth, $isFree against isFree, $hasAdditionalHandlingFee against hasAdditionalHandlingFee), so
        // those properties used to be documented as read only, and then removed from the payload, although the
        // command requires them.
        yield 'Carrier input for creation, mapped properties are all documented as writable' => [
            'Carrier.AddCarrierCommand',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'name' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'grade' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'trackingUrl' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'position' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    // Constructor parameters in snake_case, detected via their getters
                    'maxWidth' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'maxHeight' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'maxDepth' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'maxWeight' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    // The identifier collections are documented as integers by the openapiContext of the resource,
                    // the command only exposes them as arrays of strings
                    'associatedGroupIds' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            'type' => 'integer',
                        ],
                    ]),
                    // Constructor parameters detected via their has/is accessors, defaulted by the operation so their
                    // default value is documented and they are absent from the required properties below
                    'additionalHandlingFee' => new ArrayObject([
                        'type' => 'boolean',
                        'default' => false,
                    ]),
                    'free' => new ArrayObject([
                        'type' => 'boolean',
                        'default' => false,
                    ]),
                    'shippingMethod' => new ArrayObject([
                        'type' => 'integer',
                        'default' => ShippingMethod::BY_PRICE,
                    ]),
                    'rangeBehavior' => new ArrayObject([
                        'type' => 'integer',
                        'default' => OutOfRangeBehavior::USE_HIGHEST_RANGE,
                    ]),
                    'associatedShopIds' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            'type' => 'integer',
                        ],
                    ]),
                    'zones' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            'type' => 'integer',
                        ],
                    ]),
                    // Renamed by the mapping, the localizedDelay and active parameters are not documented
                    'delays' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                    'enabled' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                ],
                // Every parameter of the command without a default value, so the ones the request must provide.
                // The carrierId is absent since it is a URI variable, the shop constraint since the API fills it from
                // the request context, and the five defaulted fields since the operation fills them: the four fields
                // above with literal values, and the associatedShopIds with a context default resolved per request
                // (the shops of the request context), which is why it is not required despite having no literal value.
                'required' => [
                    'name',
                    'delays',
                    'grade',
                    'trackingUrl',
                    'enabled',
                    'associatedGroupIds',
                    'zones',
                ],
            ]),
        ];

        // The openapiContext declared on the resource must win over the format detected from the CQRS command, here
        // SetCarrierRangesCommand only exposes an array of ranges so the collection of objects documented by the
        // resource used to be reduced to a collection of strings in the request body.
        yield 'Carrier ranges input, the openapiContext of the resource is applied on the command schema' => [
            'CarrierRanges.SetCarrierRangesCommand',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    // carrierId is a URI variable, read from the URL and not from the payload
                    'ranges' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'zoneId' => [
                                    'type' => 'integer',
                                ],
                                'rangeFrom' => [
                                    'type' => 'number',
                                ],
                                'rangeTo' => [
                                    'type' => 'number',
                                ],
                                'rangePrice' => [
                                    'type' => 'number',
                                ],
                            ],
                        ],
                    ]),
                ],
                'required' => [
                    'ranges',
                ],
            ]),
        ];

        yield 'Product list output, we need to check ApiResourceMapping is correctly applied' => [
            'ProductList',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'productId' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'type' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'enabled' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'name' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'quantity' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'priceTaxExcluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'priceTaxIncluded' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'category' => new ArrayObject([
                        'type' => 'string',
                    ]),
                ],
            ]),
        ];

        yield 'UpdatePositionResource, the documentation is adapted thanks to the PositionCollection attribute' => [
            'UpdatePositionResource',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'positions' => new ArrayObject([
                        'type' => 'array',
                        'items' => new ArrayObject([
                            'type' => 'object',
                            'properties' => new ArrayObject([
                                'testId' => ['type' => 'integer'],
                                'newPosition' => ['type' => 'integer'],
                            ]),
                        ]),
                        'example' => new ArrayObject([
                            [
                                'testId' => 5,
                                'newPosition' => 3,
                            ],
                            [
                                'testId' => 8,
                                'newPosition' => 1,
                            ],
                        ]),
                    ]),
                ],
            ]),
        ];

        // Sub objects typed with DTOs are documented as references to their own schema, and the arrays
        // documented with a @var Foo[] PHPDoc as lists of these references
        yield 'Sub object of a resource, adapted like the resource (DecimalNumber documented as number)' => [
            'CustomerOrderRow',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'orderId' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'valid' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'totalPaidTaxIncl' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'conversionRate' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                ],
            ]),
        ];

        yield 'CustomerDetails schema' => [
            'CustomerDetails',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'customerId' => new ArrayObject([
                        'type' => 'integer',
                        'example' => 1,
                    ]),
                    'personalInformation' => new ArrayObject([
                        '$ref' => '#/components/schemas/CustomerPersonalInformation',
                    ]),
                    'ordersInformation' => new ArrayObject([
                        '$ref' => '#/components/schemas/CustomerOrdersInformation',
                    ]),
                    'messagesInformation' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            '$ref' => '#/components/schemas/CustomerMessageInformation',
                        ],
                    ]),
                    'sentEmailsInformation' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            '$ref' => '#/components/schemas/CustomerSentEmail',
                        ],
                    ]),
                    'lastConnectionsInformation' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            '$ref' => '#/components/schemas/CustomerLastConnection',
                        ],
                    ]),
                    'groupsInformation' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            '$ref' => '#/components/schemas/CustomerGroupInformation',
                        ],
                    ]),
                    'generalInformation' => new ArrayObject([
                        '$ref' => '#/components/schemas/CustomerGeneralInformation',
                    ]),
                    'privateNote' => new ArrayObject([
                        'writeOnly' => true,
                        'type' => [
                            'string',
                            'null',
                        ],
                    ]),
                ],
                'required' => [
                    'privateNote',
                ],
            ]),
        ];

        // The URI variable customerId is not part of the request body, and the command parameters without type
        // declaration (firstName, birthday, ...) use the type of the resource property
        yield 'Customer.EditCustomerCommand schema' => [
            'Customer.EditCustomerCommand',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'firstName' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'lastName' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'email' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'password' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'defaultGroupId' => new ArrayObject([
                        'type' => 'integer',
                        'example' => 3,
                    ]),
                    'groupIds' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            'type' => 'integer',
                        ],
                        'example' => [
                            1,
                            3,
                        ],
                    ]),
                    'genderId' => new ArrayObject([
                        'type' => 'integer',
                        'example' => 1,
                    ]),
                    'newsletterSubscribed' => new ArrayObject([
                        'type' => 'boolean',
                        'example' => false,
                    ]),
                    'enabled' => new ArrayObject([
                        'type' => 'boolean',
                        'example' => true,
                    ]),
                    'partnerOffersSubscribed' => new ArrayObject([
                        'type' => 'boolean',
                        'example' => false,
                    ]),
                    'birthday' => new ArrayObject([
                        'type' => [
                            'string',
                            'null',
                        ],
                    ]),
                    'companyName' => new ArrayObject([
                        'type' => [
                            'string',
                            'null',
                        ],
                    ]),
                    'siretCode' => new ArrayObject([
                        'type' => [
                            'string',
                            'null',
                        ],
                    ]),
                    'apeCode' => new ArrayObject([
                        'type' => [
                            'string',
                            'null',
                        ],
                    ]),
                    'website' => new ArrayObject([
                        'type' => [
                            'string',
                            'null',
                        ],
                    ]),
                    'allowedOutstandingAmount' => new ArrayObject([
                        'type' => 'number',
                        'example' => 1000.5,
                    ]),
                    'maxPaymentDays' => new ArrayObject([
                        'type' => 'integer',
                        'example' => 30,
                    ]),
                    'riskId' => new ArrayObject([
                        'type' => 'integer',
                        'example' => 1,
                    ]),
                ],
            ]),
        ];

        // The constructor parameters whose getter has no get/is/has prefix (displayPriceTaxExcluded, showPrice) are
        // documented and required, shopIds is not required since the context shops are its default value
        yield 'CustomerGroup.AddCustomerGroupCommand schema' => [
            'CustomerGroup.AddCustomerGroupCommand',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'reductionPercent' => new ArrayObject([
                        'type' => 'number',
                        'example' => 42.99,
                    ]),
                    'shopIds' => new ArrayObject([
                        'type' => 'array',
                        'items' => [
                            'type' => 'integer',
                        ],
                        'description' => 'Shops associated with the group. On creation, optional from PrestaShop 9.2.0 where it defaults '
                            . 'to the shops of the context, required on older versions.',
                    ]),
                    'displayPriceTaxExcluded' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'showPrice' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'names' => new ArrayObject([
                        'type' => 'object',
                        'example' => [
                            'en-US' => 'value',
                            'fr-FR' => 'valeur',
                        ],
                    ]),
                ],
                'required' => [
                    'names',
                    'reductionPercent',
                    'displayPriceTaxExcluded',
                    'showPrice',
                ],
            ]),
        ];

        // A nullable DecimalNumber is documented as a nullable number, and the plain comment of the class is not
        // used as description
        yield 'CustomerList schema' => [
            'CustomerList',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'customerId' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'socialTitle' => new ArrayObject([
                        'type' => [
                            'string',
                            'null',
                        ],
                    ]),
                    'firstName' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'lastName' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'email' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'companyName' => new ArrayObject([
                        'type' => [
                            'string',
                            'null',
                        ],
                    ]),
                    'totalSpent' => new ArrayObject([
                        'type' => [
                            'number',
                            'null',
                        ],
                        'example' => 42.99,
                    ]),
                    'enabled' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'newsletterSubscribed' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'partnerOffersSubscribed' => new ArrayObject([
                        'type' => 'boolean',
                    ]),
                    'defaultGroupName' => new ArrayObject([
                        'type' => [
                            'string',
                            'null',
                        ],
                    ]),
                    'shopName' => new ArrayObject([
                        'type' => 'string',
                    ]),
                    'dateAdd' => new ArrayObject([
                        'type' => 'string',
                        'format' => 'date-time',
                    ]),
                    'lastVisitDate' => new ArrayObject([
                        'type' => [
                            'string',
                            'null',
                        ],
                        'format' => 'date-time',
                    ]),
                ],
            ]),
        ];

        // @todo Add a schema with a dateTime like discount when it is released or virtual product's expiration date.
    }

    /**
     * @dataProvider getExpectedTags
     */
    public function testPathTags(string $path, string $expectedMethod, array $expectedTags): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();
        $pathItem = $openApi->getPaths()->getPath($path);
        $this->assertNotNull($pathItem);

        $methodGetter = 'get' . ucfirst(strtolower($expectedMethod));
        /** @var Operation $operation */
        $operation = $pathItem->$methodGetter();
        $this->assertNotNull($operation);
        $this->assertEquals($expectedTags, $operation->getTags());
    }

    public function getExpectedTags(): iterable
    {
        yield 'product get endpoint keeps Product tag' => [
            '/products/{productId}',
            'get',
            ['Product'],
        ];

        yield 'product patch endpoint keeps Product tag' => [
            '/products/{productId}',
            'patch',
            ['Product'],
        ];

        yield 'product image get endpoint has Product tag instead of ProductImage' => [
            '/products/images/{imageId}',
            'get',
            ['Product'],
        ];

        yield 'api client get endpoint keeps ApiClient tag' => [
            '/api-clients/{apiClientId}',
            'get',
            ['ApiClient'],
        ];

        yield 'api client list endpoint has ApiClient tag instead of ApiClientList' => [
            '/api-clients',
            'get',
            ['ApiClient'],
        ];
    }

    /**
     * @dataProvider provideIntegerPathParameters
     */
    public function testIntegerPathParameters(string $path, string $method, string $parameterName, string $expectedType): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();
        $pathItem = $openApi->getPaths()->getPath($path);
        $this->assertNotNull($pathItem);

        $methodGetter = 'get' . ucfirst(strtolower($method));
        /** @var Operation $operation */
        $operation = $pathItem->$methodGetter();
        $this->assertNotNull($operation);

        $pathParameters = array_filter($operation->getParameters(), fn ($parameter) => 'path' === $parameter->getIn() && $parameterName === $parameter->getName());
        $this->assertCount(1, $pathParameters);
        $this->assertEquals(['type' => $expectedType], reset($pathParameters)->getSchema());
    }

    public static function provideIntegerPathParameters(): iterable
    {
        yield 'identifier with a digits requirement' => ['/customers/{customerId}', 'get', 'customerId', 'integer'];
        yield 'identifier without requirement, based on the int property of the resource' => ['/customers/groups/{customerGroupId}', 'get', 'customerGroupId', 'integer'];
        yield 'string identifier' => ['/modules/{technicalName}', 'get', 'technicalName', 'string'];
    }

    public function testMultipartFileUploadRequestBody(): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();

        // The multipart operation documents the uploaded file properties declared in the API resource class
        $multipartOperation = $openApi->getPaths()->getPath('/test/file-upload')->getPost();
        $content = $multipartOperation->getRequestBody()->getContent();
        $this->assertTrue($content->offsetExists('multipart/form-data'));

        $schema = $content->offsetGet('multipart/form-data')->getSchema();
        // The schema is inlined so that file properties are only documented on the multipart operation
        $this->assertArrayNotHasKey('$ref', (array) $schema);
        $this->assertArrayHasKey('productId', $schema['properties']);
        $this->assertEquals(['type' => 'string', 'format' => 'binary'], (array) $schema['properties']['file']);
        $this->assertEquals(['type' => 'string', 'format' => 'binary'], (array) $schema['properties']['optionalFile']);
        // Non-nullable file properties are required, nullable ones are optional
        $this->assertContains('file', $schema['required']);
        $this->assertNotContains('optionalFile', $schema['required']);

        // The JSON operation based on the same CQRS command keeps its schema reference, and the
        // shared component schema is not polluted by the file properties
        $jsonOperation = $openApi->getPaths()->getPath('/test/file-upload-json')->getPost();
        $jsonContent = $jsonOperation->getRequestBody()->getContent();
        $this->assertFalse($jsonContent->offsetExists('multipart/form-data'));

        $jsonSchema = $jsonContent->offsetGet('application/json')->getSchema();
        $this->assertEquals('#/components/schemas/FileUploadResource.AddProductImageCommand', $jsonSchema['$ref']);

        $componentSchema = $openApi->getComponents()->getSchemas()['FileUploadResource.AddProductImageCommand'];
        $this->assertArrayNotHasKey('file', (array) $componentSchema['properties']);
        $this->assertArrayNotHasKey('optionalFile', (array) $componentSchema['properties']);
    }

    public function testApiPropertyOpenApiContextApplied(): void
    {
        /** @var OpenApiFactoryInterface $openApiFactory */
        $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
        /** @var OpenApi $openApi */
        $openApi = $openApiFactory->__invoke();
        $schemas = $openApi->getComponents()->getSchemas();

        /** @var ArrayObject $contactSchema */
        $contactSchema = $schemas['Contact'];
        $shopIdsProperty = $contactSchema['properties']['shopIds'];

        $this->assertEquals('array', $shopIdsProperty['type']);
        $this->assertEquals(['type' => 'integer'], $shopIdsProperty['items']);
        $this->assertEquals([1, 3], $shopIdsProperty['example']);
    }

    /**
     * Module-declared extra properties are documented in the generated OpenAPI schema: grouped under their module
     * technical name inside a synthetic "extraProperties" object on every resource whose operations they target,
     * a definition flagged required (ExtraPropertyDefinition::isRequired()) is listed in that module object's
     * OpenAPI "required" array, and a declared defaultValue is emitted as the field's "default" with its scalar
     * type (decoded for JSON, whose runtime read shape is the decoded structure). The definitions are persisted
     * directly (no module install needed) and removed afterwards so the shared test database stays clean.
     */
    public function testExtraPropertiesAreDocumentedInGeneratedSchema(): void
    {
        $repository = $this->getContainer()->get(ExtraPropertyDefinitionRepositoryInterface::class);
        // The shared cache decorator also implements the writer interface (and invalidates the cache on write),
        // so we can persist definitions directly and have them surface in the very next schema generation.
        self::assertInstanceOf(ExtraPropertyDefinitionWriterInterface::class, $repository);

        $productApis = ['/products', '/products/{productId}'];
        $requiredDefinition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'oa_required_url',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'openapitest',
            required: true,
            associatedApis: $productApis,
        );
        $optionalDefinition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'oa_optional_flag',
            type: ExtraPropertyType::BOOL,
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'openapitest',
            required: false,
            defaultValue: false,
            associatedApis: $productApis,
        );
        $jsonDefinition = new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'oa_json_meta',
            type: ExtraPropertyType::JSON,
            scope: ExtraPropertyScope::COMMON,
            moduleName: 'openapitest',
            required: false,
            defaultValue: '{"tier":"bronze"}',
            associatedApis: $productApis,
        );

        $requiredId = $repository->save($requiredDefinition);
        $optionalId = $repository->save($optionalDefinition);
        $jsonId = $repository->save($jsonDefinition);
        $moduleKey = $requiredDefinition->getNormalizedModuleKey();

        try {
            /** @var OpenApiFactoryInterface $openApiFactory */
            $openApiFactory = $this->getContainer()->get(OpenApiFactoryInterface::class);
            /** @var OpenApi $openApi */
            $openApi = $openApiFactory->__invoke();

            $schemas = $openApi->getComponents()->getSchemas();
            $this->assertArrayHasKey('Product', $schemas);

            /** @var ArrayObject $productSchema */
            $productSchema = $schemas['Product'];
            $this->assertArrayHasKey('extraProperties', $productSchema['properties']);

            // The synthetic object is an object grouped by module technical name.
            $extraProperties = $productSchema['properties']['extraProperties'];
            $this->assertSame('object', $extraProperties['type']);
            $this->assertArrayHasKey($moduleKey, $extraProperties['properties']);

            $module = $extraProperties['properties'][$moduleKey];
            // All fields are documented under the module object …
            $this->assertArrayHasKey('oa_required_url', $module['properties']);
            $this->assertArrayHasKey('oa_optional_flag', $module['properties']);
            $this->assertArrayHasKey('oa_json_meta', $module['properties']);
            // … but only the required one is reported in the module object's OpenAPI "required" list.
            $this->assertSame(['oa_required_url'], $module['required']);

            // A declared default is emitted with its scalar type; JSON defaults are stored as
            // strings but documented decoded, matching the runtime read shape of the API.
            $this->assertArrayNotHasKey('default', $module['properties']['oa_required_url']);
            $this->assertFalse($module['properties']['oa_optional_flag']['default']);
            $this->assertSame(['tier' => 'bronze'], $module['properties']['oa_json_meta']['default']);
        } finally {
            if (!empty($requiredId)) {
                $repository->delete((int) $requiredId);
            }
            if (!empty($optionalId)) {
                $repository->delete((int) $optionalId);
            }
            if (!empty($jsonId)) {
                $repository->delete((int) $jsonId);
            }
        }
    }
}
