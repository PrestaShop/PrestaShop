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
use Currency;
use Order;
use OrderCarrier;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;
use Validate;

/**
 * OrderCarrier::updateWs() is reachable only through the webservice ($definition
 * objectMethods). It commits the row with parent::update() before sending the "in transit"
 * notification, so a failed notification must not be reported back as a failed save:
 * WebserviceRequest turns a false return into "Unable to save resource" (error 46).
 */
class OrderCarrierUpdateWsTest extends TestCase
{
    private const ORDER_CARRIER_ID = 1;

    /**
     * @var array<string, mixed>
     */
    private $mailConfigurationBackup = [];

    /**
     * @var string
     */
    private $carrierUrlBackup = '';

    /**
     * @var int
     */
    private $carrierId = 0;

    /**
     * @var Currency|null
     */
    private $contextCurrencyBackup;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['order_carrier', 'carrier']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['order_carrier', 'carrier']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['PS_MAIL_METHOD', 'PS_MAIL_SERVER', 'PS_MAIL_SMTP_PORT', 'PS_MAIL_SMTP_ENCRYPTION'] as $key) {
            $this->mailConfigurationBackup[$key] = Configuration::get($key);
        }

        // WHY: the send has to fail in Mail::send()'s transport catch. sendInTransitEmail()
        // calls Mail::send() with $die = true, so any failure routed through Tools::dieOrLog()
        // would terminate the process instead of returning false.
        Configuration::updateValue('PS_MAIL_METHOD', 2);
        Configuration::updateValue('PS_MAIL_SERVER', 'smtp.invalid.test');
        Configuration::updateValue('PS_MAIL_SMTP_PORT', 25);
        Configuration::updateValue('PS_MAIL_SMTP_ENCRYPTION', 'off');

        // WHY: sendInTransitEmail() walks the order products, which needs the computing
        // precision of a loaded context currency. This bare TestCase does not boot a kernel.
        $context = Context::getContext();
        $this->contextCurrencyBackup = $context->currency;
        if (!Validate::isLoadedObject($context->currency)) {
            $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        }

        $order = new Order((int) (new OrderCarrier(self::ORDER_CARRIER_ID))->id_order);
        $this->carrierId = (int) $order->id_carrier;
        $carrier = new Carrier($this->carrierId);
        $this->carrierUrlBackup = (string) $carrier->url;

        // WHY: sendInTransitEmail() returns true without sending anything when the carrier has
        // no tracking url, which would make every assertion below vacuous.
        $carrier->url = 'http://tracking.invalid.test/@';
        $carrier->save();
    }

    protected function tearDown(): void
    {
        unset($_GET['sendemail'], $_POST['sendemail']);

        $carrier = new Carrier($this->carrierId);
        $carrier->url = $this->carrierUrlBackup;
        $carrier->save();

        foreach ($this->mailConfigurationBackup as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        Context::getContext()->currency = $this->contextCurrencyBackup;

        parent::tearDown();
    }

    /**
     * Control: without this, a passing updateWs() test would prove nothing, because the
     * notification branch would never be entered.
     */
    public function testTheNotificationFailsWithThisFixture(): void
    {
        $orderCarrier = new OrderCarrier(self::ORDER_CARRIER_ID);
        $order = new Order((int) $orderCarrier->id_order);

        $this->assertFalse($orderCarrier->sendInTransitEmail($order));
    }

    public function testUpdateWsReportsSuccessWhenTheNotificationFails(): void
    {
        $_GET['sendemail'] = 1;

        $orderCarrier = new OrderCarrier(self::ORDER_CARRIER_ID);
        $orderCarrier->tracking_number = 'TRACK-37249';

        $this->assertTrue($orderCarrier->updateWs());

        $persisted = new OrderCarrier(self::ORDER_CARRIER_ID);
        $this->assertSame('TRACK-37249', $persisted->tracking_number);
    }
}
