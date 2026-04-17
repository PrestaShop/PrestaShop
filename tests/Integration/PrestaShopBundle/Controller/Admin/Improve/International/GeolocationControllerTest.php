<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Integration\Utility\LoginTrait;

class GeolocationControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @var KernelBrowser
     */
    protected $client;
    /**
     * @var Router
     */
    protected $router;

    protected function setUp(): void
    {
        parent::setUp();

        // Enable debug mode
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMock();

        $values = [
            ['_PS_MODE_DEMO_', null, null, true],
            ['_PS_MODULE_DIR_', null, null, dirname(__DIR__, 3) . '/resources/modules/'],
        ];

        $configurationMock->method('get')
            ->will($this->returnValueMap($values));

        $this->client = self::createClient();
        $this->loginUser($this->client);
        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);
        $this->router = self::$kernel->getContainer()->get('router');
    }

    public function testIndexAction(): void
    {
        $this->client->request('GET', $this->router->generate('admin_geolocation_index'));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
