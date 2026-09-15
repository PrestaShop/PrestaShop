<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Presentation;

use Context;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Presentation\ProductPageProductPreparation;
use PrestaShop\PrestaShop\Adapter\Product\Presentation\ProductQuantityDiscountProvider;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Pricing\Product\Calculator\ProductCalculatorInterface;
use Product;

final class ProductPageProductPreparationTest extends TestCase
{
    private ProductPageProductPreparation $preparation;

    protected function setUp(): void
    {
        parent::setUp();

        $featureFlagStateChecker = $this->createMock(FeatureFlagStateCheckerInterface::class);
        $productCalculator = $this->createMock(ProductCalculatorInterface::class);

        $quantityDiscountProvider = new ProductQuantityDiscountProvider(
            $featureFlagStateChecker,
            $productCalculator,
        );

        $this->preparation = new ProductPageProductPreparation($quantityDiscountProvider);
    }

    public function testItUsesProductMinimalQuantityWithoutCombination(): void
    {
        $product = new Product();
        $product->minimal_quantity = 5;

        $minimalQuantity = $this->preparation->getProductMinimalQuantity(
            ['id_product_attribute' => null],
            $product,
            new Context(),
            null,
        );

        self::assertSame(5, $minimalQuantity);
    }

    public function testItUsesCombinationResolverForMinimalQuantity(): void
    {
        $product = new Product();
        $resolverCalledWith = null;

        $minimalQuantity = $this->preparation->getProductMinimalQuantity(
            ['id_product_attribute' => 42],
            $product,
            new Context(),
            42,
            static function (int $combinationId) use (&$resolverCalledWith): array {
                $resolverCalledWith = $combinationId;

                return [
                    'minimal_quantity' => 4,
                ];
            },
        );

        self::assertSame(42, $resolverCalledWith);
        self::assertSame(4, $minimalQuantity);
    }

    public function testItComputesRequiredAndWantedQuantityLikeProductController(): void
    {
        $product = [
            'minimal_quantity' => 5,
            'cart_quantity' => 3,
        ];

        $requiredQuantity = $this->preparation->getRequiredQuantity($product);
        $wantedQuantity = $this->preparation->getWantedQuantity(1, $product, $requiredQuantity);

        self::assertSame(2, $requiredQuantity);
        self::assertSame(2, $wantedQuantity);
    }

    public function testWantedQuantityKeepsExplicitHigherQuantity(): void
    {
        $product = [
            'minimal_quantity' => 5,
            'cart_quantity' => 3,
        ];

        $requiredQuantity = $this->preparation->getRequiredQuantity($product);
        $wantedQuantity = $this->preparation->getWantedQuantity(8, $product, $requiredQuantity);

        self::assertSame(2, $requiredQuantity);
        self::assertSame(8, $wantedQuantity);
    }
}
