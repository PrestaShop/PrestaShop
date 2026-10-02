<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Db;
use DbPDO;
use PHPUnit\Framework\TestCase;
use Product;

/**
 * Pricing a product reads how many of it the cart holds. That total is the same whatever quantity is
 * being priced, so it is looked up once per cart and product; a cart holding several combinations of
 * one product used to repeat the query for every row.
 */
class ProductCartQuantityCacheTest extends TestCase
{
    private int $cartQuantityQueries = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $realDb = Db::getInstance();
        $spy = $this->getMockBuilder(DbPDO::class)
            ->setConstructorArgs([_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_, true])
            ->onlyMethods(['getValue'])
            ->getMock();

        $spy->method('getValue')->willReturnCallback(
            function ($sql, $useCache = true) use ($realDb) {
                if (str_contains((string) $sql, 'cart_product')) {
                    ++$this->cartQuantityQueries;
                }

                return $realDb->getValue($sql, $useCache);
            }
        );

        Db::setInstanceForTesting($spy);
        Cache::clean('Product::getPriceStatic_*');
    }

    protected function tearDown(): void
    {
        Db::deleteTestingInstance();
        Cache::clean('Product::getPriceStatic_*');

        parent::tearDown();
    }

    public function testItReadsTheCartQuantityOncePerProductAndCart(): void
    {
        $idCart = (int) Db::getInstance()->getValue('SELECT id_cart FROM ' . _DB_PREFIX_ . 'cart ORDER BY id_cart DESC');
        $idProduct = (int) Db::getInstance()->getValue('SELECT id_product FROM ' . _DB_PREFIX_ . 'product');
        $this->cartQuantityQueries = 0;

        // Several quantities, as a cart holding several combinations of one product produces.
        foreach ([1, 2, 3, 4, 5] as $quantity) {
            Product::getPriceStatic($idProduct, true, null, 6, null, false, true, $quantity, false, null, $idCart, 0);
        }

        $this->assertSame(1, $this->cartQuantityQueries);
    }
}
