<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\ApiPlatform;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ApiPlatform\OpenApi\Model\Server;
use ApiPlatform\OpenApi\OpenApi;
use ArrayObject;
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
                    ]),
                    'packStockType' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'outOfStockType' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'quantity' => new ArrayObject([
                        'type' => 'integer',
                    ]),
                    'minimalQuantity' => new ArrayObject([
                        'type' => 'integer',
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
            ]),
        ];

        yield 'Product patch input output is based on UpdateProductCommand' => [
            'Product.UpdateProductCommand',
            new ArrayObject([
                'type' => 'object',
                'description' => '',
                'deprecated' => false,
                'properties' => [
                    'productId' => new ArrayObject([
                        'type' => 'integer',
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
}
