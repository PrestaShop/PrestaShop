<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Cart\Calculator;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Calculator\ProductTotalCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPrice;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Provider\CartProduct;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Provider\MockCartProductProvider;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\BaseProductCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Product\Provider\MockProductProvider;
use PrestaShop\PrestaShop\Core\Pricing\Product\Provider\ProductPriceData;

class ProductTotalCalculatorTest extends TestCase
{
    public function testEmptyCartProducesZeroTotals(): void
    {
        $cartProductProvider = new MockCartProductProvider([]);
        $productCalculator = new ProductCalculator([]);

        $calculator = new ProductTotalCalculator($cartProductProvider, $productCalculator);
        $cartPrice = CartPrice::create(1);

        $calculator->compute($cartPrice);

        $this->assertTrue($cartPrice->getProductTotal()->getTaxExcluded()->equalsZero());
        $this->assertTrue($cartPrice->getCartTotal()->getTaxExcluded()->equalsZero());
        $this->assertSame([], $cartPrice->getProductPrices());
    }

    public function testSingleProductComputesTotal(): void
    {
        $cartProductProvider = new MockCartProductProvider([
            1 => [
                new CartProduct(1, 0, 0, 2),
            ],
        ]);

        $productProvider = new MockProductProvider([
            '1' => new ProductPriceData(
                new DecimalNumber('29.99'),
                new DecimalNumber('5.00'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
            ),
        ]);

        $baseCalculator = new BaseProductCalculator($productProvider);
        $productCalculator = new ProductCalculator([$baseCalculator]);

        $calculator = new ProductTotalCalculator($cartProductProvider, $productCalculator);
        $cartPrice = CartPrice::create(1);

        $calculator->compute($cartPrice);

        // 29.99 * 2 = 59.98
        $this->assertTrue(
            $cartPrice->getProductTotal()->getTaxExcluded()->equals(new DecimalNumber('59.98'))
        );
        $this->assertCount(1, $cartPrice->getProductPrices());
        $this->assertSame(1, $cartPrice->getProductPrices()[0]->getProductId());
        $this->assertSame(2, $cartPrice->getProductPrices()[0]->getQuantity());
    }

    public function testMultipleProductsSumsTotals(): void
    {
        $cartProductProvider = new MockCartProductProvider([
            1 => [
                new CartProduct(1, 0, 0, 1),
                new CartProduct(2, 0, 0, 3),
            ],
        ]);

        $productProvider = new MockProductProvider([
            '1' => new ProductPriceData(
                new DecimalNumber('10'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
            ),
            '2' => new ProductPriceData(
                new DecimalNumber('20'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
            ),
        ]);

        $baseCalculator = new BaseProductCalculator($productProvider);
        $productCalculator = new ProductCalculator([$baseCalculator]);

        $calculator = new ProductTotalCalculator($cartProductProvider, $productCalculator);
        $cartPrice = CartPrice::create(1);

        $calculator->compute($cartPrice);

        // 10 * 1 + 20 * 3 = 70
        $this->assertTrue(
            $cartPrice->getProductTotal()->getTaxExcluded()->equals(new DecimalNumber('70'))
        );
        $this->assertCount(2, $cartPrice->getProductPrices());
    }

    public function testProductWithCombination(): void
    {
        $cartProductProvider = new MockCartProductProvider([
            1 => [
                new CartProduct(1, 5, 0, 1),
            ],
        ]);

        $productProvider = new MockProductProvider([
            '1-5' => new ProductPriceData(
                new DecimalNumber('100'),
                new DecimalNumber('0'),
                new DecimalNumber('15'),
                new DecimalNumber('0'),
            ),
        ]);

        $baseCalculator = new BaseProductCalculator($productProvider);
        $productCalculator = new ProductCalculator([$baseCalculator]);

        $calculator = new ProductTotalCalculator($cartProductProvider, $productCalculator);
        $cartPrice = CartPrice::create(1);

        $calculator->compute($cartPrice);

        // 100 + 15 = 115
        $this->assertTrue(
            $cartPrice->getProductTotal()->getTaxExcluded()->equals(new DecimalNumber('115'))
        );
    }

    public function testCartTotalMatchesProductTotal(): void
    {
        $cartProductProvider = new MockCartProductProvider([
            1 => [
                new CartProduct(1, 0, 0, 1),
            ],
        ]);

        $productProvider = new MockProductProvider([
            '1' => new ProductPriceData(
                new DecimalNumber('50'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
                new DecimalNumber('0'),
            ),
        ]);

        $baseCalculator = new BaseProductCalculator($productProvider);
        $productCalculator = new ProductCalculator([$baseCalculator]);

        $calculator = new ProductTotalCalculator($cartProductProvider, $productCalculator);
        $cartPrice = CartPrice::create(1);

        $calculator->compute($cartPrice);

        $this->assertTrue(
            $cartPrice->getCartTotal()->getTaxExcluded()->equals(
                $cartPrice->getProductTotal()->getTaxExcluded()
            )
        );
    }
}
