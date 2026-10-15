<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use OrderDetail;
use PHPUnit\Framework\TestCase;
use Product;
use Tests\Integration\Utility\ContextMockerTrait;

class OrderDetailTaxRulesGroupTest extends TestCase
{
    use ContextMockerTrait;

    private const PRODUCT_ID = 1;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
    }

    /**
     * An order is a financial record: once a line has been sold under a tax rules group, moving the
     * product to another group must not change how that line is taxed when the order is recalculated
     * (which is what editing an address in the back office does).
     */
    public function testItKeepsTheGroupTheLineWasSoldUnderWhenTheProductMovedToAnother(): void
    {
        $currentProductGroupId = (int) Product::getIdTaxRulesGroupByIdProduct(self::PRODUCT_ID);
        $groupSoldUnder = $currentProductGroupId + 1;

        $orderDetail = new OrderDetail();
        $orderDetail->product_id = self::PRODUCT_ID;
        $orderDetail->id_tax_rules_group = $groupSoldUnder;

        $this->assertSame($groupSoldUnder, $orderDetail->getTaxRulesGroupId());
    }

    /**
     * Lines that carry no group - imported orders, and orders created before the column was written
     * - have nothing historical to honour, so they still resolve the product's current group.
     */
    public function testItFallsBackToTheProductWhenTheLineCarriesNoGroup(): void
    {
        $currentProductGroupId = (int) Product::getIdTaxRulesGroupByIdProduct(self::PRODUCT_ID);
        $this->assertGreaterThan(
            0,
            $currentProductGroupId,
            'the fixture product must belong to a tax rules group for this test to mean anything'
        );

        $orderDetail = new OrderDetail();
        $orderDetail->product_id = self::PRODUCT_ID;
        $orderDetail->id_tax_rules_group = 0;

        $this->assertSame($currentProductGroupId, $orderDetail->getTaxRulesGroupId());
    }
}
