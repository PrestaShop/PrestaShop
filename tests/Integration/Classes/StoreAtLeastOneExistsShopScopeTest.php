<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration as LegacyConfiguration;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Shop as LegacyShop;
use Store;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class StoreAtLeastOneExistsShopScopeTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'shop_url', 'configuration'];

    private static int $secondShopId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $configuration = $container->get('prestashop.adapter.legacy.configuration');
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        // Second shop in group 1 — the fixture stores are all associated to shop 1 only.
        $shopGroup = $entityManager->find(ShopGroup::class, 1);
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_store');
        $shop->setShopGroup($shopGroup);
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);
        $entityManager->persist($shop);
        $entityManager->flush();
        self::$secondShopId = (int) $shop->getId();

        LegacyShop::resetStaticCache();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    /**
     * atLeastOneStoreExists() gates the front-office "our stores" link for the current shop, so it
     * must be shop-scoped. It used to query the whole `store` table without the `store_shop`
     * association, reporting that stores exist for a shop that has none.
     */
    public function testAtLeastOneStoreExistsIsShopScoped(): void
    {
        self::bootKernel();

        // Shop 1 owns the fixture stores.
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);
        self::assertTrue(Store::atLeastOneStoreExists(), 'shop 1 has stores associated');

        // Shop 2 has no store associated; the previous global query wrongly returned true.
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, self::$secondShopId);
        self::assertFalse(Store::atLeastOneStoreExists(), 'shop 2 has no stores associated');
    }
}
