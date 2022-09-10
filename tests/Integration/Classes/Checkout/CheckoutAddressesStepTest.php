<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
