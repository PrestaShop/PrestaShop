<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Address;
use Cart;
use Combination;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Db;
use Language;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * A combination carries an impact on the product's shipping fee, the way it carries one on the
 * product's weight, so what the cart charges is the sum of the two.
 */
class CombinationShippingCostTest extends KernelTestCase
{
    private const PRODUCT_ID = 1;

    private int $combinationId;
    private ?Cart $cart = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        // Legacy code reaches services through this global, and it is not set for a booted kernel.
        global $kernel;
        $kernel = self::$kernel;

        Configuration::loadConfiguration();
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);

        $this->combinationId = (int) Db::getInstance()->getValue(
            'SELECT `id_product_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute`
             WHERE `id_product` = ' . self::PRODUCT_ID . ' ORDER BY `id_product_attribute`'
        );
        $this->prepareContext();
    }

    protected function tearDown(): void
    {
        if (null !== $this->cart && $this->cart->id) {
            $this->cart->delete();
        }
        $this->cart = null;
        DatabaseDump::restoreTables(['product', 'product_shop', 'product_attribute', 'product_attribute_shop']);
        parent::tearDown();
    }

    /**
     * @dataProvider provideShippingFees
     */
    public function testTheCartChargesTheProductFeePlusTheCombinationImpact(
        string $productFee,
        string $combinationFee,
        float $expected
    ): void {
        $this->writeFees($productFee, $combinationFee);

        $row = $this->addCombinationToCart();

        $this->assertSame(
            $expected,
            round((float) $row['additional_shipping_cost'], 6),
            'The cart did not charge the product fee plus the impact of its combination.'
        );
    }

    /**
     * @return array<string, array{string, string, float}>
     */
    public static function provideShippingFees(): array
    {
        return [
            'neither carries a fee' => ['0', '0', 0.0],
            // A shop that never sets an impact must keep charging exactly what it charged before.
            'only the product carries one' => ['3', '0', 3.0],
            'both carry one' => ['3', '2.5', 5.5],
            'only the combination carries one' => ['0', '4', 4.0],
        ];
    }

    public function testACombinationImpactDoesNotReachAnotherCombination(): void
    {
        $otherId = (int) Db::getInstance()->getValue(
            'SELECT `id_product_attribute` FROM `' . _DB_PREFIX_ . 'product_attribute`
             WHERE `id_product` = ' . self::PRODUCT_ID . ' AND `id_product_attribute` != ' . $this->combinationId . '
             ORDER BY `id_product_attribute`'
        );
        $this->assertGreaterThan(0, $otherId, 'The fixture product needs a second combination.');

        $this->writeFees('1', '9');

        $combination = new Combination($otherId);
        $this->assertSame(0.0, round((float) $combination->additional_shipping_cost, 6));

        $row = $this->addCombinationToCart($otherId);
        $this->assertSame(1.0, round((float) $row['additional_shipping_cost'], 6));
    }

    private function writeFees(string $productFee, string $combinationFee): void
    {
        $db = Db::getInstance();
        foreach (['product', 'product_shop'] as $table) {
            $db->execute('UPDATE `' . _DB_PREFIX_ . $table . '` SET `additional_shipping_cost` = ' . (float) $productFee
                . ' WHERE `id_product` = ' . self::PRODUCT_ID);
        }
        foreach (['product_attribute', 'product_attribute_shop'] as $table) {
            $db->execute('UPDATE `' . _DB_PREFIX_ . $table . '` SET `additional_shipping_cost` = ' . (float) $combinationFee
                . ' WHERE `id_product_attribute` = ' . $this->combinationId);
        }
        Cart::resetStaticCache();
        Product::flushPriceCache();
    }

    /**
     * @return array<string, mixed> the cart row for that combination
     */
    private function addCombinationToCart(?int $combinationId = null): array
    {
        $context = Context::getContext();
        $cart = new Cart();
        $cart->id_customer = (int) Db::getInstance()->getValue('SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'customer`');
        $cart->id_address_delivery = (int) Db::getInstance()->getValue('SELECT `id_address` FROM `' . _DB_PREFIX_ . 'address` WHERE `deleted` = 0');
        $cart->id_address_invoice = $cart->id_address_delivery;
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $cart->id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        $cart->add();
        $context->cart = $cart;

        $cart->updateQty(1, self::PRODUCT_ID, $combinationId ?? $this->combinationId);
        Cart::resetStaticCache();

        $this->cart = new Cart((int) $cart->id);
        $context->cart = $this->cart;

        return $this->cart->getProducts(true)[0];
    }

    private function prepareContext(): void
    {
        $context = Context::getContext();
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->customer = new Customer((int) Db::getInstance()->getValue('SELECT `id_customer` FROM `' . _DB_PREFIX_ . 'customer`'));
        $idAddress = (int) Db::getInstance()->getValue('SELECT `id_address` FROM `' . _DB_PREFIX_ . 'address` WHERE `deleted` = 0');
        $address = new Address($idAddress);
        if ($address->id_country) {
            $context->country = new Country((int) $address->id_country);
        }
    }
}
