<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Cart\Comparator;

use Cart;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductsComparator;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductUpdate;

class CartProductsComparatorTest extends TestCase
{
    /**
     * @dataProvider getExpectedModifiedProducts
     *
     * @param array $initialProducts
     * @param array $newProducts
     * @param array $knownUpdatedProducts
     * @param array $expectedModifiedProducts
     */
    public function testGetModifiedProducts(
        array $initialProducts,
        array $newProducts,
        array $knownUpdatedProducts,
        array $expectedModifiedProducts
    ) {
        $cart = $this->mockCart($initialProducts, $newProducts);
        $comparator = new CartProductsComparator($cart);
        $comparator->setKnownUpdates($knownUpdatedProducts);

        $modifiedProducts = $comparator->getModifiedProducts();
        Assert::assertEquals($expectedModifiedProducts, $modifiedProducts);
    }

    public function getExpectedModifiedProducts()
    {
        yield [
            // Previous products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
            ],
            // New products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
            ],
            // Known updates
            [
            ],
            // Expected updates
            [
                new CartProductUpdate(2, 0, -1, false),
            ],
        ];

        yield [
            // Previous products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
            ],
            // New products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
            ],
            // Known updates
            [
            ],
            // Expected updates
            [
            ],
        ];

        yield [
            // Previous products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 3],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
            ],
            // New products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
            ],
            // Known updates
            [
            ],
            // Expected updates
            [
                new CartProductUpdate(1, 0, -2, false),
                new CartProductUpdate(2, 0, 1, false),
            ],
        ];

        yield [
            // Previous products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 3],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
            ],
            // New products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
                ['id_product' => 3, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
            ],
            // Known updates
            [
            ],
            // Expected updates
            [
                new CartProductUpdate(1, 0, -2, false),
                new CartProductUpdate(2, 0, 1, false),
                new CartProductUpdate(3, 0, 2, true),
            ],
        ];

        yield [
            // Previous products
            [
                ['id_product' => 1, 'id_product_attribute' => 1, 'id_customization' => 0, 'cart_quantity' => 3],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
            ],
            // New products
            [
                ['id_product' => 1, 'id_product_attribute' => 1, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
                ['id_product' => 3, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
            ],
            // Known updates
            [
                new CartProductUpdate(1, 1, -2, false),
            ],
            // Expected updates
            [
                new CartProductUpdate(2, 0, 1, false),
                new CartProductUpdate(3, 0, 2, true),
            ],
        ];

        yield [
            // Previous products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 1, 'cart_quantity' => 3],
            ],
            // New products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 1, 'cart_quantity' => 2],
            ],
            // Known updates
            [
                new CartProductUpdate(2, 0, -1, false, 1),
            ],
            // Expected updates
            [
            ],
        ];

        yield [
            // Previous products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
            ],
            // New products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 3],
            ],
            // Known updates
            [
                new CartProductUpdate(2, 0, -1, false),
            ],
            // Expected updates
            [
                new CartProductUpdate(2, 0, 2, false),
            ],
        ];

        yield [
            // Previous products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
            ],
            // New products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
                ['id_product' => 3, 'id_product_attribute' => 0, 'id_customization' => 1, 'cart_quantity' => 1],
            ],
            // Known updates
            [
                new CartProductUpdate(3, 0, 1, true, 1),
            ],
            // Expected updates
            [
            ],
        ];

        yield [
            // Previous products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
            ],
            // New products
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'id_customization' => 0, 'cart_quantity' => 2],
                ['id_product' => 3, 'id_product_attribute' => 0, 'id_customization' => 1, 'cart_quantity' => 2],
            ],
            // Known updates
            [
                new CartProductUpdate(3, 0, 1, true, 1),
            ],
            // Expected updates
            [
                new CartProductUpdate(3, 0, 1, true, 1),
            ],
        ];
    }

    /**
     * @dataProvider getExpectedAdditionalProducts
     *
     * @param array $initialProducts
     * @param array $newProducts
     * @param array $knownUpdatedProducts
     * @param array $expectedModifiedProducts
     */
    public function testGetAdditionalProducts(
        array $initialProducts,
        array $newProducts,
        array $knownUpdatedProducts,
        array $expectedModifiedProducts
    ) {
        $cart = $this->mockCart($initialProducts, $newProducts);
        $comparator = new CartProductsComparator($cart);
        $comparator->setKnownUpdates($knownUpdatedProducts);

        $modifiedProducts = $comparator->getAdditionalProducts();
        Assert::assertEquals($expectedModifiedProducts, $modifiedProducts);
    }

    public function getExpectedAdditionalProducts()
    {
        $modifiedProducts = $this->getExpectedModifiedProducts();
        foreach ($modifiedProducts as $modifiedProduct) {
            $expectedUpdates = $modifiedProduct[3];

            // Filter update modifications
            $filteredExpectedUpdates = [];
            /** @var CartProductUpdate $expectedUpdate */
            foreach ($expectedUpdates as $expectedUpdate) {
                if ($expectedUpdate->isCreated()) {
                    $filteredExpectedUpdates[] = $expectedUpdate;
                }
            }
            $modifiedProduct[3] = $filteredExpectedUpdates;

            yield $modifiedProduct;
        }
    }

    /**
     * @dataProvider getExpectedUpdatedProducts
     *
     * @param array $initialProducts
     * @param array $newProducts
     * @param array $knownUpdatedProducts
     * @param array $expectedModifiedProducts
     */
    public function testGetUpdatedProducts(
        array $initialProducts,
        array $newProducts,
        array $knownUpdatedProducts,
        array $expectedModifiedProducts
    ) {
        $cart = $this->mockCart($initialProducts, $newProducts);
        $comparator = new CartProductsComparator($cart);
        $comparator->setKnownUpdates($knownUpdatedProducts);

        $modifiedProducts = $comparator->getUpdatedProducts();
        Assert::assertEquals($expectedModifiedProducts, $modifiedProducts);
    }

    public function getExpectedUpdatedProducts()
    {
        $modifiedProducts = $this->getExpectedModifiedProducts();
        foreach ($modifiedProducts as $modifiedProduct) {
            $expectedUpdates = $modifiedProduct[3];

            // Filter update modifications
            $filteredExpectedUpdates = [];
            /** @var CartProductUpdate $expectedUpdate */
            foreach ($expectedUpdates as $expectedUpdate) {
                if (!$expectedUpdate->isCreated()) {
                    $filteredExpectedUpdates[] = $expectedUpdate;
                }
            }
            $modifiedProduct[3] = $filteredExpectedUpdates;

            yield $modifiedProduct;
        }
    }

    /**
     * @param array $initialProducts
     * @param array $newProducts
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|Cart
     */
    private function mockCart(array $initialProducts, array $newProducts)
    {
        $cartMock = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $cartMock
            ->method('getProducts')
            ->willReturnOnConsecutiveCalls(
                $initialProducts,
                $newProducts
            )
        ;

        return $cartMock;
    }
}
