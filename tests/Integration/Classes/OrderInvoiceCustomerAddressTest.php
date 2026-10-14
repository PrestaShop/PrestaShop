<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Classes;

use Address;
use Db;
use Order;
use OrderInvoice;
use PHPUnit\Framework\TestCase;

class OrderInvoiceCustomerAddressTest extends TestCase
{
    /** @var int */
    private $idOrderInvoice = 0;

    protected function tearDown(): void
    {
        if ($this->idOrderInvoice) {
            Db::getInstance()->delete('order_invoice', 'id_order_invoice = ' . (int) $this->idOrderInvoice);
            $this->idOrderInvoice = 0;
        }

        parent::tearDown();
    }

    public function testItFormatsTheAddressOfAnOrder(): void
    {
        $idAddress = (int) Db::getInstance()->getValue('SELECT id_address FROM ' . _DB_PREFIX_ . 'address WHERE deleted = 0');
        $address = new Address($idAddress);

        $formatted = OrderInvoice::getFormattedCustomerAddress($idAddress);

        $this->assertNotEmpty($formatted);
        $this->assertStringContainsString($address->address1, $formatted);
    }

    public function testItReturnsNullWhenTheAddressIsGone(): void
    {
        $this->assertNull(OrderInvoice::getFormattedCustomerAddress(0));
    }

    /**
     * The point of the change: an invoice keeps the address it was issued with, so editing the
     * customer address afterwards no longer rewrites a document already sent.
     */
    public function testAnIssuedInvoiceKeepsTheAddressItWasCreatedWith(): void
    {
        $idOrder = (int) Db::getInstance()->getValue('SELECT id_order FROM ' . _DB_PREFIX_ . 'orders');
        $order = new Order($idOrder);
        $address = new Address((int) $order->id_address_invoice);

        $originalCity = $address->city;

        $invoice = new OrderInvoice();
        $invoice->id_order = $idOrder;
        $invoice->number = 0;
        $invoice->add();
        $this->idOrderInvoice = (int) $invoice->id;

        $this->assertNotEmpty($invoice->customer_address, 'the invoice stores the address when it is created');
        $this->assertStringContainsString($originalCity, $invoice->customer_address);

        $address->city = $originalCity . ' Renamed';
        $address->update();

        try {
            $reloaded = new OrderInvoice($this->idOrderInvoice);

            $this->assertStringContainsString(
                $originalCity,
                $reloaded->customer_address,
                'the issued invoice must not follow a later edit of the address'
            );
            $this->assertStringNotContainsString('Renamed', $reloaded->customer_address);
        } finally {
            $address->city = $originalCity;
            $address->update();
        }
    }
}
