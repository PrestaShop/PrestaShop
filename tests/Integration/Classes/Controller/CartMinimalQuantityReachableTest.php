<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use Cart;
use CartControllerCore;
use Configuration;
use Context;
use Currency;
use Db;
use Product;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class CartMinimalQuantityReachableTest extends KernelTestCase
{
    private const PRODUCT_ID = 6;

    /**
     * @dataProvider getStockSituations
     */
    public function testAProductIsOnlyBuyableWhenStockReachesItsMinimum(
        int $minimalQuantity,
        int $availableQuantity,
        int $outOfStock,
        bool $expected,
        string $because
    ): void {
        self::bootKernel();

        self::assertSame($expected, $this->isReachable($availableQuantity, $minimalQuantity, $outOfStock), $because);
    }

    public static function getStockSituations(): array
    {
        // 0 denies ordering out of stock, 1 allows it.
        return [
            'stock fell below the minimum' => [
                3, 2, 0, false,
                'no quantity is acceptable, so the product cannot be bought at all',
            ],
            'stock exactly meets the minimum' => [
                3, 3, 0, true,
                'the minimum can still be bought',
            ],
            'stock is above the minimum' => [
                3, 10, 0, true,
                'nothing stands in the way',
            ],
            'out of stock orders are allowed' => [
                3, 0, 1, true,
                'stock is not the limit when the shop sells beyond it',
            ],
            'no minimum set' => [
                1, 0, 0, false,
                'nothing is left to sell',
            ],
        ];
    }

    public function testThePageLoadCheckSaysAProductUnderItsMinimumIsNoLongerAvailable(): void
    {
        self::bootKernel();
        $context = Context::getContext();
        $savedCart = $context->cart;
        $savedCurrency = $context->currency;
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $db = Db::getInstance();
        $db->update('product', ['minimal_quantity' => 3], 'id_product = ' . self::PRODUCT_ID);
        $db->update('product_shop', ['minimal_quantity' => 3], 'id_product = ' . self::PRODUCT_ID);
        $db->update('stock_available', ['quantity' => 2, 'out_of_stock' => 0], 'id_product = ' . self::PRODUCT_ID);

        try {
            $cart = new Cart();
            $cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
            $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
            $cart->id_shop = 1;
            $cart->add();
            $db->insert('cart_product', ['id_cart' => (int) $cart->id, 'id_product' => self::PRODUCT_ID, 'id_product_attribute' => 0, 'id_shop' => 1, 'quantity' => 3, 'date_add' => date('Y-m-d H:i:s')]);
            $context->cart = new Cart((int) $cart->id);
            Cart::resetStaticCache();
            Product::resetStaticCache();
            StockAvailable::resetStaticCache();

            $controller = (new ReflectionClass(CartControllerCore::class))->newInstanceWithoutConstructor();
            $contextProperty = new ReflectionProperty(CartControllerCore::class, 'context');
            $contextProperty->setAccessible(true);
            $contextProperty->setValue($controller, $context);
            $translatorProperty = new ReflectionProperty(CartControllerCore::class, 'translator');
            $translatorProperty->setAccessible(true);
            $translatorProperty->setValue($controller, $context->getTranslator());
            $method = new ReflectionMethod(CartControllerCore::class, 'areProductsAvailable');
            $method->setAccessible(true);

            self::assertStringContainsString('is no longer available', (string) $method->invoke($controller));
        } finally {
            $context->cart = $savedCart;
            $context->currency = $savedCurrency;
            DatabaseDump::restoreTables(['product', 'product_shop', 'stock_available', 'cart', 'cart_product']);
        }
    }

    private function isReachable(int $availableQuantity, int $minimalQuantity, int $outOfStock): bool
    {
        $controller = (new ReflectionClass(CartControllerCore::class))->newInstanceWithoutConstructor();
        $method = new ReflectionMethod(CartControllerCore::class, 'isMinimalQuantityReachable');
        $method->setAccessible(true);

        return $method->invoke($controller, $availableQuantity, $minimalQuantity, $outOfStock);
    }
}
