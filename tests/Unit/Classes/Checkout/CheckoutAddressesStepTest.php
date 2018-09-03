<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Classes\Checkout;

use CheckoutAddressesStep;
use CheckoutProcess;
use Context;
use Customer;
use Language;
use Link;
use Phake;
use Tests\TestCase\UnitTestCase;

class CheckoutAddressesStepTest extends UnitTestCase
{
    private $step;
    private $session;

    public function setUp()
    {
        parent::setUp();
        $context = new Context;
        $context->language = new Language;
        $context->customer = new Customer;
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
