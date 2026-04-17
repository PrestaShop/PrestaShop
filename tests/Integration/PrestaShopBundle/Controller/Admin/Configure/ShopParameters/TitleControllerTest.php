<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Integration\Utility\LoginTrait;

class TitleControllerTest extends WebTestCase
{
    use ContextMockerTrait;
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
        self::mockContext();

        $this->client = self::createClient();
        $this->loginUser($this->client);
        $this->router = self::$kernel->getContainer()->get('router');
    }

    public function testIndexAction(): void
    {
        $this->client->request('GET', $this->router->generate('admin_title_index'));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
