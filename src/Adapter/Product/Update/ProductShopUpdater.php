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

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierReferenceId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use Product;

/**
 * Class ProductShopUpdater
 */
class ProductShopUpdater
{
    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @var StockAvailableMultiShopRepository
     */
    private $stockAvailableRepository;

    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @param ProductMultiShopRepository $productRepository
     * @param StockAvailableMultiShopRepository $stockAvailableRepository
     * @param ShopRepository $shopRepository
     */
    public function __construct(
        ProductMultiShopRepository $productRepository,
        StockAvailableMultiShopRepository $stockAvailableRepository,
        ShopRepository $shopRepository
    ) {
        $this->productRepository = $productRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->shopRepository = $shopRepository;
    }

    /**
     * @param ProductId $productId
     * @param ShopId $sourceShopId
     * @param ShopId $targetShopId
     */
    public function copyToShop(ProductId $productId, ShopId $sourceShopId, ShopId $targetShopId): void
    {
        $this->shopRepository->assertShopExists($sourceShopId);
        $this->shopRepository->assertShopExists($targetShopId);

        /** @var Product $sourceProduct */
        $sourceProduct = $this->productRepository->getByShopConstraint($productId, ShopConstraint::shop($sourceShopId->getValue()));
        $this->productRepository->update(
            $sourceProduct,
            ShopConstraint::shop($targetShopId->getValue()),
            CannotUpdateProductException::FAILED_SHOP_COPY
        );

        $this->copyStockToShop($productId, $sourceShopId, $targetShopId);
        $this->copyCarriersToShop($sourceProduct, $targetShopId);
    }

    private function copyStockToShop(ProductId $productId, ShopId $sourceShopId, ShopId $targetShopId): void
    {
        // First get the source stock
        $sourceStock = $this->stockAvailableRepository->getForProduct($productId, $sourceShopId);

        // Then try to get the target stock, if it doesn't exist create it
        try {
            $targetStock = $this->stockAvailableRepository->getForProduct($productId, $targetShopId);
        } catch (StockAvailableNotFoundException $e) {
            $targetStock = $this->stockAvailableRepository->createProductStock($productId, $targetShopId);
        }

        // Copy source data to target
        $targetStock->quantity = (int) $sourceStock->quantity;
        $targetStock->location = $sourceStock->location;
        $targetStock->out_of_stock = (int) $sourceStock->out_of_stock;
        $targetStock->depends_on_stock = (bool) $sourceStock->depends_on_stock;

        // These fields are not accessible via the Object but they probably should once we clean this part
        // $targetStock->physical_quantity = $sourceStock->physical_quantity;
        // $targetStock->reserved_quantity = $sourceStock->reserved_quantity;

        $this->stockAvailableRepository->update($targetStock);
    }

    /**
     * @param Product $sourceProduct
     * @param ShopId $targetShopId
     */
    private function copyCarriersToShop(Product $sourceProduct, ShopId $targetShopId): void
    {
        $sourceCarriers = $sourceProduct->getCarriers();
        $sourceCarrierReferences = [];
        foreach ($sourceCarriers as $sourceCarrier) {
            $sourceCarrierReferences[] = new CarrierReferenceId((int) $sourceCarrier['id_reference']);
        }

        $this->productRepository->setCarrierReferences(
            new ProductId($sourceProduct->id),
            $sourceCarrierReferences,
            ShopConstraint::shop($targetShopId->getValue())
        );
    }
}
