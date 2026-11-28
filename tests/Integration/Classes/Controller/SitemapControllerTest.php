<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Cache;
use Category;
use Configuration as LegacyConfiguration;
use Context;
use Db;
use Language;
use Link;
use ReflectionClass;
use ReflectionProperty;
use Shop as LegacyShop;
use SitemapController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class SitemapControllerTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['category', 'category_lang', 'category_shop', 'shop', 'shop_group', 'configuration'];

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        Cache::clean('Category::*');
        self::bootKernel();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
        Cache::clean('Category::*');
        parent::tearDown();
    }

    public function testGetCategoriesLinksUsesCurrentShopRootCategoryInMultishop(): void
    {
        $configuration = self::$kernel->getContainer()->get('prestashop.adapter.legacy.configuration');
        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $db = Db::getInstance();
        $now = date('Y-m-d H:i:s');
        $db->insert('category', [
            'id_parent' => 1,
            'id_shop_default' => 1,
            'level_depth' => 1,
            'nleft' => 100,
            'nright' => 101,
            'active' => 1,
            'date_add' => $now,
            'date_upd' => $now,
            'position' => 1,
            'is_root_category' => 1,
        ]);
        $secondRootCategoryId = (int) $db->Insert_ID();

        $db->insert('category_lang', [
            'id_category' => $secondRootCategoryId,
            'id_shop' => 1,
            'id_lang' => 1,
            'name' => 'Second Shop Home',
            'description' => '',
            'link_rewrite' => 'second-shop-home',
        ]);
        $db->insert('category_shop', [
            'id_category' => $secondRootCategoryId,
            'id_shop' => 1,
            'position' => 1,
        ]);

        $db->insert('shop_group', [
            'name' => 'test_sitemap_group',
            'color' => '',
            'share_customer' => 0,
            'share_order' => 0,
            'share_stock' => 0,
            'active' => 1,
            'deleted' => 0,
        ]);
        $secondGroupId = (int) $db->Insert_ID();
        $db->insert('shop', [
            'id_shop_group' => $secondGroupId,
            'name' => 'test_sitemap_shop',
            'color' => '',
            'id_category' => $secondRootCategoryId,
            'theme_name' => 'classic',
            'active' => 1,
            'deleted' => 0,
        ]);

        LegacyShop::resetStaticCache();
        Cache::clean('Category::*');

        $context = Context::getContext();
        $context->shop = new LegacyShop(1);
        $context->language = new Language(1);
        $context->link = new Link();

        // Sanity check: without passing $shop, Category::getRootCategory() falls back to top category 1
        $this->assertTrue(LegacyShop::isFeatureActive());
        $this->assertSame(1, (int) Category::getRootCategory()->id);

        $controller = (new ReflectionClass(SitemapController::class))->newInstanceWithoutConstructor();
        $contextProp = new ReflectionProperty($controller, 'context');
        $contextProp->setValue($controller, $context);

        $links = $controller->getCategoriesLinks();

        $this->assertNotEmpty($links);
        $this->assertSame('category-page-' . (int) $context->shop->getCategory(), $links[0]['id']);
        $this->assertNotSame('category-page-1', $links[0]['id']);
    }
}
