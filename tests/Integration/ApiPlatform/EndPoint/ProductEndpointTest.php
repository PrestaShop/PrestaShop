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

declare(strict_types=1);

namespace Tests\Integration\ApiPlatform\EndPoint;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ProductGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Query\ProductQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use Tests\Resources\Resetter\LanguageResetter;
use Tests\Resources\Resetter\ProductResetter;
use Tests\Resources\ResourceResetter;

class ProductEndpointTest extends ApiTestCase
{
    protected const EN_LANG_ID = 1;
    protected static int $frenchLangId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        (new ResourceResetter())->backupTestModules();
        ProductResetter::resetProducts();
        LanguageResetter::resetLanguages();
        self::$frenchLangId = self::addLanguageByLocale('fr-FR');
        self::createApiAccess(['product_write', 'product_read']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        ProductResetter::resetProducts();
        LanguageResetter::resetLanguages();
        // Reset modules folder that are removed with the FR language
        (new ResourceResetter())->resetTestModules();
    }

    /**
     * @dataProvider getProtectedEndpoints
     *
     * @param string $method
     * @param string $uri
     */
    public function testProtectedEndpoints(string $method, string $uri, string $contentType = 'application/json'): void
    {
        $options['headers']['content-type'] = $contentType;
        // Check that endpoints are not accessible without a proper Bearer token
        $client = static::createClient([], $options);
        $response = $client->request($method, $uri);
        self::assertResponseStatusCodeSame(401);

        $content = $response->getContent(false);
        $this->assertNotEmpty($content);
        $decodedContent = json_decode($content, true);
        $this->assertArrayHasKey('title', $decodedContent);
        $this->assertArrayHasKey('detail', $decodedContent);
        $this->assertStringContainsString('An error occurred', $decodedContent['title']);
        $this->assertStringContainsString('Full authentication is required to access this resource.', $decodedContent['detail']);
    }

    public function getProtectedEndpoints(): iterable
    {
        yield 'get endpoint' => [
            'GET',
            '/api/product/1',
        ];

        yield 'create endpoint' => [
            'POST',
            '/api/product',
        ];

        yield 'update endpoint' => [
            'PATCH',
            '/api/product/1',
            'application/merge-patch+json',
        ];
    }

    public function testAddProduct(): int
    {
        $productsNumber = $this->getProductsNumber();
        $bearerToken = $this->getBearerToken(['product_write']);
        $client = static::createClient();
        $response = $client->request('POST', '/api/product', [
            'auth_bearer' => $bearerToken,
            'json' => [
                'type' => ProductType::TYPE_STANDARD,
                'names' => [
                    self::EN_LANG_ID => 'product name',
                    self::$frenchLangId => 'nom produit',
                ],
            ],
        ]);
        self::assertResponseStatusCodeSame(201);
        $newProductsNumber = $this->getProductsNumber();
        self::assertEquals($productsNumber + 1, $newProductsNumber);

        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertNotFalse($decodedResponse);
        $this->assertArrayHasKey('productId', $decodedResponse);
        $productId = $decodedResponse['productId'];
        $this->assertEquals(
            [
                'type' => ProductType::TYPE_STANDARD,
                'productId' => $productId,
                'names' => [
                    self::EN_LANG_ID => 'product name',
                    self::$frenchLangId => 'nom produit',
                ],
                'descriptions' => [
                    self::EN_LANG_ID => '',
                    self::$frenchLangId => '',
                ],
                'active' => false,
            ],
            $decodedResponse
        );

        return $productId;
    }

    /**
     * @depends testAddProduct
     *
     * @param int $productId
     *
     * @return int
     */
    public function testPartialUpdateProduct(int $productId): int
    {
        $productsNumber = $this->getProductsNumber();
        $bearerToken = $this->getBearerToken(['product_write']);
        $client = static::createClient();

        // Update product with partial data, even multilang fields can be updated language by language
        $response = $client->request('PATCH', '/api/product/' . $productId, [
            'auth_bearer' => $bearerToken,
            'headers' => [
                'content-type' => 'application/merge-patch+json',
            ],
            'json' => [
                'names' => [
                    self::$frenchLangId => 'nouveau nom',
                ],
                'descriptions' => [
                    self::EN_LANG_ID => 'new description',
                ],
                'active' => true,
            ],
        ]);
        self::assertResponseStatusCodeSame(200);
        // No new product
        $this->assertEquals($productsNumber, $this->getProductsNumber());

        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertNotFalse($decodedResponse);
        // Returned data has modified fields, the others haven't changed
        $this->assertEquals(
            [
                'type' => ProductType::TYPE_STANDARD,
                'productId' => $productId,
                'names' => [
                    self::EN_LANG_ID => 'product name',
                    self::$frenchLangId => 'nouveau nom',
                ],
                'descriptions' => [
                    self::EN_LANG_ID => 'new description',
                    self::$frenchLangId => '',
                ],
                'active' => true,
            ],
            $decodedResponse
        );

        // Update product with partial data, only name default language the other names are not impacted
        $response = $client->request('PATCH', '/api/product/' . $productId, [
            'auth_bearer' => $bearerToken,
            'headers' => [
                'content-type' => 'application/merge-patch+json',
            ],
            'json' => [
                'names' => [
                    self::EN_LANG_ID => 'new product name',
                ],
            ],
        ]);
        self::assertResponseStatusCodeSame(200);
        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertNotFalse($decodedResponse);
        // Returned data has modified fields, the others haven't changed
        $this->assertEquals(
            [
                'type' => ProductType::TYPE_STANDARD,
                'productId' => $productId,
                'names' => [
                    self::EN_LANG_ID => 'new product name',
                    self::$frenchLangId => 'nouveau nom',
                ],
                'descriptions' => [
                    self::EN_LANG_ID => 'new description',
                    self::$frenchLangId => '',
                ],
                'active' => true,
            ],
            $decodedResponse
        );

        return $productId;
    }

    /**
     * @depends testPartialUpdateProduct
     *
     * @param int $productId
     */
    public function testGetProduct(int $productId): int
    {
        $bearerToken = $this->getBearerToken(['product_read']);
        $client = static::createClient();
        $response = $client->request('GET', '/api/product/' . $productId, [
            'auth_bearer' => $bearerToken,
        ]);
        self::assertResponseStatusCodeSame(200);

        $decodedResponse = json_decode($response->getContent(), true);
        $this->assertNotFalse($decodedResponse);
        // Returned data has modified fields, the others haven't changed
        $this->assertEquals(
            [
                'type' => ProductType::TYPE_STANDARD,
                'productId' => $productId,
                'names' => [
                    self::EN_LANG_ID => 'new product name',
                    self::$frenchLangId => 'nouveau nom',
                ],
                'descriptions' => [
                    self::EN_LANG_ID => 'new description',
                    self::$frenchLangId => '',
                ],
                'active' => true,
            ],
            $decodedResponse
        );

        return $productId;
    }

    /**
     * @depends testGetProduct
     *
     * @param int $productId
     */
    public function testDeleteProduct(int $productId): void
    {
        $productsNumber = $this->getProductsNumber();
        $bearerToken = $this->getBearerToken(['product_read', 'product_write']);
        $client = static::createClient();
        // Update customer group with partial data
        $response = $client->request('DELETE', '/api/product/' . $productId, [
            'auth_bearer' => $bearerToken,
        ]);
        self::assertResponseStatusCodeSame(204);
        $this->assertEmpty($response->getContent());

        // One less products
        $this->assertEquals($productsNumber - 1, $this->getProductsNumber());

        $client = static::createClient();
        $client->request('GET', '/api/product/' . $productId, [
            'auth_bearer' => $bearerToken,
        ]);
        self::assertResponseStatusCodeSame(404);
    }

    protected function getProductsNumber(): int
    {
        /** @var ProductQueryBuilder $productQueryBuilder */
        $productQueryBuilder = $this->getContainer()->get('prestashop.core.grid.query_builder.product');
        $queryBuilder = $productQueryBuilder->getCountQueryBuilder(new ProductFilters(ShopConstraint::allShops(), ProductFilters::getDefaults(), ProductGridDefinitionFactory::GRID_ID));

        return (int) $queryBuilder->executeQuery()->fetchOne();
    }
}
