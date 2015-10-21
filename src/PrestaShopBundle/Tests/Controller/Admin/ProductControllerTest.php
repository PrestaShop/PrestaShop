<?php

namespace PrestaShopBundle\Tests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests about admin ProductController and its actions.
 */
class ProductControllerTest extends WebTestCase
{
    /**
     * Test all ProductController URLs.
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

    /**
     * Contains all URLs that could be rejected or accepted by the ProductController.
     *
     * Please avoid side-effect URLs here (POST or actions that modify data in DB).
     *
     * @return multitype:multitype:string  multitype:string number
     */
    public function urlProvider()
    {
        // if no status code given, then 200 by default
        // 405: method not allowed
        // 302: URL redirection ('found')
        return array(
            ['/product/catalog', 'GET'],
            ['/product/catalog', 'POST'],
            ['/product/catalog/test/test', 'GET', 404],
            ['/product/catalog/0/10', 'GET'],
            ['/product/catalog/0/10/test/test', 'GET', 404],
            ['/product/catalog/0/10/name_category/asc', 'GET'],
            ['/product/catalog/0/10/position_ordering/asc', 'GET'],

            ['/product/list', 'GET'],
            ['/product/list', 'POST', 405],
            ['/product/list/test/test', 'GET', 404],
            ['/product/list/0/10', 'GET'],
            ['/product/list/0/10/test/test', 'GET', 404],
            ['/product/list/0/10/name_category/asc', 'GET'],

            ['/product/form', 'GET'],
            // TODO: Luc, test it!

            ['/product/uselegacy/1', 'GET', 302],
            ['/product/uselegacy/0', 'GET', 302],
            ['/product/uselegacy/test', 'GET', 404],
            ['/product/uselegacy', 'GET', 404],

            ['/product/bulk', 'GET', 404],
            ['/product/bulk/test', 'GET', 404],
            ['/product/bulk/activate_all', 'GET', 405],
            ['/product/bulk', 'POST', 404],
            ['/product/bulk/test', 'POST', 404],
            ['/product/bulk/activate_all', 'POST', 500], // route & action OK, but missing POST parameters

            ['/product/unit', 'GET', 404],
            ['/product/unit/test', 'GET', 404],
            ['/product/unit/duplicate', 'GET', 404],
            ['/product/unit/duplicate/0', 'GET', 405], // even if 0 is not a valid ID, GET is forbidden
            ['/product/unit', 'POST', 404],
            ['/product/unit/test', 'POST', 404],
            ['/product/unit/duplicate', 'POST', 404], // route & action OK, but missing POST parameters
            ['/product/unit/duplicate/0', 'POST', 500], // route & action OK, but ID 0 is not valid

            ['/product/massedit', 'GET', 404],
            ['/product/massedit/test', 'GET', 404],
            ['/product/massedit/sort', 'GET', 405],
            ['/product/massedit', 'POST', 404],
            ['/product/massedit/test', 'POST', 404],
            ['/product/massedit/sort', 'POST', 500], // route & action OK, but missing POST parameters
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
}
