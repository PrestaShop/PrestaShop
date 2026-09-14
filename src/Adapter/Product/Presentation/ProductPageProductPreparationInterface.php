<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Presentation;

use Context;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray;
use Product;

interface ProductPageProductPreparationInterface
{
    public function presentObject(Product $product);

    public function transformDescriptionWithImg(
        string $description,
        Product $product,
        Context $context,
    );

    public function getProductMinimalQuantity(
        ProductLazyArray|array $productForPresentation,
        Product $product,
        Context $context,
        ?int $idProductAttribute,
        ?callable $combinationResolver = null,
    );

    public function getRequiredQuantity(
        ProductLazyArray|array $product,
        ?int $minimalQuantity = null,
    );

    public function getWantedQuantity(
        int $quantityWanted,
        ProductLazyArray|array $product,
        ?int $requiredQuantity = null,
    );

    /**
     * @return array<string, mixed>|null
     */
    public function findProductCombinationById(
        Product $product,
        Context $context,
        int $combinationId,
    );

    public function getProductEcotax(
        array $product,
        Product $productObject,
        Context $context,
        ?callable $combinationResolver = null,
    );

    public function presentProduct(
        array $product,
        Context $context,
    );

    public function addProductCustomizationData(
        array $productFull,
        Product $product,
        Context $context,
    );

    public function getQuantityLabel(
        int $quantity,
        Context $context,
    ): string;

    public function getQuantityDiscounts(
        Product $product,
        Context $context,
        ?int $idProductAttribute,
    ): ?array;

    public function appendAttributesToTitle(
        string $title,
        Product $product,
        Context $context,
        ?int $idProductAttribute,
    ): string;
}
