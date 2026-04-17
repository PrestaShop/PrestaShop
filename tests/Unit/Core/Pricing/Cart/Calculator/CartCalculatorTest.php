<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Pricing\Cart\Calculator;

use Closure;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Calculator\CartCalculator;
use PrestaShop\PrestaShop\Core\Pricing\Cart\Calculator\CartCalculatorInterface;
use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPrice;
use PrestaShop\PrestaShop\Core\Pricing\Cart\CartPriceInterface;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxablePrice;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class CartCalculatorTest extends TestCase
{
    public function testIteratesCalculatorsInOrder(): void
    {
        $executionOrder = [];

        $calculator1 = $this->createCalculator(function () use (&$executionOrder) {
            $executionOrder[] = 'first';
        });
        $calculator2 = $this->createCalculator(function () use (&$executionOrder) {
            $executionOrder[] = 'second';
        });

        $cartCalculator = new CartCalculator([$calculator1, $calculator2]);
        $cartPrice = CartPrice::create(1);

        $cartCalculator->compute($cartPrice);

        $this->assertSame(['first', 'second'], $executionOrder);
    }

    public function testEmptyPipelineLeavesCartPriceUnchanged(): void
    {
        $calculator = new CartCalculator([]);
        $cartPrice = CartPrice::create(1);

        $calculator->compute($cartPrice);

        $this->assertTrue($cartPrice->getProductTotal()->getTaxExcluded()->equalsZero());
        $this->assertTrue($cartPrice->getCartTotal()->getTaxExcluded()->equalsZero());
    }

    public function testCalculatorsMutateCartPrice(): void
    {
        $calculator = $this->createCalculator(function (CartPriceInterface $cp) {
            $cp->setProductTotal(TaxablePrice::fromTaxExcluded(new DecimalNumber('42'), TaxRate::zero()));
        });

        $cartCalculator = new CartCalculator([$calculator]);
        $cartPrice = CartPrice::create(1);

        $cartCalculator->compute($cartPrice);

        $this->assertTrue($cartPrice->getProductTotal()->getTaxExcluded()->equals(new DecimalNumber('42')));
    }

    private function createCalculator(callable $callback): CartCalculatorInterface
    {
        return new class($callback) implements CartCalculatorInterface {
            public function __construct(private readonly Closure $callback)
            {
            }

            public function compute(CartPriceInterface $cartPrice): void
            {
                ($this->callback)($cartPrice);
            }
        };
    }
}
