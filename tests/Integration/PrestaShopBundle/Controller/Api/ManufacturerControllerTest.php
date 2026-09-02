<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Controller\Api;

class ManufacturerControllerTest extends ApiTestCase
{
    public function testItShouldReturnOkResponseWhenRequestingSuppliers(): void
    {
        $route = $this->router->generate('api_stock_list_manufacturers');
        self::$client->request('GET', $route);

        $response = self::$client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');
    }
}
