<?php

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
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
     * @depends testCreateProduct
     */
    public function testEditProduct(int $productId)
    {
        $client = static::createClient();
        $router = $client->getKernel()->getContainer()->get('router');
        var_dump($productId);
        $createUrl = $router->generate('admin_products_v2_edit', ['productId' => $productId]);
        $crawler = $client->request('GET', $createUrl);

        // Get button by id #product_save
        $submitButton = $crawler->selectButton('product_save');
        $productForm = $submitButton->form();
        $productForm['product[basic][name][1]'] = 'Test Update Product';

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
        $this->assertTrue(isset($updateData['basic']['name'][1]));
        $this->assertEquals('Test Update Product', $updateData['basic']['name'][1]);
    }
}
