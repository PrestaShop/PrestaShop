<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Integration\Utility\LoginTrait;

abstract class ApiTestCase extends WebTestCase
{
    use ContextMockerTrait;
    use LoginTrait;

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
        $this->loginUser(self::$client);
        self::$client->setServerParameters([]);
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
