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

namespace PrestaShop\PrestaShop\Adapter\Product\Update;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use Product;

/**
 * Provides methods to update packed products
 */
class PackedProductsUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * @param ProductId $productId
     * @param ProductId[] $packedProductIds
     */
    public function setPackedProducts(ProductId $productId, array $packedProductIds): void
    {
        $product = $this->productRepository->get($productId);

        if (empty($relatedProductIds)) {
            $this->deletePackedProducts($product);

            return;
        }
        $this->productRepository->assertAllProductsExists($packedProductIds);
        $this->deletePackedProducts($product);
        $this->insertPackedProducts($product, $packedProductIds);
    }

    /**
     * @param Product $product
     * @param array $packedProductIds
     *
     * @throws CoreException
     */
    private function insertPackedProducts(Product $product, array $packedProductIds): void
    {
        $ids = array_map(function (ProductId $productId): int {
            return $productId->getValue();
        }, $packedProductIds);

        try {
            $product->changeAccessories($ids);
        } catch (PrestaShopException $e) {
            throw new CoreException(sprintf(
                'Error occurred when updating packed products for product #%d',
                $product->id
            ));
        }
    }

    /**
     * @param Product $product
     *
     * @throws CannotUpdateProductException
     * @throws CoreException
     */
    private function deletePackedProducts(Product $product): void
    {
        try {
            if (!$product->deletePack()) {
                throw new CannotUpdateProductException(sprintf(
                    'Failed to delete packed products for product #%d',
                    $product->id
                ));
            }
        } catch (PrestaShopException $e) {
            throw new CoreException(sprintf(
                'Error occurred when updating packed products for product #%d',
                $product->id
            ));
        }
    }
}
