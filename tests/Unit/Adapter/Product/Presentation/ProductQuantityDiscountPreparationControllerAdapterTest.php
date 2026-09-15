<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Presentation;

use Context;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Presentation\ProductQuantityDiscountPreparationControllerAdapter;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculatorInterface;

final class ProductQuantityDiscountPreparationControllerAdapterTest extends TestCase
{
    public function testItDelegatesLegacyQuantityDiscountExtensionPoints(): void
    {
        $calculator = $this->createMock(ProductCalculatorInterface::class);
        $context = new Context();

        $formatCalled = false;
        $newPricingCalled = false;

        $adapter = new ProductQuantityDiscountPreparationControllerAdapter(
            static fn (): ProductCalculatorInterface => $calculator,
            static function (
                array $specificPrices,
                float $price,
                float $taxRate,
                float $ecotaxAmount,
                Context $callbackContext,
            ) use (&$formatCalled, $context): array {
                $formatCalled = true;

                self::assertSame($context, $callbackContext);
                self::assertSame([['from_quantity' => 2]], $specificPrices);
                self::assertSame(10.0, $price);
                self::assertSame(20.0, $taxRate);
                self::assertSame(1.5, $ecotaxAmount);

                return [['formatted' => true]];
            },
            static function () use (&$newPricingCalled): bool {
                $newPricingCalled = true;

                return false;
            },
        );

        self::assertSame($calculator, $adapter->getProductCalculator());
        self::assertSame(
            [['formatted' => true]],
            $adapter->formatQuantityDiscounts(
                [['from_quantity' => 2]],
                10.0,
                20.0,
                1.5,
                $context,
            )
        );
        self::assertFalse($adapter->isNewPricingEnabled());

        self::assertTrue($formatCalled);
        self::assertTrue($newPricingCalled);
    }
}
