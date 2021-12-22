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

namespace Tests\Integration\PrestaShopBundle\Controller\Api;

use AdminController;
use Context;
use Employee;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use Shop;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class ApiTestCase extends WebTestCase
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Client
     */
    protected static $client;

    /**
     * @var Context
     */
    protected $oldContext;

    /**
     * @var ContainerInterface
     */
    protected static $container;

    protected function setUp(): void
    {
        parent::setUp();

        self::$kernel = static::bootKernel();
        self::$container = self::$kernel->getContainer();

        $this->router = self::$container->get('router');

        $this->oldContext = Context::getContext();
        $legacyContextMock = $this->mockContextAdapter();
        self::$container->set('prestashop.adapter.legacy.context', $legacyContextMock);

        $client = self::$kernel->getContainer()->get('test.client');
        $client->setServerParameters([]);

        self::$client = $client;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        self::$container = null;
        self::$kernel = null;
        self::$client = null;
        Context::setInstanceForTesting($this->oldContext);
    }

    protected function mockContextAdapter(): LegacyContext
    {
        $legacyContextMock = $this->getMockBuilder(LegacyContext::class)
            ->setMethods([
                'getContext',
                'getEmployeeLanguageIso',
                'getEmployeeCurrency',
                'getRootUrl',
                'getLanguage',
            ])
            ->getMock();

        $contextMock = $this->mockContext();
        $legacyContextMock->expects($this->any())->method('getContext')->willReturn($contextMock);

        $legacyContextMock->method('getEmployeeLanguageIso')->willReturn(null);
        $legacyContextMock->method('getEmployeeCurrency')->willReturn(null);
        $legacyContextMock->method('getRootUrl')->willReturn(null);
        $legacyContextMock->method('getLanguage')->willReturn(new Language());

        return $legacyContextMock;
    }

    private function mockContext(): Context
    {
        $contextMock = $this->getMockBuilder(Context::class)->getMock();

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

        $contextMock->currency = (object) ['sign' => '$'];

        Context::setInstanceForTesting($contextMock);

        return $contextMock;
    }

    private function mockEmployee(): Employee
    {
        $employeeMock = $this->getMockBuilder(Employee::class)->getMock();
        $employeeMock->id_lang = 1;

        return $employeeMock;
    }

    private function mockLanguage(): Language
    {
        $languageMock = $this->getMockBuilder(Language::class)
            ->getMock();

        $languageMock->iso_code = 'en-US';

        return $languageMock;
    }

    private function mockLink(): Link
    {
        return $this->getMockBuilder(Link::class)->getMock();
    }

    private function mockShop(): Shop
    {
        $shopMock = $this->getMockBuilder(Shop::class)
            ->setMethods([
                'getContextualShopId',
                'getCategory',
                'getContextType',
                'getGroup',
            ])
            ->getMock();

        $shopMock->method('getContextualShopId')->willReturn(1);
        $shopMock->method('getCategory')->willReturn(1);
        $shopMock->method('getContextType')->willReturn(Shop::CONTEXT_SHOP);
        $shopMock->id = 1;

        $themeMock = $this->getMockBuilder(Theme::class)
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock()
        ;
        $themeMock->method('getName')->willReturn('classic');

        $shopMock->theme = $themeMock;

        $shopGroupMock = $this->getMockBuilder('\ShopGroup')->getMock();

        $shopGroupMock->id = 1;
        $shopMock->method('getGroup')->willReturn($shopGroupMock);

        return $shopMock;
    }

    private function mockController(): AdminController
    {
        $controller = $this->getMockBuilder(AdminController::class)
            ->disableOriginalConstructor()
            ->getMock();

        $controller->controller_type = 'admin';

        return $controller;
    }

    /**
     * @param string $route
     * @param array $params
     */
    protected function assertBadRequest(string $route, array $params): void
    {
        $route = $this->router->generate($route, $params);
        self::$client->request('GET', $route);

        $response = self::$client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'It should return a response with "Bad Request" Status.');
    }

    /**
     * @param string $route
     * @param array $params
     */
    protected function assertOkRequest(string $route, array $params): void
    {
        $route = $this->router->generate($route, $params);
        self::$client->request('GET', $route);

        $response = self::$client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');
    }

    /**
     * @param int $expectedStatusCode
     *
     * @return array
     */
    protected function assertResponseBodyValidJson(int $expectedStatusCode): array
    {
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
