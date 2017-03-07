<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Integration\PrestaShopBundle\Controller\Api;

use Prophecy\Prophet;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Context;
use Shop;

/**
 * @group admin
 */
class StockControllerTest extends WebTestCase
{
    /**
     * @var \Prophecy\Prophet
     */
    private $prophet;

    public function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

    public function testListAction()
    {
        $client = $this->createClient();
        $container = self::$kernel->getContainer();

        $router = $container->get('router');
        $productStockListRoute = $router->generate('api_product_stock_list');

        $legacyContextMock = $this->mockContextAdapter();
        $container->set('prestashop.adapter.legacy.context', $legacyContextMock->reveal());

        $client->request('GET', $productStockListRoute);

        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with OK Status.');

        $this->tearDown();
    }

    /**
     * @return \PrestaShop\PrestaShop\Adapter\LegacyContext
     */
    private function mockContextAdapter()
    {
        /** @var \PrestaShop\PrestaShop\Adapter\LegacyContext $legacyContextMock */
        $legacyContextMock = $this->prophet->prophesize('\PrestaShop\PrestaShop\Adapter\LegacyContext');

        $contextMock = $this->mockContext();
        $legacyContextMock->getContext()->willReturn($contextMock->reveal());

        $legacyContextMock->getEmployeeLanguageIso()->willReturn(null);

        return $legacyContextMock;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockContext()
    {
        $contextMock = $this->prophet->prophesize('\Context');

        $employeeMock = $this->mockEmployee();
        $contextMock->employee = $employeeMock->reveal();

        $languageMock = $this->mockLanguage();
        $contextMock->language = $languageMock->reveal();

        $shopMock = $this->mockShop();
        $contextMock->shop = $shopMock->reveal();

        $controllerMock = $this->mockController();
        $contextMock->controller = $controllerMock->reveal();

        Context::setInstanceForTesting($contextMock->reveal());

        return $contextMock;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockEmployee()
    {
        $employeeMock = $this->prophet->prophesize('\Employee');
        $employeeMock->id_lang = 1;

        return $employeeMock;
    }

    /**
     * @return object
     */
    private function mockLanguage()
    {
        $languageMock = $this->prophet->prophesize('\Language');
        $languageMock->iso_code = 'en-US';

        return $languageMock;
    }

    private function mockShop()
    {
        /** @var \Shop $shopMock */
        $shopMock = $this->prophet->prophesize('\Shop');
        $shopMock->getContextualShopId()->willReturn(1);

        $shopMock->getContextType()->willReturn(Shop::CONTEXT_SHOP);

        return $shopMock;
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    private function mockController()
    {
        $controller = $this->prophesize('\AdminController');
        $controller->controller_type = 'admin';

        return $controller;
    }
}
