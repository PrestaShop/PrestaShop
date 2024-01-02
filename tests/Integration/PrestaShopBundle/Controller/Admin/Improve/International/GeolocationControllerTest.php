<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GeolocationControllerTest extends WebTestCase
{
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
            ->setMethods(['get'])
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
        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);
        $this->router = self::$kernel->getContainer()->get('router');
    }

    public function testIndexAction(): void
    {
        $this->client->request('GET', $this->router->generate('admin_geolocation_index'));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
