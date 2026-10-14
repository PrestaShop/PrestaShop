<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\CommandHandler;

use Db;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderInvoiceException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;
use Throwable;

/**
 * Two order lines of the same product and combination are still distinct when they carry different
 * customizations. Moving one onto the invoice that holds the other used to be refused as a duplicate.
 */
class UpdateProductInOrderDuplicateTest extends KernelTestCase
{
    private const CUSTOMIZATION_ID = 777;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['order_detail', 'order_invoice']);

        parent::tearDown();
    }

    public function testALineIsNotADuplicateOfOneWithAnotherCustomization(): void
    {
        $prefix = _DB_PREFIX_;
        $orderId = (int) Db::getInstance()->getValue(
            "SELECT id_order FROM {$prefix}orders o WHERE EXISTS (SELECT 1 FROM {$prefix}order_detail d WHERE d.id_order = o.id_order) ORDER BY id_order DESC"
        );
        $this->assertGreaterThan(0, $orderId, 'the fixture has no order with products');

        $detail = Db::getInstance()->getRow("SELECT * FROM {$prefix}order_detail WHERE id_order = $orderId");
        $firstInvoiceId = $this->insertInvoice($orderId, 9001);
        $secondInvoiceId = $this->insertInvoice($orderId, 9002);

        $lineId = (int) $detail['id_order_detail'];
        Db::getInstance()->execute("UPDATE {$prefix}order_detail SET id_order_invoice = $firstInvoiceId, id_customization = 0 WHERE id_order_detail = $lineId");
        $this->insertSiblingLine($detail, $secondInvoiceId);

        // The command may still fail further down on a fixture whose cart does not carry this line;
        // what this test pins is that it is no longer refused by the duplicate guard.
        try {
            self::getContainer()->get('prestashop.core.command_bus')->handle(new UpdateProductInOrderCommand(
                $orderId,
                $lineId,
                (string) $detail['unit_price_tax_incl'],
                (string) $detail['unit_price_tax_excl'],
                (int) $detail['product_quantity'],
                $secondInvoiceId
            ));
        } catch (DuplicateProductInOrderInvoiceException $e) {
            $this->fail('the line was refused as a duplicate of a line with another customization');
        } catch (Throwable $e) {
            // Anything else is outside what this test covers.
        }

        $this->addToAssertionCount(1);
    }

    private function insertInvoice(int $orderId, int $number): int
    {
        $prefix = _DB_PREFIX_;
        Db::getInstance()->execute(
            "INSERT INTO {$prefix}order_invoice
                (id_order, number, delivery_number, total_discount_tax_excl, total_discount_tax_incl, total_paid_tax_excl,
                 total_paid_tax_incl, total_products, total_products_wt, total_shipping_tax_excl, total_shipping_tax_incl,
                 shipping_tax_computation_method, total_wrapping_tax_excl, total_wrapping_tax_incl, shop_address, note, date_add)
             VALUES ($orderId, $number, 0, 0, 0, 10, 10, 10, 10, 0, 0, 0, 0, 0, '', '', NOW())"
        );

        return (int) Db::getInstance()->Insert_ID();
    }

    /**
     * @param array<string, mixed> $detail
     */
    private function insertSiblingLine(array $detail, int $invoiceId): void
    {
        unset($detail['id_order_detail']);
        $detail['id_order_invoice'] = $invoiceId;
        $detail['id_customization'] = self::CUSTOMIZATION_ID;

        $columns = implode(',', array_map(static fn ($column) => '`' . $column . '`', array_keys($detail)));
        $values = implode(',', array_map(static fn ($value) => "'" . pSQL((string) $value) . "'", $detail));

        Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . "order_detail ($columns) VALUES ($values)");
    }
}
