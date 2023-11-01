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

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\ContextMockerTrait;

abstract class ApiTestCase extends WebTestCase
{
    use ContextMockerTrait;

    /**
     * @var KernelBrowser|null
     */
    protected static $client;

    /**
     * @var RouterInterface
     */
    protected $router;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::mockContext();
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::$kernel = static::bootKernel();
        self::$client = self::$kernel->getContainer()->get('test.client');
        self::$client->setServerParameters([]);
        self::$container = self::$client->getContainer();
        $this->router = self::getContainer()->get('router');
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
