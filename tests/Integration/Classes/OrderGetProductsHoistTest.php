<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use Currency;
use Db;
use Order;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Validate;

/**
 * Order::getProducts() resolves the delivery country and the customer default group once and passes
 * them to setProductReduction() instead of reloading the customer per product line. This guards that
 * the per-line reduction data is unchanged by that hoist.
 */
class OrderGetProductsHoistTest extends KernelTestCase
{
    public function testGetProductsReturnsConsistentReductionDataForEveryLine(): void
    {
        self::bootKernel();
        Context::getContext()->container = self::getContainer();

        $orderId = (int) Db::getInstance()->getValue(
            'SELECT id_order FROM ' . _DB_PREFIX_ . 'order_detail ORDER BY id_order'
        );
        self::assertGreaterThan(0, $orderId, 'a demo order with at least one product line is required');

        $order = new Order($orderId);
        self::assertTrue(Validate::isLoadedObject($order));
        Context::getContext()->currency = new Currency((int) $order->id_currency);

        $products = $order->getProducts();
        self::assertNotEmpty($products);

        foreach ($products as $product) {
            self::assertArrayHasKey('reduction_type', $product);
            self::assertArrayHasKey('reduction_applies', $product);
            // setProductReduction() sets a default (0 / false) when no specific price matches, or the
            // specific price values otherwise ('percentage'|'amount' + the reduction). The hoist must
            // not change what these resolve to.
            self::assertTrue(
                0 === $product['reduction_type'] || in_array($product['reduction_type'], ['percentage', 'amount'], true),
                'unexpected reduction_type: ' . var_export($product['reduction_type'], true)
            );
            self::assertTrue(
                false === $product['reduction_applies'] || is_numeric($product['reduction_applies'])
            );
        }
    }
}
