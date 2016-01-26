<?php
namespace PrestaShop\PrestaShop\tests\Unit\classes\checkout;

use CheckoutAddressesStep;
use CheckoutProcess;
use Context;
use Customer;
use Language;
use Phake;
use PrestaShop\PrestaShop\tests\TestCase\UnitTestCase;

class CheckoutAddressesStepTest extends UnitTestCase
{
    private $step;
    private $session;

    public function setup()
    {
        parent::setup();
        $context = new Context;
        $context->language = new Language;
        $context->customer = new Customer;

        $smarty = Phake::mock('Smarty');
        $translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $addressForm = Phake::mock('CustomerAddressForm');

        $this->session = Phake::mock('CheckoutSession');

        Phake::when($this->session)->getCustomer()->thenReturn($context->customer);

        $process = new CheckoutProcess(
            $smarty,
            $this->session
        );

        $this->step = new CheckoutAddressesStep(
            $smarty,
            $translator,
            $addressForm
        );

        $this->step->setCheckoutProcess($process);
    }

    private function setCustomerAddressesCount($n)
    {
        Phake::when($this->session)->getCustomerAddressesCount()->thenReturn($n);
        return $this;
    }

    private function assertTemplateParametersInclude(array $what, array $requestParams = [])
    {
        $this->assertArraySubset(
            $what,
            $this->step->handleRequest($requestParams)->getTemplateParameters()
        );
    }

    public function test_if_customer_has_no_addresses_then_delivery_address_form_is_open()
    {
        $this->setCustomerAddressesCount(0);
        $this->assertTemplateParametersInclude([
            'show_delivery_address_form' => true
        ]);
    }

    public function test_if_customer_has_one_address_then_delivery_address_form_is_not_open()
    {
        $this->setCustomerAddressesCount(1);
        $this->assertTemplateParametersInclude([
            'show_delivery_address_form' => false
        ]);
    }

    public function test_if_customer_has_one_address_and_wants_different_invoice_then_invoice_open()
    {
        $this->setCustomerAddressesCount(1);
        $this->assertTemplateParametersInclude([
            'show_invoice_address_form' => true
        ], [
            'use_same_address' => false
        ]);
    }

    public function test_when_customer_has_one_delivery_address_and_edits_it_then_is_open()
    {
        $this->setCustomerAddressesCount(1);
        $this->assertTemplateParametersInclude([
            'show_delivery_address_form' => true,
            'form_has_continue_button'   => true
        ], [
            'editAddress'   => 'delivery',
            'id_address'    => null
        ]);
    }
}
