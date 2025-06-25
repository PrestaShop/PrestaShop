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
            '/api-client/{apiClientId}',
            [
                'get' => ['api_client_read'],
                'patch' => ['api_client_write'],
                'delete' => ['api_client_write'],
            ],
        ];

        yield 'API client creation' => [
            '/api-client',
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
            '/product/{productId}',
            [
                'get' => ['product_read'],
                'patch' => ['product_write'],
                'delete' => ['product_write'],
            ],
        ];

        yield 'Product creation' => [
            '/product',
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
                    'active' => new ArrayObject([
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
                    // Nullable DateTime
                    'availableDate' => new ArrayObject([
                        'format' => 'date-time',
                        'type' => [
                            'string',
                            'null',
                        ],
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
                    'active' => new ArrayObject([
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
                    // Multi-parameters setter
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
                    // Nullable DateTime
                    'availableDate' => new ArrayObject([
                        'format' => 'date-time',
                        'type' => 'string',
                    ]),
                    // Deprecated setter still present for now
                    'ean13' => new ArrayObject([
                        'type' => 'string',
                    ]),
                ],
            ]),
        ];
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
            '/product/{productId}',
            'get',
            ['Product'],
        ];

        yield 'product patch endpoint keeps Product tag' => [
            '/product/{productId}',
            'patch',
            ['Product'],
        ];

        yield 'product image get endpoint has Product tag instead of ProductImage' => [
            '/product/image/{imageId}',
            'get',
            ['Product'],
        ];

        yield 'api client get endpoint keeps ApiClient tag' => [
            '/api-client/{apiClientId}',
            'get',
            ['ApiClient'],
        ];

        yield 'api client list endpoint has ApiClient tag instead of ApiClientList' => [
            '/api-clients',
            'get',
            ['ApiClient'],
        ];
    }
}
