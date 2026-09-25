<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Product;

use Cache;
use Db;
use PHPUnit\Framework\TestCase;
use Product;
use Shop;
use StockAvailable;

/**
 * The webservice builds its objects with `new Product($id)`, and $quantity is only filled by
 * loadStockData(), which the constructor runs for a full load. Every product was therefore reported
 * with a quantity of 0.
 */
class WsQuantityTest extends TestCase
{
    private const PRODUCT_ID = 1;

    private int $previousQuantity = 0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->previousQuantity = (int) StockAvailable::getQuantityAvailableByProduct(self::PRODUCT_ID, 0);
    }

    protected function tearDown(): void
    {
        StockAvailable::setQuantity(self::PRODUCT_ID, 0, $this->previousQuantity);

        parent::tearDown();
    }

    public function testTheWebserviceReportsTheAvailableQuantity(): void
    {
        StockAvailable::setQuantity(self::PRODUCT_ID, 0, 42);

        $product = new Product(self::PRODUCT_ID);

        $this->assertSame(0, (int) $product->quantity, 'a plain load still leaves the property empty');
        $this->assertSame(42, (int) $product->getWsQuantity());
    }

    public function testItFollowsTheStockAndNotTheProductRow(): void
    {
        StockAvailable::setQuantity(self::PRODUCT_ID, 0, 7);
        $this->assertSame(7, (int) (new Product(self::PRODUCT_ID))->getWsQuantity());

        StockAvailable::setQuantity(self::PRODUCT_ID, 0, 0);
        $this->assertSame(0, (int) (new Product(self::PRODUCT_ID))->getWsQuantity());
    }

    /**
     * The field stays read-only: stock is written through the stock_availables resource.
     */
    public function testTheFieldIsStillReadOnly(): void
    {
        $parameters = (new Product())->getWebserviceParameters();

        $this->assertSame('getWsQuantity', $parameters['fields']['quantity']['getter']);
        $this->assertFalse($parameters['fields']['quantity']['setter']);
    }

    /**
     * On a multishop install the figure must belong to the shop the request is scoped to.
     */
    public function testItReportsTheQuantityOfTheShopInContext(): void
    {
        $second = $this->addShop();

        try {
            StockAvailable::setQuantity(self::PRODUCT_ID, 0, 300, 1);
            StockAvailable::setQuantity(self::PRODUCT_ID, 0, 77, $second);

            foreach ([1 => 300, $second => 77] as $idShop => $expected) {
                Shop::setContext(Shop::CONTEXT_SHOP, $idShop);
                Product::resetStaticCache();
                Cache::clean('*');

                $this->assertSame(
                    $expected,
                    (int) (new Product(self::PRODUCT_ID))->getWsQuantity(),
                    'shop ' . $idShop . ' must report its own quantity'
                );
            }
        } finally {
            $this->removeShop($second);
        }
    }

    /**
     * Turns the installation into a two-shop one for the duration of a single test.
     */
    private function addShop(): int
    {
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'configuration (name, value, date_add, date_upd)
             VALUES ("PS_MULTISHOP_FEATURE_ACTIVE", 1, NOW(), NOW())
             ON DUPLICATE KEY UPDATE value = 1'
        );
        Shop::resetStaticCache();

        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = 1;
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = 'ws quantity test shop';
        $shop->color = 'red';
        $shop->add();
        Shop::resetStaticCache();

        Db::getInstance()->execute(
            'INSERT IGNORE INTO ' . _DB_PREFIX_ . 'product_shop
                (id_product, id_shop, id_category_default, price, active, visibility, id_tax_rules_group, indexed, date_add, date_upd)
             SELECT id_product, ' . (int) $shop->id . ', id_category_default, price, active, visibility, id_tax_rules_group, indexed, date_add, date_upd
             FROM ' . _DB_PREFIX_ . 'product_shop WHERE id_product = ' . self::PRODUCT_ID . ' AND id_shop = 1'
        );

        return (int) $shop->id;
    }

    private function removeShop(int $idShop): void
    {
        Shop::setContext(Shop::CONTEXT_ALL);
        Db::getInstance()->delete('stock_available', 'id_shop = ' . $idShop);
        Db::getInstance()->delete('product_shop', 'id_shop = ' . $idShop);
        Db::getInstance()->delete('shop_url', 'id_shop = ' . $idShop);
        Db::getInstance()->delete('shop', 'id_shop = ' . $idShop);
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'configuration WHERE id_shop = ' . $idShop);
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'configuration WHERE name = "PS_MULTISHOP_FEATURE_ACTIVE"');
        Shop::resetStaticCache();
        Cache::clean('*');
    }

    /**
     * The getter reads the product-level row, the one the back office and the front office show.
     */
    public function testItReadsTheProductLevelStockRow(): void
    {
        StockAvailable::setQuantity(self::PRODUCT_ID, 0, 11);

        $stored = (int) Db::getInstance()->getValue(
            'SELECT quantity FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . self::PRODUCT_ID . ' AND id_product_attribute = 0',
            false
        );

        $this->assertSame($stored, (int) (new Product(self::PRODUCT_ID))->getWsQuantity());
    }
}
