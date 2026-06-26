<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\EventListener\Admin\Context;

use Configuration;
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
 * @see https://github.com/PrestaShop/PrestaShop/issues/39468
 *
 * When multistore is enabled, ShopContextSubscriber inspects the matched controller to look for the
 * AllShopContext attribute. Invokable controllers (e.g. the API Platform documentation action) are
 * referenced without a "::" method separator, which used to raise "Undefined array key 1" and broke
 * the Admin API documentation page.
 */
class ShopContextSubscriberApiDocTest extends WebTestCase
{
    use ContextMockerTrait;
    use LoginTrait;

    private const TABLES = ['configuration', 'shop', 'shop_group', 'shop_url'];

    /**
     * @var KernelBrowser
     */
    private $client;

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

        $shopUrl = new ShopUrl();
        $shopUrl->id_shop = (int) $shop->id;
        $shopUrl->active = true;
        $shopUrl->main = true;
        $shopUrl->domain = 'localhost';
        $shopUrl->domain_ssl = 'localhost';
        $shopUrl->physical_uri = '/second-shop/';
        $shopUrl->virtual_uri = '';
        $shopUrl->add();

        Shop::resetStaticCache();

        $this->client = self::createClient();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::TABLES);
        Shop::resetStaticCache();
        $this->resetContext();
        parent::tearDown();
    }

    public function testApiDocumentationLoadsWithMultistoreEnabled(): void
    {
        $this->loginUser($this->client);
        $router = self::getContainer()->get(RouterInterface::class);

        // The bug surfaces as a PHP warning raised by ShopContextSubscriber while resolving the
        // shop context of the (invokable) API documentation controller; in the API Platform context
        // that warning is turned into a fatal error. Capture warnings to assert it no longer happens.
        $warnings = [];
        set_error_handler(static function (int $severity, string $message) use (&$warnings): bool {
            $warnings[] = $message;

            return false;
        }, E_WARNING);

        try {
            $this->client->request('GET', $router->generate('api_doc', ['_format' => 'json']));
        } finally {
            restore_error_handler();
        }

        $statusCode = $this->client->getResponse()->getStatusCode();
        $this->assertSame(
            Response::HTTP_OK,
            $statusCode,
            sprintf('The Admin API documentation must load when multistore is enabled, got status %d.', $statusCode)
        );
        $undefinedKeyWarnings = array_filter($warnings, static fn (string $m): bool => str_contains($m, 'Undefined array key'));
        $this->assertEmpty(
            $undefinedKeyWarnings,
            'ShopContextSubscriber must not raise an "Undefined array key" warning for invokable controllers.'
        );
    }
}
