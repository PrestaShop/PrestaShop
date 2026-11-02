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
use Currency;
use Customer;
use Language;
use PrestaShop\PrestaShop\Adapter\TradeCredit\TradeCreditOrderService;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditInsufficientException;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\TradeCreditAvailability;
use PrestaShopBundle\Entity\CustomerTradeCreditOperation;
use PrestaShopBundle\Entity\Repository\CustomerTradeCreditRepository;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\SymfonyIntegrationTestCase;

class TradeCreditOrderServiceTest extends SymfonyIntegrationTestCase
{
    private const CUSTOMER_ID = 1;

    private TradeCreditOrderService $service;

    private CustomerTradeCreditRepository $repository;

    private Currency $creditCurrency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = self::getContainer()->get(TradeCreditOrderService::class);
        $this->repository = self::getContainer()->get(CustomerTradeCreditRepository::class);
        $this->creditCurrency = self::createCurrency('PLN', 4.0);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables([
            'cart', 'currency', 'currency_lang', 'currency_shop', 'configuration',
            'customer_trade_credit', 'customer_trade_credit_operation',
        ]);
        Cache::clean('*');
        parent::tearDown();
    }

    public function testReserveGrantsDefaultAndDebits(): void
    {
        $cart = $this->createCart($this->creditCurrency);

        $this->service->reserveForCart($cart, new Customer(self::CUSTOMER_ID), $this->creditCurrency, 1234.56);

        $this->assertSame('48765.440000', $this->repository->findBalance(self::CUSTOMER_ID));
        $this->assertNotNull($this->repository->findOperation('grant:customer:' . self::CUSTOMER_ID));
        $debit = $this->repository->findOperation('debit:cart:' . $cart->id);
        $this->assertSame(CustomerTradeCreditOperation::TYPE_DEBIT, $debit['type']);
        $this->assertSame('-1234.560000', $debit['amount']);
    }

    public function testReserveRespectsAdminBalance(): void
    {
        $this->repository->createBalance(self::CUSTOMER_ID, '1000.00');
        $cart = $this->createCart($this->creditCurrency);

        try {
            $this->service->reserveForCart($cart, new Customer(self::CUSTOMER_ID), $this->creditCurrency, 1234.56);
            $this->fail('Order above the available credit must be rejected.');
        } catch (TradeCreditInsufficientException $e) {
            $this->assertSame(TradeCreditAvailability::REASON_INSUFFICIENT, $e->getAvailability()->getReason());
        }

        $this->assertSame('1000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
        $this->assertNull($this->repository->findOperation('debit:cart:' . $cart->id));
    }

    public function testReserveTwiceIsNoop(): void
    {
        $cart = $this->createCart($this->creditCurrency);
        $customer = new Customer(self::CUSTOMER_ID);

        $this->service->reserveForCart($cart, $customer, $this->creditCurrency, 100.0);
        $this->service->reserveForCart($cart, $customer, $this->creditCurrency, 100.0);

        $this->assertSame('49900.000000', $this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testReleaseReturnsDebit(): void
    {
        $cart = $this->createCart($this->creditCurrency);
        $this->service->reserveForCart($cart, new Customer(self::CUSTOMER_ID), $this->creditCurrency, 100.0);

        $this->service->releaseForCart((int) $cart->id);
        $this->service->releaseForCart((int) $cart->id);

        $this->assertSame('50000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
        $this->assertSame(CustomerTradeCreditOperation::TYPE_RELEASE, $this->repository->findOperation('release:cart:' . $cart->id)['type']);
    }

    public function testGuestIsRejected(): void
    {
        $cart = $this->createCart($this->creditCurrency);
        $guest = new Customer(self::CUSTOMER_ID);
        $guest->is_guest = true;

        try {
            $this->service->reserveForCart($cart, $guest, $this->creditCurrency, 10.0);
            $this->fail('Guests cannot use trade credit.');
        } catch (TradeCreditInsufficientException $e) {
            $this->assertSame(TradeCreditAvailability::REASON_GUEST, $e->getAvailability()->getReason());
        }

        $this->assertNull($this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testOtherCurrencyIsConverted(): void
    {
        $defaultCurrency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $cart = $this->createCart($defaultCurrency);

        // 100 in the default currency (rate 1) is 400 PLN (rate 4)
        $this->service->reserveForCart($cart, new Customer(self::CUSTOMER_ID), $defaultCurrency, 100.0);

        $this->assertSame('-400.000000', $this->repository->findOperation('debit:cart:' . $cart->id)['amount']);
    }

    public function testCurrencyUnavailable(): void
    {
        Configuration::updateValue('PS_TRADE_CREDIT_CURRENCY', 'XXX');
        $cart = $this->createCart($this->creditCurrency);

        $availability = $this->service->getAvailabilityForCart($cart, new Customer(self::CUSTOMER_ID), $this->creditCurrency);

        $this->assertSame(TradeCreditAvailability::REASON_CURRENCY_UNAVAILABLE, $availability->getReason());
    }

    public static function createCurrency(string $isoCode, float $conversionRate): Currency
    {
        $currency = new Currency();
        $currency->iso_code = $isoCode;
        $currency->numeric_iso_code = '985';
        $currency->precision = 2;
        $currency->conversion_rate = $conversionRate;
        $currency->active = true;
        foreach (Language::getIDs(false) as $languageId) {
            $currency->name[$languageId] = $isoCode;
            $currency->symbol[$languageId] = $isoCode;
        }
        $currency->add();
        Cache::clean('*');

        return $currency;
    }

    private function createCart(Currency $currency): Cart
    {
        $cart = new Cart();
        $cart->id_customer = self::CUSTOMER_ID;
        $cart->id_currency = (int) $currency->id;
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->add();

        return $cart;
    }
}
