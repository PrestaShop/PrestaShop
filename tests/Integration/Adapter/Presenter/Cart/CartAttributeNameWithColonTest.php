<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Presenter\Cart;

use Cart;
use Context;
use Currency;
use Customer;
use Db;
use Language;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The cart flattens each attribute into "group<colon>value" and used to parse that text back into
 * pairs. A group or attribute name may itself contain the colon - the back office allows it - and
 * then the pair cannot be recovered, so the attribute silently disappeared from the cart.
 */
class CartAttributeNameWithColonTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['attribute_group_lang'];
    private const COMBINATION_ID = 1;

    private static int $cartId;
    private static int $groupId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        self::bootKernel();

        $db = Db::getInstance();

        // The group this combination belongs to, renamed so its public name carries a colon.
        self::$groupId = (int) $db->getValue(
            'SELECT a.id_attribute_group FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
             INNER JOIN ' . _DB_PREFIX_ . 'attribute a ON a.id_attribute = pac.id_attribute
             WHERE pac.id_product_attribute = ' . self::COMBINATION_ID
        );
        $db->execute(
            'UPDATE ' . _DB_PREFIX_ . "attribute_group_lang SET public_name = 'Colour : shade'
             WHERE id_attribute_group = " . self::$groupId
        );

        $productId = (int) $db->getValue(
            'SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute WHERE id_product_attribute = ' . self::COMBINATION_ID
        );

        $cart = new Cart();
        $cart->id_customer = 1;
        $cart->id_currency = 1;
        $cart->id_lang = 1;
        $cart->id_shop = 1;
        $cart->id_shop_group = 1;
        $cart->id_address_delivery = 0;
        $cart->id_address_invoice = 0;
        $cart->add();
        self::$cartId = (int) $cart->id;
        $cart->updateQty(1, $productId, self::COMBINATION_ID);
    }

    public static function tearDownAfterClass(): void
    {
        Db::getInstance()->delete('cart_product', 'id_cart = ' . self::$cartId);
        Db::getInstance()->delete('cart', 'id_cart = ' . self::$cartId);
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Cart::resetStaticCache();
        parent::tearDownAfterClass();
    }

    public function testAGroupNameContainingAColonStillReachesTheCart(): void
    {
        self::bootKernel();
        Cart::resetStaticCache();

        $context = Context::getContext();
        // Legacy presenters resolve services through the context rather than being injected.
        $context->container = self::getContainer();
        $context->currency = new Currency(1);
        $context->language = new Language(1);
        $context->shop = new Shop(1);
        $context->customer = new Customer(1);
        // The product presenter formats numbers through the context locale.
        $context->currentLocale = self::getContainer()
            ->get('prestashop.core.localization.locale.repository')
            ->getLocale($context->language->getLocale());

        $cart = new Cart(self::$cartId);
        $context->cart = $cart;

        $products = $cart->getProducts(true);
        self::assertNotEmpty($products, 'the cart has no line to present');

        $line = reset($products);
        self::assertArrayHasKey('attributes_array', $line, 'the cart no longer carries the structured pairs');
        self::assertArrayHasKey(
            'Colour : shade',
            $line['attributes_array'],
            'the group name containing a colon was lost'
        );
        self::assertSame($this->expectedAttributeName(), $line['attributes_array']['Colour : shade']);

        // What the theme actually renders: the presented line must carry the same pair.
        $presented = (new CartPresenter())->present($cart);
        $presentedLine = reset($presented['products']);
        self::assertArrayHasKey(
            'Colour : shade',
            $presentedLine['attributes'],
            'the presented cart line lost the group name containing a colon'
        );

        // The flattened text cannot express it, which is why the pairs are kept alongside it.
        self::assertArrayNotHasKey(
            'Colour : shade',
            (new CartPresenter())->getAttributesArrayFromString($line['attributes']),
            'if parsing the text recovered the name, keeping the pairs would be pointless'
        );
    }

    private function expectedAttributeName(): string
    {
        return (string) Db::getInstance()->getValue(
            'SELECT al.name FROM ' . _DB_PREFIX_ . 'product_attribute_combination pac
             INNER JOIN ' . _DB_PREFIX_ . 'attribute a ON a.id_attribute = pac.id_attribute
             INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang al ON al.id_attribute = pac.id_attribute AND al.id_lang = 1
             WHERE pac.id_product_attribute = ' . self::COMBINATION_ID . ' AND a.id_attribute_group = ' . self::$groupId
        );
    }
}
