<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\TradeCredit;

use Configuration;
use Context;
use Customer;
use PrestaShop\PrestaShop\Adapter\TradeCredit\TradeCreditOrderService;

/**
 * Trade credit information and restriction added to the payment options presented in checkout.
 */
class TradeCreditPaymentOptionsTest extends AbstractTradeCreditOrderTestCase
{
    private TradeCreditOrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(TradeCreditOrderService::class);
    }

    public function testTradeCreditOptionShowsAvailableCredit(): void
    {
        $options = $this->decorate($this->createCart(), new Customer(self::CUSTOMER_ID));

        $option = $options[self::PAYMENT_MODULE][0];
        $this->assertStringStartsWith('Pay by bank wire, available trade credit: ', $option['call_to_action_text']);
        $this->assertStringContainsString('50,000.00', $option['call_to_action_text']);
        $this->assertTrue($option['trade_credit']['sufficient']);
        $this->assertNull($option['trade_credit']['message']);
        $this->assertStringContainsString('<span class="js-trade-credit-restriction" hidden data-sufficient="1" data-message=""></span>', $option['additionalInformation']);
    }

    public function testRestrictionMarkerKeepsModuleInformationAndEscapesMessage(): void
    {
        $this->repository->createBalance(self::CUSTOMER_ID, '10.00');

        $options = $this->decorate($this->createCart(), new Customer(self::CUSTOMER_ID), '<p>Module info</p>');

        $additionalInformation = $options[self::PAYMENT_MODULE][0]['additionalInformation'];
        $this->assertStringStartsWith('<p>Module info</p><span class="js-trade-credit-restriction" hidden data-sufficient="0" data-message="', $additionalInformation);
        $this->assertStringContainsString('exceeds your available trade credit', $additionalInformation);
        $this->assertStringNotContainsString('"<', substr($additionalInformation, strlen('<p>Module info</p>') + 1));
    }

    public function testInsufficientCreditMarksOptionDisabled(): void
    {
        $this->repository->createBalance(self::CUSTOMER_ID, '10.00');

        $options = $this->decorate($this->createCart(), new Customer(self::CUSTOMER_ID));

        $tradeCredit = $options[self::PAYMENT_MODULE][0]['trade_credit'];
        $this->assertFalse($tradeCredit['sufficient']);
        $this->assertStringContainsString('exceeds your available trade credit', $tradeCredit['message']);
        $this->assertStringContainsString('10.00', $tradeCredit['message']);
        $this->assertStringContainsString('data-sufficient="0"', $options[self::PAYMENT_MODULE][0]['additionalInformation']);
    }

    public function testGuestGetsGuestMessage(): void
    {
        $cart = $this->createCart();
        $guest = new Customer(self::CUSTOMER_ID);
        $guest->is_guest = true;

        $options = $this->decorate($cart, $guest);

        $tradeCredit = $options[self::PAYMENT_MODULE][0]['trade_credit'];
        $this->assertFalse($tradeCredit['sufficient']);
        $this->assertSame('Trade credit is available only to registered customers.', $tradeCredit['message']);
    }

    public function testMissingCreditCurrencyDisablesOptionWithoutBreakingCheckout(): void
    {
        Configuration::updateValue('PS_TRADE_CREDIT_CURRENCY', 'XXX');

        $options = $this->decorate($this->createCart(), new Customer(self::CUSTOMER_ID));

        $option = $options[self::PAYMENT_MODULE][0];
        $this->assertSame('Pay by bank wire', $option['call_to_action_text']);
        $this->assertFalse($option['trade_credit']['sufficient']);
        $this->assertSame('Trade credit is currently unavailable.', $option['trade_credit']['message']);
    }

    public function testNonTradeCreditOptionUntouched(): void
    {
        $options = $this->decorate($this->createCart(), new Customer(self::CUSTOMER_ID));

        $this->assertSame($this->otherOption(), $options['ps_checkpayment'][0]);
    }

    private function decorate($cart, Customer $customer, string $additionalInformation = ''): array
    {
        return $this->service->decoratePaymentOptions(
            [
                self::PAYMENT_MODULE => [[
                    'module_name' => self::PAYMENT_MODULE,
                    'call_to_action_text' => 'Pay by bank wire',
                    'additionalInformation' => $additionalInformation,
                    'id' => 'payment-option-1',
                ]],
                'ps_checkpayment' => [$this->otherOption()],
            ],
            $cart,
            $customer,
            Context::getContext()->currency,
            1
        );
    }

    private function otherOption(): array
    {
        return [
            'module_name' => 'ps_checkpayment',
            'call_to_action_text' => 'Pay by check',
            'id' => 'payment-option-2',
        ];
    }
}
