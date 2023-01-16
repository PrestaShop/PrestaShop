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

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockUpdater;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierReferenceId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
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
     * @var ProductImageMultiShopRepository
     */
    private $productImageMultiShopRepository;

    /**
     * @var ProductStockUpdater
     */
    private $productStockUpdater;

    /**
     * @var CombinationMultiShopRepository
     */
    private $combinationRepository;

    /**
     * @var CombinationStockUpdater
     */
    private $combinationStockUpdater;

    public function __construct(
        ProductMultiShopRepository $productRepository,
        StockAvailableMultiShopRepository $stockAvailableRepository,
        ShopRepository $shopRepository,
        ProductImageMultiShopRepository $productImageMultiShopRepository,
        ProductStockUpdater $productStockUpdater,
        CombinationMultiShopRepository $combinationMultiShopRepository,
        CombinationStockUpdater $combinationStockUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->shopRepository = $shopRepository;
        $this->productImageMultiShopRepository = $productImageMultiShopRepository;
        $this->productStockUpdater = $productStockUpdater;
        $this->combinationRepository = $combinationMultiShopRepository;
        $this->combinationStockUpdater = $combinationStockUpdater;
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

        $this->copyStockToShop($productId, $sourceShopId, $targetShopId, $sourceProduct->getProductType());
        $this->copyCarriersToShop($sourceProduct, $targetShopId);
        $this->copyImageAssociations($productId, $sourceShopId, $targetShopId);
    }

    private function copyStockToShop(ProductId $productId, ShopId $sourceShopId, ShopId $targetShopId, string $productType): void
    {
        // First get the source stock
        $sourceStock = $this->stockAvailableRepository->getForProduct($productId, $sourceShopId);
        $outOfStock = new OutOfStockType((int) $sourceStock->out_of_stock);

        // Then try to get the target stock, if it doesn't exist create it
        try {
            $targetStock = $this->stockAvailableRepository->getForProduct($productId, $targetShopId);
        } catch (StockAvailableNotFoundException $e) {
            $targetStock = $this->stockAvailableRepository->createStockAvailable($productId, $targetShopId);
        }

        $deltaQuantity = (int) $sourceStock->quantity - (int) $targetStock->quantity;
        if ($deltaQuantity !== 0 || (int) $sourceStock->out_of_stock !== (int) $targetStock->out_of_stock || $sourceStock->location !== $targetStock->location) {
            $stockModification = StockModification::buildFixedQuantity((int) $sourceStock->quantity);
            $stockProperties = new ProductStockProperties(
                null,
                $stockModification,
                $outOfStock,
                null,
                $sourceStock->location
            );
            $this->productStockUpdater->update($productId, $stockProperties, ShopConstraint::shop($targetShopId->getValue()));
        }

        if ($productType === ProductType::TYPE_COMBINATIONS) {
            $this->copyCombinationsStockToShop($productId, $sourceShopId, $targetShopId, $outOfStock);
        }
    }

    private function copyCombinationsStockToShop(ProductId $productId, ShopId $sourceShopId, ShopId $targetShopId, OutOfStockType $outOfStockType): void
    {
        $sourceCombinations = $this->combinationRepository->getCombinationIds($productId, ShopConstraint::shop($targetShopId->getValue()));
        $targetConstraint = ShopConstraint::shop($targetShopId->getValue());

        foreach ($sourceCombinations as $combinationId) {
            // First get the source stock
            $sourceStock = $this->stockAvailableRepository->getForCombination($combinationId, $sourceShopId);

            // Then try to get the target stock, if it doesn't exist create it
            try {
                $targetStock = $this->stockAvailableRepository->getForCombination($combinationId, $targetShopId);
            } catch (StockAvailableNotFoundException $e) {
                $targetStock = $this->stockAvailableRepository->createStockAvailable($productId, $targetShopId, $combinationId);
            }

            $deltaQuantity = (int) $sourceStock->quantity - (int) $targetStock->quantity;
            if ($deltaQuantity !== 0) {
                $stockModification = StockModification::buildDeltaQuantity($deltaQuantity);
                $stockProperties = new CombinationStockProperties(
                    $stockModification,
                    null,
                    $sourceStock->location
                );
                $this->combinationStockUpdater->update($combinationId, $stockProperties, $targetConstraint);
            }
        }
        $this->combinationRepository->updateCombinationOutOfStockType($productId, $outOfStockType, $targetConstraint);
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

    /**
     * @param ProductId $productId
     * @param ShopId $sourceShopId
     * @param ShopId $targetShopId
     *
     * @return void
     *
     * @throws \PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException
     * @throws \PrestaShop\PrestaShop\Core\Exception\CoreException
     */
    private function copyImageAssociations(ProductId $productId, ShopId $sourceShopId, ShopId $targetShopId): void
    {
        $imagesFromSourceShop = $this->productImageMultiShopRepository->getImages($productId, ShopConstraint::shop($sourceShopId->getValue()));
        foreach ($imagesFromSourceShop as $image) {
            $image->associateTo($targetShopId->getValue(), $productId->getValue());
        }
    }
}
