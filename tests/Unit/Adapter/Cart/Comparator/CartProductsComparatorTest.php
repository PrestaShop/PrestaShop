<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
     * @dataProvider getTestAllProducts
     */
    public function testGetAllUpdatedProducts(
        array $initialProducts,
        array $newProducts,
        array $expectedUpdatedProducts,
        array $knownUpdatedProducts
    ) {
        $cart = $this->mockCart($initialProducts, $newProducts);
        $comparator = new CartProductsComparator($cart);
        foreach ($knownUpdatedProducts as $knownUpdatedProduct) {
            $comparator->addKnownUpdate($knownUpdatedProduct);
        }

        $updatedProducts = $comparator->getAllUpdatedProducts();
        Assert::assertEquals($expectedUpdatedProducts, $updatedProducts);
    }

    public function getCommonTestProducts()
    {
        yield [
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 1],
            ],
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
            ],
            [
                new CartProductUpdate(2, 0, -1),
            ],
            [
            ],
        ];

        yield [
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 1],
            ],
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 1],
            ],
            [
            ],
            [
            ],
        ];

        yield [
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 3],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 1],
            ],
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 2],
            ],
            [
                new CartProductUpdate(1, 0, -2),
                new CartProductUpdate(2, 0, 1),
            ],
            [
            ],
        ];

        yield [
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 3],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 1],
            ],
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 2],
                ['id_product' => 3, 'id_product_attribute' => 0, 'cart_quantity' => 2],
            ],
            [
                new CartProductUpdate(1, 0, -2),
                new CartProductUpdate(2, 0, 1),
                new CartProductUpdate(3, 0, 2),
            ],
            [
            ],
        ];
    }

    public function getTestAllProducts()
    {
        foreach ($this->getCommonTestProducts() as $commonTestProduct) {
            yield $commonTestProduct;
        }

        yield [
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 3],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 1],
            ],
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 2],
                ['id_product' => 3, 'id_product_attribute' => 0, 'cart_quantity' => 2],
            ],
            [
                new CartProductUpdate(1, 0, -2),
                new CartProductUpdate(2, 0, 1),
                new CartProductUpdate(3, 0, 2),
            ],
            [
                new CartProductUpdate(1, 0, -2),
            ],
        ];
    }

    /**
     * @dataProvider getTestProducts
     */
    public function testGetUpdatedProducts(
        array $initialProducts,
        array $newProducts,
        array $expectedUpdatedProducts,
        array $knownUpdatedProducts
    ) {
        $cart = $this->mockCart($initialProducts, $newProducts);
        $comparator = new CartProductsComparator($cart);
        foreach ($knownUpdatedProducts as $knownUpdatedProduct) {
            $comparator->addKnownUpdate($knownUpdatedProduct);
        }

        $updatedProducts = $comparator->getUpdatedProducts();
        Assert::assertEquals($expectedUpdatedProducts, $updatedProducts);
    }

    public function getTestProducts()
    {
        foreach ($this->getCommonTestProducts() as $commonTestProduct) {
            yield $commonTestProduct;
        }

        yield [
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 3],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 1],
            ],
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 2],
                ['id_product' => 3, 'id_product_attribute' => 0, 'cart_quantity' => 2],
            ],
            [
                new CartProductUpdate(2, 0, 1),
                new CartProductUpdate(3, 0, 2),
            ],
            [
                new CartProductUpdate(1, 0, -2),
            ],
        ];

        yield [
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 3],
            ],
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 2],
            ],
            [
            ],
            [
                new CartProductUpdate(2, 0, -1),
            ],
        ];

        yield [
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 2],
            ],
            [
                ['id_product' => 1, 'id_product_attribute' => 0, 'cart_quantity' => 1],
                ['id_product' => 2, 'id_product_attribute' => 0, 'cart_quantity' => 3],
            ],
            [
                new CartProductUpdate(2, 0, 2),
            ],
            [
                new CartProductUpdate(2, 0, -1),
            ],
        ];
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
