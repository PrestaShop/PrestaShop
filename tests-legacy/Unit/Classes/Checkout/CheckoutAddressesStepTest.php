<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Classes\Checkout;

use CheckoutAddressesStep;
use CheckoutProcess;
use Context;
use Customer;
use Language;
use LegacyTests\TestCase\UnitTestCase;
use Phake;

class CheckoutAddressesStepTest extends UnitTestCase
{
    private $step;
    private $session;

    protected function setUp()
    {
        parent::setUp();
        $context = new Context();
        $context->language = new Language();
        $context->customer = new Customer();
        $context->link = Phake::mock('Link');
        Phake::when($context->link)->getPageLink(Phake::anyParameters())->thenReturn('http://addresses-actions.url');

        $smarty = Phake::mock('Smarty');
        $translator = Phake::mock('Symfony\Component\Translation\TranslatorInterface');
        $addressForm = Phake::mock('CustomerAddressForm');

        $this->session = Phake::mock('CheckoutSession');

        Phake::when($this->session)->getCustomer()->thenReturn($context->customer);

        $process = new CheckoutProcess(
            $context,
            $this->session
        );

        $this->step = new CheckoutAddressesStep(
            $context,
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

    public function testIfCustomerHasNoAddressesThenDeliveryAddressFormIsOpen()
    {
        $this->setCustomerAddressesCount(0);
        $this->assertTemplateParametersInclude([
            'show_delivery_address_form' => true,
        ]);
    }

    public function testIfCustomerHasOneAddressThenDeliveryAddressFormIsNotOpen()
    {
        $this->setCustomerAddressesCount(1);
        $this->assertTemplateParametersInclude([
            'show_delivery_address_form' => false,
        ]);
    }

    public function testIfCustomerHasOneAddressAndWantsDifferentInvoiceThenInvoiceOpen()
    {
        $this->setCustomerAddressesCount(1);
        $this->assertTemplateParametersInclude([
            'show_invoice_address_form' => true,
        ], [
            'use_same_address' => false,
        ]);
    }

    public function testWhenCustomerHasOneDeliveryAddressAndEditsItThenIsOpen()
    {
        $this->setCustomerAddressesCount(1);
        $this->assertTemplateParametersInclude([
            'show_delivery_address_form' => true,
            'form_has_continue_button'   => true,
        ], [
            'editAddress'   => 'delivery',
            'id_address'    => null,
        ]);
    }
}
