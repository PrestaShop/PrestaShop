<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\EventListener\Admin;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Integration\Utility\LoginTrait;

class AccessDeniedListenerTest extends WebTestCase
{
    use LoginTrait;

    protected KernelBrowser $client;
    protected Router $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->router = self::$kernel->getContainer()->get('router');
    }

    public function testAccessDenied(): void
    {
        $this->loginUser($this->client);

        $this->client->request('GET', $this->router->generate('test_index'));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testValidAccess(): void
    {
        $this->loginUser($this->client);

        $this->client->request('GET', $this->router->generate('test_something_complex'));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('ComplexAction', $response->getContent());
    }

    public function testRedirection(): void
    {
        $this->loginUser($this->client);
        $this->client->request('GET', $this->router->generate('test_redirect'));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertStringStartsWith('/tests/something-complex?_token=', $response->headers->get('location'));
        $this->assertStringContainsString('Redirecting to /tests/something-complex', $response->getContent());
    }

    public function testAnonymous(): void
    {
        $this->client->request('GET', $this->router->generate('test_anonymous'));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('AnonymousController', $response->getContent());

        $this->client->request('GET', $this->router->generate('test_hard_coded_anonymous'));

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('AnonymousController', $response->getContent());
    }
}
