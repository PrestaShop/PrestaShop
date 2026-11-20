<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cart;
use Context;
use Db;
use Product;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class ProductGetQuantityTest extends KernelTestCase
{
    private const PRODUCT_ID = 1;
    private const ORPHAN_QUANTITY = 7;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['cart_product']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['cart_product']);
    }

    protected function setUp(): void
    {
        self::bootKernel();
        Context::getContext()->container = self::getContainer();
    }

    /**
     * Product::getQuantity() must not subtract "quantity in cart" for an in-memory cart
     * that has not been persisted yet (id = 0). The SQL filter `WHERE id_cart = 0` would
     * otherwise sum every orphan ps_cart_product row, inflating the subtracted amount and
     * returning a stock figure below the real StockAvailable value.
     */
    public function testGetQuantityIgnoresAnUnsavedCart(): void
    {
        $stock = (int) StockAvailable::getQuantityAvailableByProduct(self::PRODUCT_ID, 0);

        // An orphan cart line attached to the non-persisted cart id 0.
        Db::getInstance()->insert('cart_product', [
            'id_cart' => 0,
            'id_product' => self::PRODUCT_ID,
            'id_address_delivery' => 0,
            'id_shop' => (int) Context::getContext()->shop->id,
            'id_product_attribute' => 0,
            'id_customization' => 0,
            'quantity' => self::ORPHAN_QUANTITY,
            'date_add' => date('Y-m-d H:i:s'),
        ]);

        $unsavedCart = new Cart();

        $quantity = Product::getQuantity(self::PRODUCT_ID, 0, null, $unsavedCart);

        $this->assertSame($stock, $quantity);
    }
}
