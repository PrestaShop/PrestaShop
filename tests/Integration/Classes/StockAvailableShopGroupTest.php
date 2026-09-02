<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration as LegacyConfiguration;
use Db;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup as ShopGroupEntity;
use Shop as LegacyShop;
use ShopGroup;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * Changing the share-stock parameter of a shop group must clean up the stock_available rows that
 * belong to the previous sharing model, instead of leaving orphan lines behind.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/28779
 */
class StockAvailableShopGroupTest extends KernelTestCase
{
    private const ID_PRODUCT = 999999;
    private const ID_GROUP = 1;

    private static int $idShop2;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$idShop2 = self::initMultistore();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreAllTables();
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('stock_available', 'id_product = ' . self::ID_PRODUCT);
        parent::tearDown();
    }

    public function testDisablingStockSharingRemovesTheObsoleteSharedRow(): void
    {
        // State right after stock sharing was disabled: the new per-shop rows plus the now-obsolete
        // shared row (id_shop = 0 / id_shop_group = group).
        $this->insertStock(0, self::ID_GROUP, 50);
        $this->insertStock(1, 0, 10);
        $this->insertStock(self::$idShop2, 0, 20);

        $group = new ShopGroup(self::ID_GROUP);
        $group->share_stock = false;
        StockAvailable::resetProductFromStockAvailableByShopGroup($group);

        // The obsolete shared row is gone; the per-shop rows are kept.
        $this->assertSame(0, $this->countStock('id_shop = 0 AND id_shop_group = ' . self::ID_GROUP));
        $this->assertSame(1, $this->countStock('id_shop = 1 AND id_shop_group = 0'));
        $this->assertSame(1, $this->countStock('id_shop = ' . self::$idShop2 . ' AND id_shop_group = 0'));
    }

    public function testEnablingStockSharingRemovesTheObsoletePerShopRows(): void
    {
        // State right after stock sharing was enabled: the new shared row plus the now-obsolete
        // per-shop rows.
        $this->insertStock(0, self::ID_GROUP, 50);
        $this->insertStock(1, 0, 10);
        $this->insertStock(self::$idShop2, 0, 20);

        $group = new ShopGroup(self::ID_GROUP);
        $group->share_stock = true;
        StockAvailable::resetProductFromStockAvailableByShopGroup($group);

        // The obsolete per-shop rows are gone; the shared row is kept (quantity reset to 0).
        $this->assertSame(0, $this->countStock('id_shop = 1 AND id_shop_group = 0'));
        $this->assertSame(0, $this->countStock('id_shop = ' . self::$idShop2 . ' AND id_shop_group = 0'));
        $this->assertSame(1, $this->countStock('id_shop = 0 AND id_shop_group = ' . self::ID_GROUP));
        $this->assertSame(
            '0',
            (string) Db::getInstance()->getValue(
                'SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available
                 WHERE id_product = ' . self::ID_PRODUCT . ' AND id_shop = 0 AND id_shop_group = ' . self::ID_GROUP
            )
        );
    }

    private function insertStock(int $idShop, int $idShopGroup, int $quantity): void
    {
        Db::getInstance()->insert('stock_available', [
            'id_product' => self::ID_PRODUCT,
            'id_product_attribute' => 0,
            'id_shop' => $idShop,
            'id_shop_group' => $idShopGroup,
            'quantity' => $quantity,
            'out_of_stock' => 0,
        ]);
    }

    private function countStock(string $where): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . self::ID_PRODUCT . ' AND ' . $where
        );
    }

    private static function initMultistore(): int
    {
        DatabaseDump::restoreAllTables();
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $configuration = $container->get('prestashop.adapter.legacy.configuration');
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $configuration->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $shopGroup = $entityManager->find(ShopGroupEntity::class, self::ID_GROUP);
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_2');
        $shop->setShopGroup($shopGroup);
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);
        $entityManager->persist($shop);
        $entityManager->flush();

        LegacyShop::resetStaticCache();

        return (int) $shop->getId();
    }
}
