<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Cart;

use Address;
use Cart;
use CartRule;
use Configuration;
use Context;
use Currency;
use DateTime;
use Db;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartVoucherSummaryTest extends KernelTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        // ContainerFinder (used by Cart::getCartRules) reads the global $kernel.
        global $kernel;
        $kernel = self::$kernel;
        Context::getContext()->currency = Currency::getDefaultCurrency();
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);
        Configuration::updateValue('PS_TAX_ADDRESS_TYPE', 'id_address_invoice');
        // Pre-existing (auto-applied) cart rules would pollute the cart summary.
        Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'cart_rule SET active = 0');
        Configuration::set('PS_CART_RULE_FEATURE_ACTIVE', true);
    }

    /**
     * Two percentage vouchers restricted to the same product stack: the first takes 10% of the
     * price, the second takes 10% of the already-reduced price. The cart summary must show each
     * voucher's actually-applied reduction, so the per-voucher figures sum to the cart's real
     * discount - not the naive per-rule value that double-counts (10 + 10 instead of 10 + 9).
     */
    public function testProductRestrictedPercentageVouchersSummarizeTheAppliedReduction(): void
    {
        $lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $address = new Address();
        $address->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $address->firstname = 'Unit';
        $address->lastname = 'Tester';
        $address->address1 = '55 rue Raspail';
        $address->alias = microtime() . getmypid();
        $address->city = 'Levallois';
        $address->save();

        $product = new Product(null, false, $lang);
        $product->id_tax_rules_group = 0;
        $product->name = 'Voucher summary product';
        $product->price = 100;
        $product->link_rewrite = 'voucher-summary-product';
        $product->save();

        $cart = new Cart(null, $lang);
        $cart->id_currency = Currency::getDefaultCurrencyId();
        $cart->id_address_invoice = (int) $address->id;
        $cart->save();
        Context::getContext()->cart = $cart;
        $cart->updateQty(1, (int) $product->id);

        foreach (['A', 'B'] as $suffix) {
            $cartRule = new CartRule(null, $lang);
            $cartRule->name = 'Ten percent ' . $suffix;
            $from = (new DateTime())->modify('-2 days');
            $to = (new DateTime())->modify('+2 days');
            $cartRule->date_from = $from->format('Y-m-d H:i:s');
            $cartRule->date_to = $to->format('Y-m-d H:i:s');
            $cartRule->quantity = 100;
            $cartRule->quantity_per_user = 100;
            $cartRule->reduction_percent = 10;
            $cartRule->reduction_product = (int) $product->id;
            $cartRule->active = true;
            $cartRule->save();
            $cart->addCartRule((int) $cartRule->id);
        }

        $presented = (new CartPresenter())->present($cart);

        $vouchers = $presented['vouchers']['added'];
        self::assertCount(2, $vouchers, 'Both vouchers should be listed in the summary.');

        $displayedReductions = array_map(
            static function (array $voucher): float {
                // reduction_formatted is a signed, currency-formatted string, e.g. "-$9.00".
                return (float) preg_replace('/[^\d.]/', '', $voucher['reduction_formatted']);
            },
            array_values($vouchers)
        );

        $cartDiscount = (float) $presented['subtotals']['discounts']['amount'];

        self::assertEqualsWithDelta(
            $cartDiscount,
            array_sum($displayedReductions),
            0.001,
            'The reductions shown per voucher must sum to the cart discount actually applied.'
        );
    }
}
