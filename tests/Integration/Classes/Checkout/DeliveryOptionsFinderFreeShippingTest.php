<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Checkout;

use Address;
use Cache;
use Cart;
use CartRule;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Db;
use DeliveryOptionsFinder;
use Language;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class DeliveryOptionsFinderFreeShippingTest extends KernelTestCase
{
    private const TABLES = ['cart_rule', 'cart_rule_lang', 'cart_rule_shop', 'cart_rule_carrier', 'cart_cart_rule'];

    private Context $context;

    /**
     * @var array<string, mixed>
     */
    private array $savedContext = [];

    private Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->context = Context::getContext();
        foreach (['container', 'cart', 'customer', 'currency', 'language', 'country'] as $property) {
            $this->savedContext[$property] = $this->context->{$property} ?? null;
        }

        $this->cart = new Cart((int) Db::getInstance()->getValue(
            'SELECT id_cart FROM ' . _DB_PREFIX_ . 'cart WHERE id_address_delivery > 0 ORDER BY id_cart'
        ));
        $this->context->container = self::getContainer();
        $this->context->cart = $this->cart;
        $this->context->customer = new Customer((int) $this->cart->id_customer);
        $this->context->currency = new Currency((int) $this->cart->id_currency);
        $this->context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $this->context->country = new Country((int) (new Address((int) $this->cart->id_address_delivery))->id_country);
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(self::TABLES);
        foreach ($this->savedContext as $property => $value) {
            $this->context->{$property} = $value;
        }
        parent::tearDown();
    }

    public function testAFreeShippingCartRuleMakesEveryOptionFree(): void
    {
        $this->addFreeShippingRule(null);

        $prices = $this->getOptionPrices();

        self::assertNotEmpty($prices);
        self::assertSame([$this->free()], array_values(array_unique($prices)));
    }

    public function testAFreeShippingCartRuleRestrictedToACarrierLeavesTheOthersPriced(): void
    {
        $carrierId = (int) Db::getInstance()->getValue('SELECT id_reference FROM ' . _DB_PREFIX_ . 'carrier WHERE deleted = 0 AND is_free = 1');
        $this->addFreeShippingRule($carrierId);

        self::assertNotEmpty(array_diff($this->getOptionPrices(), [$this->free()]));
    }

    /**
     * @return string[]
     */
    private function getOptionPrices(): array
    {
        $finder = new DeliveryOptionsFinder(
            $this->context,
            self::getContainer()->get('translator'),
            new ObjectPresenter(),
            new PriceFormatter()
        );

        return array_column($finder->getDeliveryOptions(), 'price');
    }

    private function free(): string
    {
        return self::getContainer()->get('translator')->trans('Free', [], 'Shop.Theme.Checkout');
    }

    private function addFreeShippingRule(?int $carrierReferenceId): void
    {
        $cartRule = new CartRule();
        $cartRule->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Free shipping'];
        $cartRule->free_shipping = true;
        $cartRule->quantity = 100;
        $cartRule->quantity_per_user = 100;
        $cartRule->date_from = date('Y-m-d H:i:s', strtotime('-1 day'));
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+1 year'));
        $cartRule->carrier_restriction = null !== $carrierReferenceId;
        $cartRule->add();
        if (null !== $carrierReferenceId) {
            Db::getInstance()->insert('cart_rule_carrier', ['id_cart_rule' => (int) $cartRule->id, 'id_carrier' => $carrierReferenceId]);
        }
        Db::getInstance()->insert('cart_cart_rule', ['id_cart' => (int) $this->cart->id, 'id_cart_rule' => (int) $cartRule->id]);
        CartRule::resetStaticCache();
        Cart::resetStaticCache();
        Cache::clean('Cart::getCartRules_*');
    }
}
