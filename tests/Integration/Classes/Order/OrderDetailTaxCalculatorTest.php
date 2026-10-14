<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Db;
use OrderDetail;
use PHPUnit\Framework\TestCase;

/**
 * `order_detail_tax` can hold an `id_tax` that resolves to nothing: a tax-free line stores 0, and a tax
 * deleted after the order was placed leaves its id behind. `getTaxCalculatorStatic()` handed those to
 * `TaxCalculator` as unloaded `Tax` objects whose `rate` is null, so `abs($tax->rate)` raised
 * "Passing null to parameter #1 ($num) of type int|float is deprecated" on PHP 8 - a TypeError on PHP 9 -
 * and `getTaxesAmount()` returned an entry keyed by the empty string, which
 * `Order::getProductTaxesDetails()` writes back as another `id_tax = 0` row.
 */
class OrderDetailTaxCalculatorTest extends TestCase
{
    /**
     * @var int
     */
    private $orderDetailId;

    /**
     * @var array<int, array<string, string>>
     */
    private $originalRows = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderDetailId = (int) Db::getInstance()->getValue(
            'SELECT id_order_detail FROM ' . _DB_PREFIX_ . 'order_detail ORDER BY id_order_detail'
        );
        if (!$this->orderDetailId) {
            $this->markTestSkipped('the fixture holds no order detail to attach a tax row to');
        }

        $this->originalRows = (array) Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'order_detail_tax WHERE id_order_detail = ' . $this->orderDetailId
        );
        $this->deleteRows();
    }

    protected function tearDown(): void
    {
        $this->deleteRows();
        foreach ($this->originalRows as $row) {
            $this->insertRow((int) $row['id_tax'], (float) $row['unit_amount'], (float) $row['total_amount']);
        }
        $this->originalRows = [];

        parent::tearDown();
    }

    public function testATaxThatDoesNotExistIsLeftOutOfTheCalculator(): void
    {
        $this->insertRow(0, 0.0, 0.0);

        $calculator = OrderDetail::getTaxCalculatorStatic($this->orderDetailId);

        $this->assertSame([], $calculator->taxes, 'an id_tax that loads nothing must not reach the calculator');
        $this->assertSame(0.0, (float) $calculator->getTotalRate());
        $this->assertSame(
            [],
            $calculator->getTaxesAmount(100.0),
            'a tax with no rate must not produce an amount keyed by the empty string'
        );
    }

    public function testAnExistingTaxStillReachesTheCalculator(): void
    {
        $idTax = (int) Db::getInstance()->getValue(
            'SELECT id_tax FROM ' . _DB_PREFIX_ . 'tax WHERE active = 1 AND rate > 0 ORDER BY id_tax'
        );
        if (!$idTax) {
            $this->markTestSkipped('the fixture holds no active tax with a rate');
        }
        $this->insertRow($idTax, 1.0, 1.0);

        $calculator = OrderDetail::getTaxCalculatorStatic($this->orderDetailId);

        $this->assertCount(1, $calculator->taxes);
        $this->assertGreaterThan(0, $calculator->getTotalRate(), 'the control must exercise a real rate');
        $this->assertArrayHasKey($idTax, $calculator->getTaxesAmount(100.0));
    }

    private function insertRow(int $idTax, float $unitAmount, float $totalAmount): void
    {
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'order_detail_tax (id_order_detail, id_tax, unit_amount, total_amount)'
            . ' VALUES (' . $this->orderDetailId . ', ' . $idTax . ', ' . $unitAmount . ', ' . $totalAmount . ')'
        );
    }

    private function deleteRows(): void
    {
        Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'order_detail_tax WHERE id_order_detail = ' . $this->orderDetailId
        );
    }
}
