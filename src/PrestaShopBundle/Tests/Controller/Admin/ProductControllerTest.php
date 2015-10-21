<?php

namespace PrestaShopBundle\Tests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests about admin ProductController and its actions.
 */
class ProductControllerTest extends WebTestCase
{
    /**
     * Test all Productcontroller URLs.
     *
     * @dataProvider urlProvider
     *
     * @param string $url The URL to test
     * @param string $method (GET|POST)
     * @param integer $statusCode 200, 404, etc...
     */
    public function testUrls($url, $method, $statusCode = 200)
    {
        $client = static::createClient();
        $client->request($method, $url);

        $this->assertEquals($statusCode, $client->getResponse()->getStatusCode());
    }

    public function urlProvider()
    {
        // 405: method not allowed
        // 302: URL redirection ('found')
        return array(
            array('/product/catalog', 'GET'),
            array('/product/catalog', 'POST'),
            array('/product/catalog/test/test', 'GET', 404),
            array('/product/catalog/0/10', 'GET'),
            array('/product/catalog/0/10/test/test', 'GET', 404),
            array('/product/catalog/0/10/name_category/asc', 'GET'),

            array('/product/list', 'GET'),
            array('/product/list', 'POST', 405),
            array('/product/list/test/test', 'GET', 404),
            array('/product/list/0/10', 'GET'),
            array('/product/list/0/10/test/test', 'GET', 404),
            array('/product/list/0/10/name_category/asc', 'GET'),

            array('/product/form', 'GET'),

            array('/product/uselegacy/1', 'GET', 302),
            array('/product/uselegacy/0', 'GET', 302),
            array('/product/uselegacy/test', 'GET', 404),
            array('/product/uselegacy', 'GET', 404),

            array('/product/bulk', 'GET', 404),
            
            array('/product/catalog', 'GET'),
            // TODO !0: complete !
        );
    }

    /**
     * Simple basic tests on catalogAction
     */
    public function testCatalog()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/catalog');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // response contains category tree div.
        $this->assertGreaterThan(0, $crawler->filter('div#product_catalog_category_tree_filter')->count());
        // response contains form
        $this->assertGreaterThan(0, $crawler->filter('form#product_catalog_list')->count());
        //response contains table
        $this->assertGreaterThan(0, $crawler->filter('form#product_catalog_list table.table.product')->count());

        $crawler = $client->request('POST', '/product/catalog');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Tests about route attributes on catalogAction
     */
    public function testCatalogParams()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/catalog/1/2/id_product/asc');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/product/catalog/a/b/toto/titi');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Tests posting filter on catalogAction
     *
     * We POST input[name="filter_column_id_product"] with "1"
     * by several methods: submit of form, click on submit button.
     * Then we test if POST gives a response and if response does contains "1" value.
     *
     * @
     */
    public function testCatalogColumnFilter()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/catalog');

        // get form and set values, then send with submit action
        $form = $crawler->filter('input[name="products_filter_submit"]')->form();
        $form['filter_column_id_product'] = "1";
        $crawler = $client->submit($form);

        // test if data is returned in the new form input value
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("1", $crawler->filter('input[name="filter_column_id_product"]')->attr('value'));

        // filtering by ID can return 0 or 1 element only.
        $this->assertLessThanOrEqual(1, $crawler->filter('form#product_catalog_list table.table.product tbody tr')->count());

        // reset filter
        $form = $crawler->filter('input[name="products_filter_submit"]')->form();
        $form['filter_column_id_product'] = "";
        $crawler = $client->submit($form);

        // test if data is returned in the new form input value
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("", $crawler->filter('input[name="filter_column_id_product"]')->attr('value'));
    }

    /**
     * Simple basic tests on listAction
     */
    public function testList()
    {
        // TODO !1 : GET 200, POST 404
    }
    
    // TODO !1 : listAction with different params...

    // TODO !2 : navigation on catalogAction, click on the next page if any, test if offset=2 gives max 2 products.

    // TODO !3 : filter with price range, and use improbable amount, then test no product returned.
}
