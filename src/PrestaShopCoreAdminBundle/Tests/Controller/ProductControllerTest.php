<?php

namespace PrestaShopCoreAdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests about admin ProductController and its actions.
 */
class ProductControllerTest extends WebTestCase
{
    public function testCatalog()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/catalog');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('POST', '/product/catalog');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // TODO !9 : add check of HTML listing table, OR empty case
    }

    public function testCatalogParams()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/product/catalog/1/2/id_product/asc');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/product/catalog/a/b/toto/titi');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
