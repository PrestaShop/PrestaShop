<?php

namespace PrestaShopCoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * FIXME: example, to remove
 */
class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/home');

//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }
}
