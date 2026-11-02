<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\TradeCredit;

use Cache;
use Cart;
use Configuration;
use Context;
use Currency;
use Customer;
use Module;
use Order;
use OrderHistory;
use PaymentModule;
use PrestaShop\PrestaShop\Adapter\TradeCredit\Repository\TradeCreditPaymentModuleRepository;
use PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\SymfonyIntegrationTestCase;

/**
 * Places real orders through PaymentModule::validateOrder() with the bankwire test payment module,
 * flagged as a trade credit payment method. The credit is kept in the shop default currency.
 */
abstract class AbstractTradeCreditOrderTestCase extends SymfonyIntegrationTestCase
{
    protected const CUSTOMER_ID = 1;
    protected const ADDRESS_ID = 1;
    protected const CARRIER_ID = 2;
    protected const PRODUCT_ID = 1;
    protected const OTHER_PRODUCT_ID = 2;
    protected const PAYMENT_MODULE = 'bankwire';

    protected CustomerTradeCreditRepository $repository;

    protected TradeCreditPaymentModuleRepository $paymentModuleRepository;

    protected PaymentModule $paymentModule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(CustomerTradeCreditRepository::class);
        $this->paymentModuleRepository = self::getContainer()->get(TradeCreditPaymentModuleRepository::class);
        $this->paymentModule = Module::getInstanceByName(self::PAYMENT_MODULE);

        $defaultCurrency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        Configuration::updateValue('PS_TRADE_CREDIT_CURRENCY', $defaultCurrency->iso_code);
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', 1);
        $this->paymentModuleRepository->setTradeCreditModules(1, [(int) $this->paymentModule->id]);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreAllTables();
        Cache::clean('*');
        parent::tearDown();
    }

    protected function createCart(?Currency $currency = null): Cart
    {
        $customer = new Customer(self::CUSTOMER_ID);
        $currency ??= new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));

        $cart = new Cart();
        $cart->id_customer = self::CUSTOMER_ID;
        $cart->id_address_delivery = self::ADDRESS_ID;
        $cart->id_address_invoice = self::ADDRESS_ID;
        $cart->id_currency = (int) $currency->id;
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_carrier = self::CARRIER_ID;
        $cart->secure_key = $customer->secure_key;
        $cart->add();
        $cart->updateQty(1, self::PRODUCT_ID);
        $cart->setDeliveryOption([self::ADDRESS_ID => self::CARRIER_ID . ',']);
        $cart->update();

        $context = Context::getContext();
        $context->cart = $cart;
        $context->customer = $customer;
        $context->currency = $currency;

        return $cart;
    }

    /**
     * Adds a product and refreshes the cart package cache, which validateOrder() uses to build the orders.
     */
    protected function addProduct(Cart $cart, int $productId): void
    {
        $cart->updateQty(1, $productId);
        $cart->getPackageList(true);
        $cart->getDeliveryOptionList(null, true);
    }

    protected function validateOrder(
        Cart $cart,
        ?PaymentModule $paymentModule = null,
        ?float $amountPaid = null,
        string $stateConfigurationKey = 'PS_OS_BANKWIRE'
    ): Order {
        $paymentModule ??= $this->paymentModule;
        $paymentModule->validateOrder(
            (int) $cart->id,
            (int) Configuration::get($stateConfigurationKey),
            $amountPaid ?? (float) $cart->getOrderTotal(true, Cart::BOTH),
            'Bank wire',
            null,
            [],
            null,
            false,
            $cart->secure_key
        );

        return new Order((int) Order::getIdByCartId((int) $cart->id));
    }

    protected function changeOrderState(Order $order, string $stateConfigurationKey): Order
    {
        $history = new OrderHistory();
        $history->id_order = (int) $order->id;
        $history->changeIdOrderState((int) Configuration::get($stateConfigurationKey), $order, true);
        $history->add();

        return new Order((int) $order->id);
    }
}
