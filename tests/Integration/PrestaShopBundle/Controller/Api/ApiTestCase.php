<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Integration\PrestaShopBundle\Controller\Api;

use Context;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// bin/phpunit -c tests/phpunit-admin.xml --group api --stop-on-error --stop-on-failure --verbose --debug
abstract class ApiTestCase extends WebTestCase
{
    /**
     * @var \Prophecy\Prophet
     */
    protected $prophet;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \Symfony\Component\BrowserKit\Client
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();

        $this->client = $this->createClient();

        $container = self::$kernel->getContainer();
        $this->router = $container->get('router');
        $legacyContextMock = $this->mockContextAdapter();
        $container->set('prestashop.adapter.legacy.context', $legacyContextMock->reveal());
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

    /**
     * @return \Prophecy\Prophecy\ObjectProphecy
     */
    protected function mockContextAdapter()
    {
        /** @var LegacyContext|ObjectProphecy $legacyContextMock */
        $legacyContextMock = $this->prophet->prophesize('\PrestaShop\PrestaShop\Adapter\LegacyContext');

        $contextMock = $this->mockContext();
        $legacyContextMock->getContext()->willReturn($contextMock->reveal());

        $legacyContextMock->getEmployeeLanguageIso()->willReturn(null);
        $legacyContextMock->getEmployeeCurrency()->willReturn(null);
        $legacyContextMock->getRootUrl()->willReturn(null);
        $legacyContextMock->getLanguage()->willReturn(new \Language());

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

        $linkMock = $this->mockLink();
        $contextMock->link = $linkMock->reveal();

        $shopMock = $this->mockShop();
        $contextMock->shop = $shopMock->reveal();

        $controllerMock = $this->mockController();
        $contextMock->controller = $controllerMock->reveal();

        $contextMock->currency = (object)array('sign' => '$');

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

    /**
     * @return object
     */
    private function mockLink()
    {
        $linkMock = $this->prophet->prophesize('\Link');

        return $linkMock;
    }

    private function mockShop()
    {
        /** @var \Shop $shopMock */
        $shopMock = $this->prophet->prophesize('\Shop');
        $shopMock->getContextualShopId()->willReturn(1);
        $shopMock->getCategory()->willReturn(1);
        $shopMock->getContextType()->willReturn(Shop::CONTEXT_SHOP);
        $shopMock->id = 1;

        $shopGroupMock = $this->prophet->prophesize('\ShopGroup');
        $shopGroupMock->id = 1;
        $shopMock->getGroup()->willReturn($shopGroupMock);

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

    /**
     * @param $route
     * @param $params
     */
    protected function assertBadRequest($route, $params)
    {
        $route = $this->router->generate($route, $params);
        $this->client->request('GET', $route);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'It should return a response with "Bad Request" Status.');
    }

    /**
     * @param $route
     * @param $params
     */
    protected function assetOkRequest($route, $params)
    {
        $route = $this->router->generate($route, $params);
        $this->client->request('GET', $route);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');
    }

    /**
     * @param $expectedStatusCode
     * @return mixed
     */
    protected function assertResponseBodyValidJson($expectedStatusCode)
    {
        /** @var \Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = $this->client->getResponse();

        $message = 'Unexpected status code.';

        switch ($expectedStatusCode) {
            case 200:
                $message = 'It should return a response with "OK" Status.';
                break;
            case 400:
                $message = 'It should return a response with "Bad Request" Status.';
                break;
            case 404:
                $message = 'It should return a response with "Not Found" Status.';
                break;

            default:
                $this->fail($message);
        }

        $this->assertEquals($expectedStatusCode, $response->getStatusCode(), $message);

        $content = json_decode($response->getContent(), true);

        $this->assertEquals(
            JSON_ERROR_NONE,
            json_last_error(),
            'The response body should be a valid json document.'
        );

        return $content;
    }
}
