<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration as LegacyConfiguration;
use Context;
use Db;
use Dispatcher;
use Link;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class LinkCmsShopRewriteTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_url', 'cms_lang', 'cms_shop', 'configuration'];

    private const SHOP2_REWRITE = 'cms-page-second-shop-slug';

    private static int $secondShopId;

    /**
     * @var Link|null
     */
    private static $originalContextLink;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$originalContextLink = Context::getContext()->link;
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $configuration = $container->get('prestashop.adapter.legacy.configuration');
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);
        $configuration->set('PS_REWRITING_SETTINGS', 1);
        // The dispatcher singleton snapshots PS_REWRITING_SETTINGS (use_routes) at construction,
        // so an instance built before this class saw rewriting disabled.
        Dispatcher::$instance = null;

        // Add a second shop in the existing group 1.
        $shopGroup = $entityManager->find(ShopGroup::class, 1);
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_cms');
        $shop->setShopGroup($shopGroup);
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);
        $entityManager->persist($shop);
        $entityManager->flush();
        self::$secondShopId = (int) $shop->getId();

        LegacyShop::resetStaticCache();

        // CMS page 1 exists for shop 1; give it a distinct link_rewrite in the second shop.
        Db::getInstance()->insert('cms_shop', ['id_cms' => 1, 'id_shop' => self::$secondShopId]);
        $langRows = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'cms_lang` WHERE id_cms = 1 AND id_shop = 1');
        foreach ($langRows as $row) {
            $row['id_shop'] = self::$secondShopId;
            $row['link_rewrite'] = self::SHOP2_REWRITE;
            Db::getInstance()->insert('cms_lang', $row);
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
        // Rebuild the dispatcher from the restored configuration and undo the context link
        // mutation, otherwise later test classes generate legacy URLs with use_routes still on
        // (e.g. admin links missing index.php).
        Dispatcher::$instance = null;
        Context::getContext()->link = self::$originalContextLink;
    }

    /**
     * A CMS link built for another shop must use that shop's link_rewrite. CMS link_rewrite is a
     * per-shop (multilang_shop) value, so building the link while ignoring the target shop emits
     * the current shop's slug under the target shop's domain (wrong/non-canonical URL).
     */
    public function testCmsLinkUsesTargetShopRewrite(): void
    {
        self::bootKernel();

        $link = new Link();
        $url = $link->getCMSLink(1, null, null, 1, self::$secondShopId);

        self::assertStringContainsString(
            self::SHOP2_REWRITE,
            $url,
            'getCMSLink() must use the target shop link_rewrite, not the current shop one'
        );
    }

    /**
     * The Smarty {url} helper built the CMS object without the target shop before passing it to
     * getCMSLink(), so the per-shop link_rewrite was ignored. Passing the id and the target shop
     * through lets getCMSLink() load the right shop's rewrite.
     */
    public function testCmsUrlSmartyUsesTargetShopRewrite(): void
    {
        self::bootKernel();

        Context::getContext()->link = new Link();
        $url = Link::getUrlSmarty([
            'entity' => 'cms',
            'id' => 1,
            'id_lang' => 1,
            'id_shop' => self::$secondShopId,
        ]);

        self::assertStringContainsString(
            self::SHOP2_REWRITE,
            $url,
            'getUrlSmarty() must use the target shop link_rewrite for cms links'
        );
    }
}
