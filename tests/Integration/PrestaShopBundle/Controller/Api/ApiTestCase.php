<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Controller\Api;

use Context;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// bin/phpunit -c tests/phpunit-admin.xml --group api --stop-on-error --stop-on-failure --verbose --debug
abstract class ApiTestCase extends WebTestCase
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    protected $router;

    /**
     * @var \Symfony\Component\BrowserKit\Client
     */
    protected static $client;

    /**
     * Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected static $container;

    public function setUp()
    {
        parent::setUp();

        self::$kernel = static::bootKernel();
        self::$container = self::$kernel->getContainer();

        $this->router = self::$container->get('router');

        $legacyContextMock = $this->mockContextAdapter();
        self::$container->set('prestashop.adapter.legacy.context', $legacyContextMock);

        $client = self::$kernel->getContainer()->get('test.client');
        $client->setServerParameters(array());

        self::$client = $client;
    }

    public function tearDown()
    {
        parent::tearDown();

        self::$container = null;
        self::$kernel = null;
        self::$client = null;

    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockContextAdapter()
    {
        $legacyContextMock = $this->getMockBuilder(LegacyContext::class)
            ->setMethods(array(
                'getContext',
                'getEmployeeLanguageIso',
                'getEmployeeCurrency',
                'getRootUrl',
                'getLanguage'
            ))
            ->getMock()
        ;

        $contextMock = $this->mockContext();
        $legacyContextMock->method('getContext')->willReturn($contextMock);

        $legacyContextMock->method('getEmployeeLanguageIso')->willReturn(null);
        $legacyContextMock->method('getEmployeeCurrency')->willReturn(null);
        $legacyContextMock->method('getRootUrl')->willReturn(null);
        $legacyContextMock->method('getLanguage')->willReturn(new \Language());

        return $legacyContextMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockContext()
    {
        $contextMock = $this->getMockBuilder('\Context')->getMock();

        $employeeMock = $this->mockEmployee();
        $contextMock->employee = $employeeMock;

        $languageMock = $this->mockLanguage();
        $contextMock->language = $languageMock;

        $linkMock = $this->mockLink();
        $contextMock->link = $linkMock;

        $shopMock = $this->mockShop();
        $contextMock->shop = $shopMock;

        $controllerMock = $this->mockController();
        $contextMock->controller = $controllerMock;

        $contextMock->currency = (object) array('sign' => '$');

        Context::setInstanceForTesting($contextMock);

        return $contextMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockEmployee()
    {
        $employeeMock = $this->getMockBuilder('\Employee')->getMock();
        $employeeMock->id_lang = 1;

        return $employeeMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockLanguage()
    {
        $languageMock = $this->getMockBuilder('\Language')
            ->getMock()
        ;

        $languageMock->iso_code = 'en-US';

        return $languageMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockLink()
    {
        return $this->getMockBuilder('\Link')->getMock();
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockShop()
    {
        $shopMock = $this->getMockBuilder('\Shop')
            ->setMethods(array(
                'getContextualShopId',
                'getCategory',
                'getContextType',
                'getGroup',
            ))
            ->getMock()
        ;

        $shopMock->method('getContextualShopId')->willReturn(1);
        $shopMock->method('getCategory')->willReturn(1);
        $shopMock->method('getContextType')->willReturn(Shop::CONTEXT_SHOP);
        $shopMock->id = 1;

        $shopGroupMock = $this->getMockBuilder('\ShopGroup')->getMock();

        $shopGroupMock->id = 1;
        $shopMock->method('getGroup')->willReturn($shopGroupMock);

        return $shopMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function mockController()
    {
        $controller = $this->getMockBuilder('\AdminController')
            ->disableOriginalConstructor()
            ->getMock()
        ;

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
        self::$client->request('GET', $route);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = self::$client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'It should return a response with "Bad Request" Status.');
    }

    /**
     * @param $route
     * @param $params
     */
    protected function assetOkRequest($route, $params)
    {
        $route = $this->router->generate($route, $params);
        self::$client->request('GET', $route);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = self::$client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');
    }

    /**
     * @param $expectedStatusCode
     * @return mixed
     */
    protected function assertResponseBodyValidJson($expectedStatusCode)
    {
        /** @var \Symfony\Component\HttpFoundation\JsonResponse $response */
        $response = self::$client->getResponse();

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
