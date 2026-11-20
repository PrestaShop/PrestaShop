<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Order;

use Configuration;
use Context;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Shop;
use ShopUrl;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Integration\Utility\LoginTrait;
use Tests\Resources\DatabaseDump;

/**
 * @see https://github.com/PrestaShop/PrestaShop/issues/31822
 *
 * The order detail page must not be displayed when the current shop context does not include the
 * order's shop, just like the orders list which is restricted to the context shops.
 */
class OrderControllerMultishopTest extends WebTestCase
{
    use ContextMockerTrait;
    use LoginTrait;

    private const TABLES = ['configuration', 'shop', 'shop_group', 'shop_url'];

    /**
     * The default fixtures place every order in the default shop (1).
     */
    private const ORDER_IN_SHOP_1 = 1;

    /**
     * @var KernelBrowser
     */
    private $client;

    /**
     * @var int
     */
    private $secondShopId;

    /**
     * @var mixed
     */
    private $previousKernel;

    /**
     * @var mixed
     */
    private $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::TABLES);
        self::mockContext();

        Configuration::updateGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE', '1');

        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = 1;
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = 'Second shop';
        $shop->add();
        $this->secondShopId = (int) $shop->id;

        $shopUrl = new ShopUrl();
        $shopUrl->id_shop = $this->secondShopId;
        $shopUrl->active = true;
        $shopUrl->main = true;
        $shopUrl->domain = 'localhost';
        $shopUrl->domain_ssl = 'localhost';
        $shopUrl->physical_uri = '/second-shop/';
        $shopUrl->virtual_uri = '';
        $shopUrl->add();

        Shop::resetStaticCache();

        $this->client = self::createClient();

        // mockContext() resets SymfonyContainer's cached instance, and it can only repopulate from
        // the global $kernel. A KernelTestCase running earlier in the suite (e.g.
        // AdminSearchControllerCoreTest) leaves that global pointing at a shut-down kernel, so the
        // legacy layer reached from the order form (Cart -> ContainerFinder) would throw
        // "Kernel Container is not available" and the page would answer 500. Point both at the
        // client's live kernel.
        global $kernel;
        $this->previousKernel = $kernel;
        $this->previousContainer = Context::getContext()->container ?? null;
        $kernel = self::$kernel;
        Context::getContext()->container = self::getContainer();
    }

    protected function tearDown(): void
    {
        // Put the globals back exactly as they were, so this test does not hand a live-but-about-to-die
        // kernel to the rest of the suite.
        global $kernel;
        $kernel = $this->previousKernel;
        Context::getContext()->container = $this->previousContainer;
        // Drop the cached reference to this test's kernel container as well, so the next test
        // resolves it from the restored global instead of a container we are about to shut down.
        SymfonyContainer::resetStaticCache();

        DatabaseDump::restoreTables(self::TABLES);
        Shop::resetStaticCache();
        $this->resetContext();
        parent::tearDown();
    }

    public function testViewingAnOrderFromAnotherShopRedirectsToTheList(): void
    {
        // Bootstrap the back office into the second shop, which does not own order #1.
        $this->loginUser($this->client, ShopConstraint::shop($this->secondShopId));
        $router = self::getContainer()->get(RouterInterface::class);

        $this->client->request('GET', $router->generate('admin_orders_view', ['orderId' => self::ORDER_IN_SHOP_1]));

        $response = $this->client->getResponse();
        $this->assertTrue(
            $response->isRedirect($router->generate('admin_orders_index')),
            sprintf('Expected a redirect to the orders list, got status %d.', $response->getStatusCode())
        );
    }

    public function testViewingAnOrderFromTheCurrentShopIsAllowed(): void
    {
        // Bootstrap into shop 1, which owns order #1: the page must not redirect to the list.
        $this->loginUser($this->client, ShopConstraint::shop(1));
        $router = self::getContainer()->get(RouterInterface::class);

        $this->client->request('GET', $router->generate('admin_orders_view', ['orderId' => self::ORDER_IN_SHOP_1]));

        $response = $this->client->getResponse();
        $this->assertFalse(
            $response->isRedirect($router->generate('admin_orders_index')),
            'The order of the current shop must not be redirected away from.'
        );
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
