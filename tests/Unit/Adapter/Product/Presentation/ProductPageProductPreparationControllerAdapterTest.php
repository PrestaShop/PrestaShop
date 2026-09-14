<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Presentation;

use Context;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Presentation\ProductPageProductPreparationControllerAdapter;
use Product;

final class ProductPageProductPreparationControllerAdapterTest extends TestCase
{
    public function testItDelegatesLegacyExtensionPointsToCallbacks(): void
    {
        $product = new Product();
        $context = new Context();

        $adapter = new ProductPageProductPreparationControllerAdapter(
            static fn (Product $product): array => ['presented_object' => true],
            static fn (string $description, Product $product, Context $context): string => 'transformed:' . $description,
            static fn (
                array $productForPresentation,
                Product $product,
                Context $context,
                ?int $idProductAttribute,
                ?callable $combinationResolver
            ): int => 4,
            static fn (array $product, ?int $minimalQuantity): int => 2,
            static fn (int $quantityWanted, array $product, ?int $requiredQuantity): int => 7,
            static fn (Product $product, Context $context, int $combinationId): array => [
                'id_product_attribute' => $combinationId,
            ],
            static fn (array $product, Product $productObject, Context $context, ?callable $combinationResolver): float => 1.25,
            static fn (array $product, Context $context): array => [
                'presented_product' => $product,
            ],
            static function (array $productFull, Product $product, Context $context): array {
                $productFull['customization_adapter_called'] = true;

                return $productFull;
            },
            static fn (int $quantity, Context $context): string => $quantity > 1 ? 'Items' : 'Item',
            static fn (Product $product, Context $context, ?int $idProductAttribute): array => [
                ['from_quantity' => 3],
            ],
            static fn (string $title, Product $product, Context $context, ?int $idProductAttribute): string => $title . ' / combination',
        );

        self::assertSame(['presented_object' => true], $adapter->presentObject($product));
        self::assertSame(
            'transformed:description',
            $adapter->transformDescriptionWithImg('description', $product, $context)
        );
        self::assertSame(4, $adapter->getProductMinimalQuantity(['id_product_attribute' => 12], $product, $context, 12));
        self::assertSame(2, $adapter->getRequiredQuantity(['minimal_quantity' => 4, 'cart_quantity' => 2]));
        self::assertSame(7, $adapter->getWantedQuantity(1, ['minimal_quantity' => 4, 'cart_quantity' => 2]));
        self::assertSame(
            ['id_product_attribute' => 12],
            $adapter->findProductCombinationById($product, $context, 12)
        );
        self::assertSame(
            1.25,
            $adapter->getProductEcotax(['ecotax' => 1.0], $product, $context)
        );
        self::assertSame(
            ['presented_product' => ['id_product' => 1]],
            $adapter->presentProduct(['id_product' => 1], $context)
        );
        self::assertSame(
            ['id_product' => 1, 'customization_adapter_called' => true],
            $adapter->addProductCustomizationData(['id_product' => 1], $product, $context)
        );
        self::assertSame(
            [['from_quantity' => 3]],
            $adapter->getQuantityDiscounts($product, $context, 12)
        );
        self::assertSame(
            'Product / combination',
            $adapter->appendAttributesToTitle('Product', $product, $context, 12)
        );
    }
}
