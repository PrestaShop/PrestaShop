<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Context;
use Db;
use Link;
use PHPUnit\Framework\TestCase;
use Shop;

/**
 * The back office is served from the admin folder, not from under a shop's virtual URI, so the
 * request URI cannot tell which shop the employee is working on - only the host can. These tests
 * pin that Link::getAdminBaseLink() follows the host the back office was reached on instead of
 * falling back to the default shop's domain, which would log the employee out on a multi-domain
 * installation.
 */
class LinkAdminBaseLinkTest extends TestCase
{
    private const HOST = 'second-shop.test';

    /** @var int */
    private $idShop;

    /** @var int */
    private $originalMultishop;

    /** @var array */
    private $originalServer;

    /** @var int */
    private $originalShopContext;

    /** @var int|null */
    private $originalShopContextId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalServer = $_SERVER;
        $this->originalShopContext = Shop::getContext();
        // CONTEXT_SHOP and CONTEXT_GROUP cannot be restored without their id.
        $this->originalShopContextId = Shop::CONTEXT_GROUP === $this->originalShopContext
            ? Shop::getContextShopGroupID()
            : Shop::getContextShopID();
        $this->originalMultishop = (int) Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
        Configuration::updateValue('PS_MULTISHOP_FEATURE_ACTIVE', 1);
    }

    protected function tearDown(): void
    {
        if (!empty($this->idShop)) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'shop_url WHERE id_shop = ' . (int) $this->idShop);
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'shop WHERE id_shop = ' . (int) $this->idShop);
            $this->idShop = 0;
        }

        Configuration::updateValue('PS_MULTISHOP_FEATURE_ACTIVE', $this->originalMultishop);
        $_SERVER = $this->originalServer;
        Shop::resetStaticCache();
        Shop::setContext($this->originalShopContext, $this->originalShopContextId);
        Context::getContext()->shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));

        parent::tearDown();
    }

    /**
     * @dataProvider provideShopUrls
     */
    public function testAdminBaseLinkFollowsTheHostTheBackOfficeWasReachedOn(
        string $physicalUri,
        string $virtualUri,
        string $host,
        string $expectedBase
    ): void {
        $this->createSecondShop($physicalUri, $virtualUri);
        $this->enterAllShopsContextOnHost($host);

        // A fresh Link per case: getMatchingUrlShopId() memoises its result on the instance.
        self::assertSame($expectedBase, (new Link())->getAdminBaseLink());
    }

    public function provideShopUrls(): iterable
    {
        // Plain multi-domain: already worked, pinned so the fix does not regress it.
        yield 'shop mounted at the root' => ['/', '', self::HOST, 'http://' . self::HOST . '/'];

        // The shop URI never prefixes the admin folder, so the request URI cannot match it.
        yield 'shop with a virtual URI' => ['/', 'boutique/', self::HOST, 'http://' . self::HOST . '/'];

        // PrestaShop installed in a subdirectory: the admin folder lives under the physical URI,
        // so that part must be kept while the virtual URI is dropped.
        yield 'shop in a subdirectory' => ['/subfolder/', '', self::HOST, 'http://' . self::HOST . '/subfolder/'];

        // Shop::initialize() resolves the host without its port first; Link now agrees.
        yield 'host carrying a port' => ['/', '', self::HOST . ':8080', 'http://' . self::HOST . '/'];
    }

    public function testAnExplicitShopIdKeepsReturningThatShopsFullBaseUri(): void
    {
        $this->createSecondShop('/', 'boutique/');
        $this->enterAllShopsContextOnHost(self::HOST);

        // Only the shop resolved from the request is treated as the back office one. A caller asking
        // for a given shop still gets its full base URI, which is what it got before this change.
        self::assertSame(
            'http://' . self::HOST . '/boutique/',
            (new Link())->getAdminBaseLink($this->idShop)
        );
    }

    public function testUnknownHostFallsBackToTheDefaultShop(): void
    {
        $this->createSecondShop('/', '');
        $this->enterAllShopsContextOnHost('attacker.example');

        // The host is resolved against ps_shop_url, so a Host header naming no known shop cannot
        // make PrestaShop build back office links pointing at it.
        $defaultShop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
        self::assertSame('http://' . $defaultShop->domain . '/', (new Link())->getAdminBaseLink());
    }

    public function testVirtualUriIsNotAppendedToTheAdminFolder(): void
    {
        $this->createSecondShop('/', 'boutique/');
        $this->enterAllShopsContextOnHost(self::HOST);

        // The front office of that shop lives under /boutique/, the back office does not.
        // Asserted as an exact string: a "does not contain boutique" check also passes on the
        // unfixed code, where the link is the default shop's domain.
        self::assertSame('/boutique/', (new Shop($this->idShop))->getBaseURI());
        self::assertSame('http://' . self::HOST . '/', (new Link())->getAdminBaseLink());
    }

    private function createSecondShop(string $physicalUri, string $virtualUri): void
    {
        $idDefaultShop = (int) Configuration::get('PS_SHOP_DEFAULT');
        $defaultShop = new Shop($idDefaultShop);

        Db::getInstance()->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'shop (id_shop_group, name, color, id_category, theme_name, active, deleted)
            VALUES (' . (int) $defaultShop->id_shop_group . ", 'Second shop', '', "
            . (int) $defaultShop->id_category . ", '" . pSQL($defaultShop->theme_name) . "', 1, 0)");
        $this->idShop = (int) Db::getInstance()->Insert_ID();

        Db::getInstance()->execute('
            INSERT INTO ' . _DB_PREFIX_ . 'shop_url (id_shop, domain, domain_ssl, physical_uri, virtual_uri, main, active)
            VALUES (' . $this->idShop . ", '" . pSQL(self::HOST) . "', '" . pSQL(self::HOST) . "', '"
            . pSQL($physicalUri) . "', '" . pSQL($virtualUri) . "', 1, 1)");
    }

    /**
     * Reproduces an employee working in the "all shops" context on a shop that is not the default
     * one: the context shop is the default shop, while the request was served by another host.
     */
    private function enterAllShopsContextOnHost(string $host): void
    {
        $_SERVER['HTTP_HOST'] = $host;
        $_SERVER['SERVER_NAME'] = $host;
        $_SERVER['REQUEST_URI'] = '/' . basename(_PS_ADMIN_DIR_) . '/index.php?controller=AdminDashboard';

        Shop::resetStaticCache();
        Shop::setContext(Shop::CONTEXT_ALL);
        Context::getContext()->shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
    }
}
