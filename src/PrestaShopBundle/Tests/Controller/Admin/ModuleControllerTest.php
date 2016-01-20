<?php

namespace PrestaShopBundle\Tests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ModuleControllerTest extends WebTestCase
{
    public function testCatalog()
    {
        $client = static::createClient();

        $client->request('GET', '/catalog');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
