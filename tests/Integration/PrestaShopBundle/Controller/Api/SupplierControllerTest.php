<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Controller\Api;

/**
 * @group api
 * @group supplier
 */
class SupplierControllerTest extends ApiTestCase
{
    /**
     * @test
     */
    public function itShouldReturnOkResponseWhenRequestingSuppliers()
    {
        $route = $this->router->generate('api_stock_list_suppliers');
        self::$client->request('GET', $route);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = self::$client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');
    }
}
