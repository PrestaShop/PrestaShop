<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Controller;

use CartController;
use Product;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Stock can fall below the quantity a product may only be bought from upwards. Every quantity is
 * then refused - a larger one for lack of stock, a smaller one for being under the minimum - and the
 * customer is bounced between the two messages with no way out. Such a product cannot be bought at
 * all, and the cart has to say that rather than ask for a quantity that does not exist.
 */
class CartMinimalQuantityReachableTest extends KernelTestCase
{
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

        $product = new Product();
        $product->minimal_quantity = $minimalQuantity;
        $product->out_of_stock = $outOfStock;

        self::assertSame($expected, $this->isReachable($product, $availableQuantity), $because);
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

    private function isReachable(Product $product, int $availableQuantity): bool
    {
        $controller = (new ReflectionClass(CartController::class))->newInstanceWithoutConstructor();

        $attribute = new ReflectionProperty(CartController::class, 'id_product_attribute');
        $attribute->setAccessible(true);
        $attribute->setValue($controller, 0);

        $method = new ReflectionMethod(CartController::class, 'isMinimalQuantityReachable');
        $method->setAccessible(true);

        return $method->invoke($controller, $product, $availableQuantity);
    }
}
