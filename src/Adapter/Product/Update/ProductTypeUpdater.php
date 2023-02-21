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

use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationDeleter;
use PrestaShop\PrestaShop\Adapter\Product\Pack\Repository\ProductPackRepository;
use PrestaShop\PrestaShop\Adapter\Product\Pack\Update\ProductPackUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockUpdater;
use PrestaShop\PrestaShop\Adapter\Product\VirtualProduct\Update\VirtualProductUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductTypeException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

class ProductTypeUpdater
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductPackUpdater
     */
    private $productPackUpdater;

    /**
     * @var CombinationDeleter
     */
    private $combinationDeleter;

    /**
     * @var VirtualProductUpdater
     */
    private $virtualProductUpdater;

    /**
     * @var ProductStockUpdater
     */
    private $productStockUpdater;

    /**
     * @var ProductPackRepository
     */
    private $productPackRepository;

    /**
     * @param ProductRepository $productRepository
     * @param ProductPackUpdater $productPackUpdater
     * @param CombinationDeleter $combinationDeleter
     * @param VirtualProductUpdater $virtualProductUpdater
     * @param ProductStockUpdater $productStockUpdater
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductPackUpdater $productPackUpdater,
        CombinationDeleter $combinationDeleter,
        VirtualProductUpdater $virtualProductUpdater,
        ProductStockUpdater $productStockUpdater,
        ProductPackRepository $productPackRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productPackUpdater = $productPackUpdater;
        $this->combinationDeleter = $combinationDeleter;
        $this->virtualProductUpdater = $virtualProductUpdater;
        $this->productStockUpdater = $productStockUpdater;
        $this->productPackRepository = $productPackRepository;
    }

    public function updateType(ProductId $productId, ProductType $productType): void
    {
        $this->checkExistingPackAssociation($productId, $productType);

        $product = $this->productRepository->getProductByDefaultShop($productId);

        // First remove the associations before the type is updated (since these actions are only allowed for a certain type)
        if ($product->product_type === ProductType::TYPE_PACK && $productType->getValue() !== ProductType::TYPE_PACK) {
            $this->productPackUpdater->setPackProducts(new PackId($productId->getValue()), []);
        }
        if ($product->product_type === ProductType::TYPE_COMBINATIONS && $productType->getValue() !== ProductType::TYPE_COMBINATIONS) {
            // When we change the combination type we must reset the stock since all combinations are removed, it must be done before
            // removing all combinations, because the Combination legacy object performs this reset internally, so we won't have the data
            // anymore to create the appropriate stock movement
            $this->resetProductStock($productId);

            $this->combinationDeleter->deleteAllProductCombinations($productId, ShopConstraint::allShops());
        }
        if ($product->product_type === ProductType::TYPE_VIRTUAL && $productType->getValue() !== ProductType::TYPE_VIRTUAL) {
            $this->virtualProductUpdater->deleteFileForProduct($productId);
        }
        // Finally, update product type
        $updatedProperties = [
            'product_type',
            'is_virtual',
            'cache_is_pack',
        ];

        // When a product is converted TO product with combinations the stock is reset
        $resetProductStock = $product->product_type !== ProductType::TYPE_COMBINATIONS && $productType->getValue() === ProductType::TYPE_COMBINATIONS;

        $product->product_type = $productType->getValue();
        $product->is_virtual = ProductType::TYPE_VIRTUAL === $productType->getValue();
        $product->cache_is_pack = ProductType::TYPE_PACK === $productType->getValue();
        if ($productType->getValue() !== ProductType::TYPE_COMBINATIONS) {
            $product->cache_default_attribute = 0;
            $updatedProperties[] = 'cache_default_attribute';
        }
        // Virtual product cannot have ecotax
        if ($productType->getValue() === ProductType::TYPE_VIRTUAL && !empty($product->ecotax)) {
            $product->price += $product->ecotax;
            $product->ecotax = 0;
            $updatedProperties[] = 'ecotax';
            $updatedProperties[] = 'price';
        }

        $this->productRepository->partialUpdate(
            $product,
            $updatedProperties,
            ShopConstraint::shop($this->productRepository->getProductDefaultShopId($productId)->getValue()),
            CannotUpdateProductException::FAILED_UPDATE_TYPE
        );

        if ($resetProductStock) {
            $this->resetProductStock($productId);
        }
    }

    private function resetProductStock(ProductId $productId): void
    {
        // Product type is bound to all shops, so when we reset stock because of type change it must be applied to all associated shops
        $this->productStockUpdater->resetStock($productId, ShopConstraint::allShops());
    }

    private function checkExistingPackAssociation(ProductId $productId, ProductType $productType): void
    {
        if ($productType->getValue() !== ProductType::TYPE_PACK) {
            return;
        }

        $packsAssociatedToProduct = $this->productPackRepository->getPacksContaining($productId);
        if (!empty($packsAssociatedToProduct)) {
            throw new InvalidProductTypeException(
                InvalidProductTypeException::EXPECTED_NO_EXISTING_PACK_ASSOCIATIONS,
                'You cannot change this product into a pack because it is already associated as a pack content'
            );
        }
    }
}
