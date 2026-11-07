<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Cache;
use Configuration;
use Context;
use Currency;
use Db;
use Order;
use OrderSlip;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

class OrderSlipShippingTaxTest extends TestCase
{
    private const ORDER_ID = 1;

    private ?Currency $currencyBackup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyBackup = Context::getContext()->currency;
        Context::getContext()->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        // A rate the carrier and the delivery address do not resolve to
        Db::getInstance()->update('orders', ['carrier_tax_rate' => 10], 'id_order = ' . self::ORDER_ID);
        Cache::clean('objectmodel_Order_*');
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['orders', 'order_slip', 'order_slip_detail']);
        Cache::clean('objectmodel_Order_*');
        Context::getContext()->currency = $this->currencyBackup;
        parent::tearDown();
    }

    public function testTheShippingIsRefundedAtTheRateOfTheOrder(): void
    {
        $this->assertTrue(OrderSlip::create(new Order(self::ORDER_ID), [], 7.0));

        $this->assertSame(['7.000000', '7.700000'], $this->getLastSlipShipping());
    }

    public function testTheShippingTaxIsRemovedAtTheRateOfTheOrder(): void
    {
        $this->assertTrue(OrderSlip::create(new Order(self::ORDER_ID), [], 7.7, 0, false, false));

        $this->assertSame(['7.000000', '7.700000'], $this->getLastSlipShipping());
    }

    /**
     * @return array{0: string, 1: string} shipping tax excluded and tax included
     */
    private function getLastSlipShipping(): array
    {
        $row = Db::getInstance()->getRow(
            'SELECT total_shipping_tax_excl, total_shipping_tax_incl FROM ' . _DB_PREFIX_ . 'order_slip
             WHERE id_order = ' . self::ORDER_ID . ' ORDER BY id_order_slip DESC'
        );

        return [(string) $row['total_shipping_tax_excl'], (string) $row['total_shipping_tax_incl']];
    }
}
