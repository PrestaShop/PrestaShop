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

class SearchAliasControllerTest extends WebTestCase
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

    public function testIndexActionReturnsSuccessResponse(): void
    {
        $this->client->request('GET', $this->router->generate('admin_search_alias_index'));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testIndexActionRendersSidebarHelpButton(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_search_alias_index'));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        // The help button must open the right sidebar (btn-sidebar), not a new window.
        // This was broken because enableSidebar was not passed to the template.
        // See: https://github.com/PrestaShop/PrestaShop/issues/40932
        $this->assertGreaterThan(
            0,
            $crawler->filter('.btn-sidebar')->count(),
            'The help button on the Aliases index page must use the sidebar (btn-sidebar class), not a plain link.'
        );
    }

    public function testCreateActionReturnsSuccessResponse(): void
    {
        $this->client->request('GET', $this->router->generate('admin_search_alias_create'));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateActionRendersSidebarHelpButton(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_search_alias_create'));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertGreaterThan(
            0,
            $crawler->filter('.btn-sidebar')->count(),
            'The help button on the Aliases create page must use the sidebar (btn-sidebar class).'
        );
    }
}
