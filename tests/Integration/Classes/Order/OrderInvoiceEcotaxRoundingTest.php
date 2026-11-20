<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Cache;
use Db;
use Order;
use OrderInvoice;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\SymfonyIntegrationTestCase;

final class OrderInvoiceEcotaxRoundingTest extends SymfonyIntegrationTestCase
{
    private const ORDER_ID = 1;

    protected function tearDown(): void
    {
        try {
            DatabaseDump::restoreTables(['orders']);
            Cache::clean('objectmodel_Order_*');
        } finally {
            parent::tearDown();
        }
    }

    /**
     * @dataProvider getEcotaxRoundingCases
     */
    public function testEcotaxIsRoundedPerItemOnlyWhenOrderRoundsPerItem(
        int $roundMode,
        int $roundType,
        float $ecotax,
        float $expectedTotalEcotaxTaxIncl,
    ): void {
        Db::getInstance()->update(
            'orders',
            ['round_mode' => $roundMode, 'round_type' => $roundType],
            'id_order = ' . self::ORDER_ID
        );
        Cache::clean('objectmodel_Order_*');

        $invoice = new OrderInvoice();
        $invoice->id_order = self::ORDER_ID;

        $row = $invoice->getProductsDetail()[0];
        $row['od_ecotax'] = $ecotax;
        $row['od_ecotax_tax_rate'] = 20;
        $row['product_quantity'] = 3;

        $products = $invoice->getProducts([$row]);

        self::assertSame($expectedTotalEcotaxTaxIncl, current($products)['total_ecotax_tax_incl']);
    }

    /**
     * @return iterable<string, array{int, int, float, float}>
     */
    public static function getEcotaxRoundingCases(): iterable
    {
        // 0.835 + 20% = 1.002 per unit
        yield 'half up, round per item' => [PS_ROUND_HALF_UP, Order::ROUND_ITEM, 0.835, 3.0];
        yield 'half up, round per line' => [PS_ROUND_HALF_UP, Order::ROUND_LINE, 0.835, 3.01];
        // 0.84 + 20% = 1.008 per unit
        yield 'down, round per line' => [PS_ROUND_DOWN, Order::ROUND_LINE, 0.84, 3.02];
        yield 'down, round per item' => [PS_ROUND_DOWN, Order::ROUND_ITEM, 0.84, 3.0];
    }
}
