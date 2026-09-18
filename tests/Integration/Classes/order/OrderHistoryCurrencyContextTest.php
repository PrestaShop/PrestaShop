<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\order;

use Carrier;
use Cart;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Db;
use Employee as LegacyEmployee;
use Mail;
use Module;
use ObjectModel;
use Order;
use OrderHistory;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\ContextBuilderPreparer;
use PrestaShop\PrestaShop\Core\Context\Employee;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddProductToCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\OrderStateByIdChoiceProvider;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Resources\Context\EmployeeContextDecorator;
use Tests\Resources\DatabaseDump;
use Validate;

/**
 * Regression tests for OrderHistory::changeIdOrderState() when legacy Context::currency is unset.
 */
class OrderHistoryCurrencyContextTest extends KernelTestCase
{
    use ContextMockerTrait;

    private const CUSTOMER_EMAIL = 'pub@prestashop.com';
    private const EMPLOYEE_EMAIL = 'test@prestashop.com';
    private const PRODUCT_NAME = 'Mug The best is yet to come';
    private const ADDRESS_COUNTRY_ISO = 'US';
    private const CARRIER_NAME = 'My carrier';

    private CommandBusInterface $commandBus;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::backupContext();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetContext();
    }

    protected function setUp(): void
    {
        parent::setUp();

        DatabaseDump::restoreAllTables();

        self::bootKernel();

        // Legacy classes (Cart, Order) resolve services through SymfonyContainer::getInstance(),
        // which reads the global $kernel. KernelTestCase does not set it, so we wire it here.
        global $kernel;
        $kernel = self::$kernel;

        ObjectModel::disableCache();

        /** @var LegacyContext $legacyContext */
        $legacyContext = self::getContainer()->get('prestashop.adapter.legacy.context');
        $legacyContext->getContext();
        static::mockContext();

        /** @var ContextBuilderPreparer $preparer */
        $preparer = self::getContainer()->get(ContextBuilderPreparer::class);
        $preparer->prepareFromLegacyContext(Context::getContext());

        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');

        Configuration::updateValue('PS_MAIL_METHOD', Mail::METHOD_DISABLE);
        Configuration::updateValue('PS_INVOICE', 1);

        $this->prepareOrderCreationEnvironment();
        $this->setAdminControllerOnContext();
        $this->loginEmployee();
    }

    public function testChangeIdOrderStateToInvoiceGeneratingStateSetsCurrencyFromOrderWhenContextCurrencyIsNull(): void
    {
        $orderId = $this->createUninvoicedOrder();
        $order = new Order($orderId);
        $paymentAcceptedStateId = $this->getOrderStateIdByName('Payment accepted');

        $this->unsetContextCurrency();

        $history = new OrderHistory();
        $history->id_order = $orderId;
        $history->id_employee = (int) Context::getContext()->employee?->id;
        $history->changeIdOrderState($paymentAcceptedStateId, $order, true);
        $history->addWithemail(false);

        $context = Context::getContext();
        $this->assertInstanceOf(Currency::class, $context->currency);
        $this->assertTrue(Validate::isLoadedObject($context->currency));
        $this->assertSame((int) $order->id_currency, (int) $context->currency->id);

        $order = new Order($orderId);
        $this->assertSame($paymentAcceptedStateId, (int) $order->current_state);
        $this->assertNotEmpty($order->invoice_number);
    }

    public function testChangeIdOrderStateToNonInvoiceStateSucceedsWhenContextCurrencyIsNull(): void
    {
        $orderId = $this->createUninvoicedOrder();
        $order = new Order($orderId);
        $awaitingCheckPaymentStateId = $this->getOrderStateIdByName('Awaiting check payment');

        $this->unsetContextCurrency();

        $history = new OrderHistory();
        $history->id_order = $orderId;
        $history->id_employee = (int) Context::getContext()->employee?->id;
        $history->changeIdOrderState($awaitingCheckPaymentStateId, $order, true);
        $history->addWithemail(false);

        $order = new Order($orderId);
        $this->assertSame($awaitingCheckPaymentStateId, (int) $order->current_state);
        $this->assertEmpty($order->invoice_number);
    }

    public function testUpdateOrderStatusHandlerSetsCurrencyFromOrderWhenContextCurrencyIsNull(): void
    {
        $orderId = $this->createUninvoicedOrder();
        $order = new Order($orderId);
        $paymentAcceptedStateId = $this->getOrderStateIdByName('Payment accepted');

        $this->unsetContextCurrency();

        $this->commandBus->handle(new UpdateOrderStatusCommand($orderId, $paymentAcceptedStateId));

        $context = Context::getContext();
        $this->assertInstanceOf(Currency::class, $context->currency);
        $this->assertTrue(Validate::isLoadedObject($context->currency));
        $this->assertSame((int) $order->id_currency, (int) $context->currency->id);

        $order = new Order($orderId);
        $this->assertSame($paymentAcceptedStateId, (int) $order->current_state);
        $this->assertNotEmpty($order->invoice_number);
    }

    private function createUninvoicedOrder(): int
    {
        $customerId = $this->getCustomerIdByEmail(self::CUSTOMER_EMAIL);
        $productId = $this->getProductIdByName(self::PRODUCT_NAME);
        $awaitingBankWireStateId = $this->getOrderStateIdByName('Awaiting bank wire payment');

        Cart::resetStaticCache();

        /** @var \PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId $cartId */
        $cartId = $this->commandBus->handle(new CreateEmptyCustomerCartCommand($customerId));
        $cartIdValue = $cartId->getValue();

        $addressId = $this->getCustomerAddressIdByCountryIso($customerId, self::ADDRESS_COUNTRY_ISO);
        $this->commandBus->handle(new UpdateCartAddressesCommand($cartIdValue, $addressId, $addressId));
        $this->commandBus->handle(new AddProductToCartCommand($cartIdValue, $productId, 2));
        $this->commandBus->handle(new UpdateCartCarrierCommand($cartIdValue, $this->getCarrierIdByName(self::CARRIER_NAME)));

        /** @var OrderId $orderId */
        $orderId = $this->commandBus->handle(new AddOrderFromBackOfficeCommand(
            $cartIdValue,
            (int) Context::getContext()->employee?->id,
            '',
            'dummy_payment',
            $awaitingBankWireStateId
        ));

        return $orderId->getValue();
    }

    private function getOrderStateIdByName(string $stateName): int
    {
        /** @var OrderStateByIdChoiceProvider $orderStateChoiceProvider */
        $orderStateChoiceProvider = self::getContainer()->get('prestashop.core.form.choice_provider.order_state_by_id');
        $availableOrderStates = $orderStateChoiceProvider->getChoices();

        if (!isset($availableOrderStates[$stateName])) {
            self::fail(sprintf('Order state "%s" was not found in test fixtures.', $stateName));
        }

        return (int) $availableOrderStates[$stateName];
    }

    private function getCustomerIdByEmail(string $email): int
    {
        $customers = Customer::getCustomersByEmail($email);
        if (empty($customers[0]['id_customer'])) {
            self::fail(sprintf('Customer with email "%s" was not found in test fixtures.', $email));
        }

        return (int) $customers[0]['id_customer'];
    }

    private function getProductIdByName(string $productName): int
    {
        $productId = (int) Db::getInstance()->getValue(
            'SELECT pl.id_product
            FROM ' . _DB_PREFIX_ . 'product_lang pl
            WHERE pl.name = "' . pSQL($productName) . '"
            AND pl.id_lang = ' . (int) Configuration::get('PS_LANG_DEFAULT')
        );

        if ($productId <= 0) {
            self::fail(sprintf('Product "%s" was not found in test fixtures.', $productName));
        }

        return $productId;
    }

    private function getCustomerAddressIdByCountryIso(int $customerId, string $countryIso): int
    {
        $customer = new Customer($customerId);
        foreach ($customer->getAddresses((int) Configuration::get('PS_LANG_DEFAULT')) as $address) {
            $country = new Country((int) $address['id_country']);
            if ($country->iso_code === $countryIso) {
                return (int) $address['id_address'];
            }
        }

        self::fail(sprintf('Customer %d has no address in country "%s".', $customerId, $countryIso));
    }

    private function prepareOrderCreationEnvironment(): void
    {
        $this->ensureCountryEnabled(self::ADDRESS_COUNTRY_ISO);
        $this->ensureDummyPaymentModuleInstalled();
        $this->setContextCurrencyByIsoCode('USD');
    }

    private function ensureCountryEnabled(string $countryIso): void
    {
        $countryId = (int) Country::getByIso($countryIso);
        if ($countryId <= 0) {
            self::fail(sprintf('Country "%s" was not found in test fixtures.', $countryIso));
        }

        $country = new Country($countryId);
        if (!$country->active) {
            $country->active = true;
            $country->save();
        }
    }

    private function ensureDummyPaymentModuleInstalled(): void
    {
        if (Module::isEnabled('dummy_payment')) {
            return;
        }

        $module = Module::getInstanceByName('dummy_payment');
        if (!$module) {
            self::fail('dummy_payment test module was not found.');
        }

        if (!Module::getModuleIdByName('dummy_payment')) {
            $module->install();
        } else {
            $module->enable();
        }

        Module::resetStaticCache();
    }

    private function setContextCurrencyByIsoCode(string $isoCode): void
    {
        $currencyId = (int) Currency::getIdByIsoCode($isoCode);
        if ($currencyId <= 0) {
            self::fail(sprintf('Currency "%s" was not found in test fixtures.', $isoCode));
        }

        Context::getContext()->currency = new Currency($currencyId);
    }

    private function getCarrierIdByName(string $carrierName): int
    {
        foreach (Carrier::getCarriers((int) Configuration::get('PS_LANG_DEFAULT')) as $carrier) {
            if ($carrier['name'] === $carrierName) {
                return (int) $carrier['id_carrier'];
            }
        }

        self::fail(sprintf('Carrier "%s" was not found in test fixtures.', $carrierName));
    }

    private function unsetContextCurrency(): void
    {
        Context::getContext()->currency = null;
    }

    private function setAdminControllerOnContext(): void
    {
        $adminControllerTestDouble = new stdClass();
        $adminControllerTestDouble->controller_type = 'admin';
        $adminControllerTestDouble->php_self = 'dummyTestDouble';
        Context::getContext()->controller = $adminControllerTestDouble;
    }

    private function loginEmployee(): void
    {
        $legacyEmployee = (new LegacyEmployee())->getByEmail(self::EMPLOYEE_EMAIL);
        if (!$legacyEmployee || !Validate::isLoadedObject($legacyEmployee)) {
            self::fail(sprintf('Employee with email "%s" was not found in test fixtures.', self::EMPLOYEE_EMAIL));
        }

        Context::getContext()->employee = $legacyEmployee;

        /** @var EmployeeContextDecorator $employeeContext */
        $employeeContext = self::getContainer()->get(EmployeeContextDecorator::class);
        $employeeContext->setOverriddenEmployee(new Employee(
            id: (int) $legacyEmployee->id,
            profileId: (int) $legacyEmployee->id_profile,
            languageId: (int) $legacyEmployee->id_lang,
            firstName: $legacyEmployee->firstname,
            lastName: $legacyEmployee->lastname,
            email: $legacyEmployee->email,
            password: $legacyEmployee->passwd,
            imageUrl: $legacyEmployee->getImage(),
            defaultTabId: (int) $legacyEmployee->default_tab,
            defaultShopId: (int) $legacyEmployee->getDefaultShopID(),
            associatedShopIds: $legacyEmployee->getAssociatedShopIds(),
            associatedShopGroupIds: $legacyEmployee->getAssociatedShopGroupIds()
        ));
    }
}
