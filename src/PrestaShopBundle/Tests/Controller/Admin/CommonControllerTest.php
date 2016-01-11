<?php

namespace PrestaShopBundle\Tests\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests about admin CommonController and its actions.
 */
class CommonControllerTest extends WebTestCase
{
    /**
     * Test all CommonController URLs.
     *
     * @dataProvider urlProvider
     *
     * @param string $url The URL to test
     * @param string $method (GET|POST)
     * @param integer $statusCode 200, 404, etc...
     * @param array $parameters GET|POST parameters
     */
    public function testUrls($url, $method, $statusCode = 200, $parameters = array())
    {
        $client = static::createClient();
        $client->request($method, $url, $parameters);
        $this->assertEquals($statusCode, $client->getResponse()->getStatusCode());
    }

    /**
     * Contains all URLs that could be rejected or accepted by the ProductController.
     *
     * Please avoid side-effect URLs here (POST or actions that modify data in DB).
     *
     * @return multitype:string  multitype:string number array
     */
    public function urlProvider()
    {
        // if no status code given, then 200 by default
        // 405: method not allowed
        // 302: URL redirection ('found')
        return array(
            ['/common/pagination', 'GET', 500], // route & action OK, but missing caller_route in GET params
            ['/common/pagination', 'POST', 405],
            ['/common/pagination/test', 'GET', 404],
            ['/common/pagination/test', 'POST', 404],
            ['/common/pagination/1/2/3', 'GET', 500], // route & action OK, but missing caller_route in GET params
            ['/common/pagination/1/2/3', 'POST', 405],

            ['/common/upload', 'GET', 405],
            ['/common/upload', 'POST', 400], // Post parameter not valid
            ['/common/upload/test', 'GET', 404],
            ['/common/upload/test', 'POST', 404],
        );
    }
}
