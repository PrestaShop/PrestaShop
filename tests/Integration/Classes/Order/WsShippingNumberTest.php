<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Carrier;
use Configuration;
use Context;
use Country;
use Currency;
use Db;
use Language;
use Mail;
use Order;
use PHPUnit\Framework\TestCase;

/**
 * Writing a tracking number through the orders resource has to behave as writing it through the
 * order_carriers resource, which the webservice routes to OrderCarrier::updateWs().
 *
 * The mail itself cannot be observed - neither suite has a mail capture - so the assertion is on the
 * return value: sending is asked for and fails, which OrderCarrier::updateWs() reports. Shipped 9.2.x
 * answers true because it never enters that path at all.
 */
class WsShippingNumberTest extends TestCase
{
    private const CARRIER_URL = 'https://tracking.example.test/@';

    private int $idOrder = 0;

    private int $idOrderCarrier = 0;

    private string $trackingNumber = '';

    private ?Carrier $carrier = null;

    private string $carrierUrl = '';

    /** @var array<string, string> */
    private array $configuration = [];

    protected function setUp(): void
    {
        parent::setUp();

        $row = Db::getInstance()->getRow(
            'SELECT oc.id_order_carrier, oc.id_order, oc.tracking_number, o.id_carrier
             FROM ' . _DB_PREFIX_ . 'order_carrier oc
             INNER JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_order = oc.id_order
             INNER JOIN ' . _DB_PREFIX_ . 'customer c ON c.id_customer = o.id_customer
             INNER JOIN ' . _DB_PREFIX_ . 'address a ON a.id_address = o.id_address_delivery
             INNER JOIN ' . _DB_PREFIX_ . 'carrier ca ON ca.id_carrier = o.id_carrier
             ORDER BY oc.id_order_carrier ASC'
        );

        if (empty($row)) {
            $this->markTestSkipped('No order with a carrier, a customer and a delivery address.');
        }

        $this->idOrder = (int) $row['id_order'];
        $this->idOrderCarrier = (int) $row['id_order_carrier'];
        $this->trackingNumber = (string) $row['tracking_number'];

        // sendInTransitEmail() returns early when the carrier has no follow-up url.
        $this->carrier = new Carrier((int) $row['id_carrier']);
        $this->carrierUrl = (string) $this->carrier->url;
        $this->carrier->url = self::CARRIER_URL;
        $this->carrier->update();

        foreach (['PS_MAIL_METHOD', 'PS_MAIL_SERVER'] as $key) {
            $this->configuration[$key] = (string) Configuration::get($key);
        }
        Configuration::updateValue('PS_MAIL_METHOD', Mail::METHOD_SMTP);
        Configuration::updateValue('PS_MAIL_SERVER', 'smtp.invalid.test');

        $context = Context::getContext();
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
    }

    protected function tearDown(): void
    {
        unset($_GET['sendemail']);

        if ($this->carrier !== null) {
            $this->carrier->url = $this->carrierUrl;
            $this->carrier->update();
        }
        foreach ($this->configuration as $key => $value) {
            Configuration::updateValue($key, $value);
        }
        if ($this->idOrderCarrier) {
            Db::getInstance()->update(
                'order_carrier',
                ['tracking_number' => pSQL($this->trackingNumber)],
                'id_order_carrier = ' . $this->idOrderCarrier
            );
        }

        parent::tearDown();
    }

    public function testAskingForTheMailReachesIt(): void
    {
        $_GET['sendemail'] = 1;

        $order = new Order($this->idOrder);

        $this->assertFalse($order->setWsShippingNumber('TRACKING-13912'));
        $this->assertSame('TRACKING-13912', $this->storedTrackingNumber());
    }

    /**
     * The mail stays opt-in: an integration that does not ask for it must be unaffected.
     */
    public function testNotAskingForTheMailWritesTheNumberAndNothingElse(): void
    {
        $order = new Order($this->idOrder);

        $this->assertTrue($order->setWsShippingNumber('TRACKING-13912-QUIET'));
        $this->assertSame('TRACKING-13912-QUIET', $this->storedTrackingNumber());
    }

    private function storedTrackingNumber(): string
    {
        return (string) Db::getInstance()->getValue(
            'SELECT tracking_number FROM ' . _DB_PREFIX_ . 'order_carrier
             WHERE id_order_carrier = ' . $this->idOrderCarrier
        );
    }
}
