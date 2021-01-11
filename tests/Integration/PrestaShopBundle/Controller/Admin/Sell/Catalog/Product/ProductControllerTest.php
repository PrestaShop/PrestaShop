<?php

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * To run this test run this command from project root:
 * php -d date.timezone=UTC ./vendor/bin/phpunit -c tests/Integration/phpunit.xml tests/Integration/PrestaShopBundle/Controller/Admin/Sell/Catalog/Product/ProductControllerTest.php
 */
class ProductControllerTest extends WebTestCase
{
    /**
     * @var bool
     */
    private $handlePartialUpdate;

    /**
     * @var bool
     */
    private $handleStrictPartialUpdate;

    protected function setUp()
    {
        $this->handlePartialUpdate = true;
        $this->handleStrictPartialUpdate = false;
    }

    public function testCreateProduct()
    {
        $client = static::createClient();
        $router = $client->getKernel()->getContainer()->get('router');
        $createUrl = $router->generate('admin_products_v2_create');
        $crawler = $client->request('GET', $createUrl);

        // Get button by id #product_save
        $submitButton = $crawler->selectButton('product_save');
        $productForm = $submitButton->form();

        // Only product name in default language should be required on creation
        $productForm['product[basic][name][1]'] = 'Test Product';

        $client->submit($productForm);
        $response = $client->getResponse();

        // If creation happens correctly then we are redirect to the edition page
        $this->assertTrue($response->isRedirection());
        $redirectUrl = $response->headers->get('Location');

        $parsedUrl = parse_url($redirectUrl);
        $routerMatching = $router->match($parsedUrl['path']);
        $this->assertArrayHasKey('_route', $routerMatching);
        $this->assertEquals('admin_products_v2_edit', $routerMatching['_route']);
        $this->assertArrayHasKey('productId', $routerMatching);
        $this->assertGreaterThan(0, $routerMatching['productId']);

        return (int) $routerMatching['productId'];
    }

    /**
     * @param array $formModifications
     * @param array $expectedUpdateData
     * @param int $productId
     *
     * @dataProvider getProductEditionModifications
     * @depends testCreateProduct
     */
    public function testEditProduct(array $formModifications, array $expectedUpdateData, int $productId)
    {
        $client = static::createClient();
        $router = $client->getKernel()->getContainer()->get('router');
        $createUrl = $router->generate('admin_products_v2_edit', ['productId' => $productId]);
        $crawler = $client->request('GET', $createUrl);

        // Get button by id #product_save
        $submitButton = $crawler->selectButton('product_save');
        $productForm = $submitButton->form();
        foreach ($formModifications as $formField => $formValue) {
            $productForm[$formField] = 'Test Update Product';
        }

        $client->submit($productForm);
        $response = $client->getResponse();

        // If update happens correctly then we are redirect to the same edition page
        $this->assertTrue($response->isRedirection());
        $redirectUrl = $response->headers->get('Location');
        $parsedUrl = parse_url($redirectUrl);
        $routerMatching = $router->match($parsedUrl['path']);
        $this->assertArrayHasKey('_route', $routerMatching);
        $this->assertEquals('admin_products_v2_edit', $routerMatching['_route']);
        $this->assertArrayHasKey('productId', $routerMatching);
        $this->assertEquals($productId, $routerMatching['productId']);

        // Now we check that the correct data were use by the form handler
        $dataChecker = $client->getContainer()->get('test.integration.core.form.identifiable_object.data_handler.product_form_data_handler_checker');
        $updateData = $dataChecker->getLastUpdateData();
        if ($this->handlePartialUpdate && !$this->handleStrictPartialUpdate) {
            // This method is deprecated in PHPUnit for PHP 8.0 but we can't use more recent libraries that replace this
            // because they require more recent version of PHP than ours, so for now we keep using this one
            $this->assertArraySubset($expectedUpdateData, $updateData);
        } else {
            $this->assertEquals($expectedUpdateData, $updateData);
        }
    }

    public function getProductEditionModifications()
    {
        yield [
            // Form fields that need to be updated
            [
                'product[basic][name][1]' => 'Test Update Product',
            ],
            // Expected data in the handler
            [
                'basic' => [
                    'name' => [
                        1 => 'Test Update Product',
                    ],
                ],
            ],
        ];
    }
}
