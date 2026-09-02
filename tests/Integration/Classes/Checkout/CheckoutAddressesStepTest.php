<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Checkout;

use CheckoutAddressesStep;
use CheckoutProcess;
use CheckoutSession;
use Context;
use Customer;
use CustomerAddressForm;
use Language;
use Link;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class CheckoutAddressesStepTest extends TestCase
{
    /**
     * @var CheckoutSession|MockObject
     */
    private $session;
    /**
     * @var CheckoutAddressesStep
     */
    private $step;

    protected function setUp(): void
    {
        parent::setUp();

        $context = $this->createMock(Context::class);
        $context->language = $this->createMock(Language::class);
        $context->customer = $this->createMock(Customer::class);
        $context->link = $this->createMock(Link::class);
        $context->link->method('getPageLink')->withAnyParameters()->willReturn('http://addresses-actions.url');

        $this->session = $this->createMock(CheckoutSession::class);
        $this->session->method('getCustomer')->willReturn($context->customer);

        $process = new CheckoutProcess($context, $this->session);

        $this->step = new CheckoutAddressesStep(
            $context,
            $this->createMock(TranslatorInterface::class),
            $this->createMock(CustomerAddressForm::class)
        );
        $this->step->setCheckoutProcess($process);
    }

    public function testIfCustomerHasNoAddressesThenDeliveryAddressFormIsOpen(): void
    {
        $this->session->method('getCustomerAddressesCount')->willReturn(0);
        $array = $this->step->handleRequest([])->getTemplateParameters();
        $this->assertArrayHasKey(
            'show_delivery_address_form',
            $array
        );
        $this->assertEquals(true, $array['show_delivery_address_form']);
    }

    public function testIfCustomerHasOneAddressThenDeliveryAddressFormIsNotOpen(): void
    {
        $this->session->method('getCustomerAddressesCount')->willReturn(1);
        $array = $this->step->handleRequest([])->getTemplateParameters();
        $this->assertArrayHasKey(
            'show_delivery_address_form',
            $array
        );
        $this->assertEquals(false, $array['show_delivery_address_form']);
    }

    public function testIfCustomerHasOneAddressAndWantsDifferentInvoiceThenInvoiceOpen(): void
    {
        $this->session->method('getCustomerAddressesCount')->willReturn(1);
        $array = $this->step->handleRequest(['use_same_address' => false])->getTemplateParameters();
        $this->assertArrayHasKey(
            'show_invoice_address_form',
            $array
        );
        $this->assertEquals(false, $array['show_delivery_address_form']);
    }

    public function testWhenCustomerHasOneDeliveryAddressAndEditsItThenIsOpen(): void
    {
        $this->session->method('getCustomerAddressesCount')->willReturn(1);

        $subset = [
            'show_delivery_address_form' => true,
            'form_has_continue_button' => true,
        ];
        $array = $this->step->handleRequest([
            'editAddress' => 'delivery',
            'id_address' => null,
        ])->getTemplateParameters();

        foreach ($subset as $key => $value) {
            $this->assertArrayHasKey($key, $array);
            $this->assertEquals($value, $array[$key]);
        }
    }
}
