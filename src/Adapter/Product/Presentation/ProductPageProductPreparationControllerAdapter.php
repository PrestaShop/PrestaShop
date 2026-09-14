<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Presentation;

use Closure;
use Context;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use Product;

/**
 * Adapts ProductController legacy extension points without duplicating preparation algorithms.
 *
 * @internal
 */
final class ProductPageProductPreparationControllerAdapter implements ProductPageProductPreparationInterface
{
    public function __construct(
        private readonly Closure $presentObject,
        private readonly Closure $transformDescriptionWithImg,
        private readonly Closure $getProductMinimalQuantity,
        private readonly Closure $getRequiredQuantity,
        private readonly Closure $getWantedQuantity,
        private readonly Closure $findProductCombinationById,
        private readonly Closure $getProductEcotax,
        private readonly Closure $presentProduct,
        private readonly Closure $addProductCustomizationData,
        private readonly Closure $getQuantityLabel,
        private readonly Closure $getQuantityDiscounts,
        private readonly Closure $appendAttributesToTitle,
    ) {
    }

    public function presentObject(Product $product)
    {
        return ($this->presentObject)($product);
    }

    public function transformDescriptionWithImg(
        string $description,
        Product $product,
        Context $context,
    ) {
        return ($this->transformDescriptionWithImg)($description, $product, $context);
    }

    public function getProductMinimalQuantity(
        ProductLazyArray|array $productForPresentation,
        Product $product,
        Context $context,
        ?int $idProductAttribute,
        ?callable $combinationResolver = null,
    ) {
        return ($this->getProductMinimalQuantity)(
            $productForPresentation,
            $product,
            $context,
            $idProductAttribute,
            $combinationResolver
        );
    }

    public function getRequiredQuantity(
        ProductLazyArray|array $product,
        ?int $minimalQuantity = null,
    ) {
        return ($this->getRequiredQuantity)($product, $minimalQuantity);
    }

    public function getWantedQuantity(
        int $quantityWanted,
        ProductLazyArray|array $product,
        ?int $requiredQuantity = null,
    ) {
        return ($this->getWantedQuantity)($quantityWanted, $product, $requiredQuantity);
    }

    public function findProductCombinationById(
        Product $product,
        Context $context,
        int $combinationId,
    ) {
        return ($this->findProductCombinationById)($product, $context, $combinationId);
    }

    public function getProductEcotax(
        array $product,
        Product $productObject,
        Context $context,
        ?callable $combinationResolver = null,
    ) {
        return ($this->getProductEcotax)($product, $productObject, $context, $combinationResolver);
    }

    public function getQuantityLabel(
        int $quantity,
        Context $context,
    ): string {
        return ($this->getQuantityLabel)($quantity, $context);
    }

    public function getQuantityDiscounts(
        Product $product,
        Context $context,
        ?int $idProductAttribute,
    ): ?array {
        return ($this->getQuantityDiscounts)($product, $context, $idProductAttribute);
    }

    public function appendAttributesToTitle(
        string $title,
        Product $product,
        Context $context,
        ?int $idProductAttribute,
    ): string {
        return ($this->appendAttributesToTitle)($title, $product, $context, $idProductAttribute);
    }

    public function presentProduct(
        array $product,
        Context $context,
    ) {
        return ($this->presentProduct)($product, $context);
    }

    public function addProductCustomizationData(
        array $productFull,
        Product $product,
        Context $context,
    ) {
        return ($this->addProductCustomizationData)($productFull, $product, $context);
    }
}
