<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Stats;

use AdminStatsController;
use Configuration;
use Db;
use PHPUnit\Framework\TestCase;

class AdminStatsControllerPurchasesTest extends TestCase
{
    private const PRODUCT_PRICE = 100.0;
    private const QUANTITY = 2;

    /** @var int|null */
    private $orderId;

    /** @var string|null */
    private $originalMargin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalMargin = Configuration::get('CONF_AVERAGE_PRODUCT_MARGIN');
        $this->createLogableOrderWithoutWholesalePrice();
    }

    protected function tearDown(): void
    {
        if (null !== $this->orderId) {
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'order_detail` WHERE id_order = ' . (int) $this->orderId);
            Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'orders` WHERE id_order = ' . (int) $this->orderId);
        }
        if (null !== $this->originalMargin) {
            Configuration::updateValue('CONF_AVERAGE_PRODUCT_MARGIN', $this->originalMargin);
        }
        parent::tearDown();
    }

    /**
     * The configured percentage is the average GROSS MARGIN: its own help text defines it as
     * ((revenue) - (cost of goods sold)) / (revenue) * 100. The estimated cost of goods sold is
     * therefore the complement of it, which the two extremes pin down exactly.
     *
     * @dataProvider provideMarginsAndExpectedCost
     */
    public function testEstimatedCostOfGoodsSoldIsTheComplementOfTheGrossMargin(int $margin, float $expectedCost): void
    {
        Configuration::updateValue('CONF_AVERAGE_PRODUCT_MARGIN', $margin);

        $purchases = (float) AdminStatsController::getPurchases($this->dateFrom(), $this->dateTo());

        $this->assertEqualsWithDelta($expectedCost, $purchases, 0.01);
    }

    public static function provideMarginsAndExpectedCost(): array
    {
        $revenue = self::PRODUCT_PRICE * self::QUANTITY;

        return [
            'no margin at all means the goods cost the whole sale price' => [0, $revenue],
            'a quarter of the sale price is margin' => [25, $revenue * 0.75],
            'a 40 percent margin leaves 60 percent of cost' => [40, $revenue * 0.60],
            'everything is margin, so the goods cost nothing' => [100, 0.0],
        ];
    }

    public function testAWholesalePriceIsUsedInsteadOfTheEstimate(): void
    {
        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'order_detail` SET purchase_supplier_price = 10 WHERE id_order = ' . (int) $this->orderId
        );
        Configuration::updateValue('CONF_AVERAGE_PRODUCT_MARGIN', 40);

        $purchases = (float) AdminStatsController::getPurchases($this->dateFrom(), $this->dateTo());

        // 2 units at a recorded wholesale price of 10, the configured margin is not consulted
        $this->assertEqualsWithDelta(20.0, $purchases, 0.01);
    }

    private function dateFrom(): string
    {
        return date('Y-m-d', strtotime('-10 day'));
    }

    private function dateTo(): string
    {
        return date('Y-m-d', strtotime('+1 day'));
    }

    private function createLogableOrderWithoutWholesalePrice(): void
    {
        $db = Db::getInstance();
        $logableState = (int) $db->getValue('SELECT id_order_state FROM `' . _DB_PREFIX_ . 'order_state` WHERE logable = 1');
        $invoiceDate = date('Y-m-d H:i:s', strtotime('-1 day'));

        $db->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'orders`
             (id_address_delivery, id_address_invoice, id_cart, id_currency, id_lang, id_customer, id_carrier,
              current_state, conversion_rate, payment, module, total_paid, total_paid_real, total_products,
              total_products_wt, total_paid_tax_excl, total_paid_tax_incl, total_shipping_tax_excl,
              invoice_date, date_add, date_upd, id_shop, id_shop_group, reference, secure_key)
             VALUES (1, 1, 1, 1, 1, 1, 1, ' . $logableState . ', 1, "test", "test", 0, 0, 0, 0, 0, 0, 0,
              "' . pSQL($invoiceDate) . '", NOW(), NOW(), 1, 1, "TESTMARGIN", "")'
        );
        $this->orderId = (int) $db->Insert_ID();

        $db->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'order_detail`
             (id_order, id_shop, product_id, product_quantity, original_product_price, purchase_supplier_price, product_name)
             VALUES (' . (int) $this->orderId . ', 1, 1, ' . self::QUANTITY . ', ' . self::PRODUCT_PRICE . ', 0, "test product")'
        );
    }
}
