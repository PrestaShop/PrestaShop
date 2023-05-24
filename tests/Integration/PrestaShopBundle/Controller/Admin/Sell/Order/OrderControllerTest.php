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

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Order;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Tests\Integration\Utility\ContextMockerTrait;

class OrderControllerTest extends WebTestCase
{
    use ContextMockerTrait;

    /**
     * @var KernelBrowser
     */
    protected $client;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var CsrfTokenManager
     */
    protected $tokenManager;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        // Enable debug mode (for data)
        $configurationMock = $this->getMockBuilder(Configuration::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->disableAutoload()
            ->getMock();

        $configurationMock->method('get')
            ->will($this->returnValueMap([
                ['_PS_MODE_DEMO_', null, null, true],
            ]));

        $this->client = self::createClient();
        self::$kernel->getContainer()->set('prestashop.adapter.legacy.configuration', $configurationMock);
        $this->router = self::$kernel->getContainer()->get('router');
        $this->tokenManager = self::$kernel->getContainer()->get('security.csrf.token_manager');
    }

    public function testSearchProductsWithContent(): void
    {
        $token = $this->tokenManager->getToken('form');
        $this->client->request(
            'GET',
            $this->router->generate(
                'admin_orders_products_search',
                [
                    'search_phrase' => 'Brown bear',
                    'currency_id' => 1,
                    'order_id' => 1,
                    '_token' => $token->getValue(),
                ]
            )
        );

        /** @var JsonResponse $response */
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = $response->getContent();
        $content = json_decode($content, true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('products', $content);
        $this->assertIsArray($content['products']);
    }

    public function testSearchProductsEmptyPhrase(): void
    {
        $token = $this->tokenManager->getToken('form');
        $this->client->request(
            'GET',
            $this->router->generate(
                'admin_orders_products_search',
                [
                    'search_phrase' => '',
                    'currency_id' => 1,
                    'order_id' => 1,
                    '_token' => $token->getValue(),
                ]
            )
        );

        /** @var JsonResponse $response */
        $response = $this->client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = $response->getContent();
        $content = json_decode($content, true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('message', $content);
        $this->assertEquals('Product search phrase must not be an empty string.', $content['message']);
    }
}
