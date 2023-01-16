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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Update;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\MovementReasonRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopGroupRepository;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use Product;
use StockAvailable;

/**
 * Updates settings related to Product stock
 */
class ProductStockUpdater
{
    /**
     * @var StockManager
     */
    private $stockManager;

    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @var StockAvailableMultiShopRepository
     */
    private $stockAvailableRepository;

    /**
     * @var MovementReasonRepository
     */
    private $movementReasonRepository;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var bool
     */
    private $advancedStockEnabled;

    /**
     * @var HookManager
     */
    private $hookManager;

    /**
     * @var ShopGroupRepository
     */
    private $shopGroupRepository;

    /**
     * @param StockManager $stockManager
     * @param ProductMultiShopRepository $productRepository
     * @param StockAvailableMultiShopRepository $stockAvailableRepository
     * @param MovementReasonRepository $movementReasonRepository
     * @param Configuration $configuration
     * @param HookManager $hookManager
     * @param ShopGroupRepository $shopGroupRepository
     */
    public function __construct(
        StockManager $stockManager,
        ProductMultiShopRepository $productRepository,
        StockAvailableMultiShopRepository $stockAvailableRepository,
        MovementReasonRepository $movementReasonRepository,
        Configuration $configuration,
        HookManager $hookManager,
        ShopGroupRepository $shopGroupRepository
    ) {
        $this->stockManager = $stockManager;
        $this->productRepository = $productRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->movementReasonRepository = $movementReasonRepository;
        $this->configuration = $configuration;
        $this->advancedStockEnabled = $this->configuration->getBoolean('PS_ADVANCED_STOCK_MANAGEMENT');
        $this->hookManager = $hookManager;
        $this->shopGroupRepository = $shopGroupRepository;
    }

    /**
     * @param ProductId $productId
     * @param ProductStockProperties $properties
     */
    public function update(ProductId $productId, ProductStockProperties $properties, ShopConstraint $shopConstraint): void
    {
        $product = $this->productRepository->getByShopConstraint($productId, $shopConstraint);
        // Use the shop matching the Product instance (either the specified ShopId from constraint or the Product default shop)
        $stockAvailable = $this->stockAvailableRepository->getForProduct($productId, new ShopId($product->getShopId()));

        $this->productRepository->partialUpdate(
            $product,
            $this->fillUpdatableProperties($product, $stockAvailable, $properties),
            $shopConstraint,
            CannotUpdateProductException::FAILED_UPDATE_STOCK
        );

        $this->updateStockByShopConstraint($stockAvailable, $properties, $shopConstraint);

        if ($this->advancedStockEnabled && $product->depends_on_stock) {
            StockAvailable::synchronize($product->id);
        }
    }

    /**
     * Resets product stock to zero, both Product and associated StockAvailable are reset, and a stock movement linked to
     * the employee from context is generated.
     *
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     *
     * @throws CoreException
     * @throws ProductStockException
     */
    public function resetStock(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Product has no features related with shop group use single shop and all shops constraints');
        }

        if ($shopConstraint->forAllShops()) {
            $shops = $this->productRepository->getAssociatedShopIds($productId);
        } else {
            $shops = [$shopConstraint->getShopId()];
        }

        foreach ($shops as $shopId) {
            $stockAvailable = $this->stockAvailableRepository->getForProduct($productId, $shopId);
            if ((int) $stockAvailable->quantity === 0) {
                continue;
            }

            $previousQuantity = (int) $stockAvailable->quantity;
            $stockModification = StockModification::buildFixedQuantity(0);

            // Update product
            $shopConstraint = ShopConstraint::shop($shopId->getValue());
            $product = $this->productRepository->getByShopConstraint($productId, $shopConstraint);
            $product->quantity = 0;
            $this->productRepository->partialUpdate(
                $product,
                ['quantity'],
                $shopConstraint,
                CannotUpdateProductException::FAILED_UPDATE_STOCK
            );

            // Update stock
            $stockAvailable->quantity = 0;
            $this->stockAvailableRepository->update($stockAvailable);

            // Generate stock movement related to the employee
            $this->saveMovement($stockAvailable, $stockModification, $previousQuantity, $shopId->getValue());

            // Update reserved and physical quantity for this stock
            $this->stockAvailableRepository->updatePhysicalProductQuantity(
                new StockId((int) $stockAvailable->id),
                new OrderStateId((int) $this->configuration->get('PS_OS_ERROR', null, ShopConstraint::shop((int) $stockAvailable->id_shop))),
                new OrderStateId((int) $this->configuration->get('PS_OS_CANCELED', null, ShopConstraint::shop((int) $stockAvailable->id_shop)))
            );

            if ($this->advancedStockEnabled) {
                StockAvailable::synchronize($productId->getValue(), $shopId->getValue());
            }
        }
    }

    /**
     * @param Product $product
     * @param ProductStockProperties $properties
     *
     * @return string[]|array<string, int[]>
     */
    private function fillUpdatableProperties(
        Product $product,
        StockAvailable $stockAvailable,
        ProductStockProperties $properties
    ): array {
        $updatableProperties = [];

        $localizedLaterLabels = $properties->getLocalizedAvailableLaterLabels();
        if (null !== $localizedLaterLabels) {
            $product->available_later = $localizedLaterLabels;
            $updatableProperties['available_later'] = array_keys($localizedLaterLabels);
        }

        $localizedNowLabels = $properties->getLocalizedAvailableNowLabels();
        if (null !== $localizedNowLabels) {
            $product->available_now = $localizedNowLabels;
            $updatableProperties['available_now'] = array_keys($localizedNowLabels);
        }
        if (null !== $properties->getLocation()) {
            $product->location = $properties->getLocation();
            $updatableProperties[] = 'location';
        }
        if (null !== $properties->isLowStockAlertEnabled()) {
            $product->low_stock_alert = $properties->isLowStockAlertEnabled();
            $updatableProperties[] = 'low_stock_alert';
        }
        if (null !== $properties->getLowStockThreshold()) {
            $product->low_stock_threshold = $properties->getLowStockThreshold();
            $updatableProperties[] = 'low_stock_threshold';
        }
        if (null !== $properties->getMinimalQuantity()) {
            $product->minimal_quantity = $properties->getMinimalQuantity();
            $updatableProperties[] = 'minimal_quantity';
        }
        if (null !== $properties->getOutOfStockType()) {
            $product->out_of_stock = $properties->getOutOfStockType()->getValue();
            $updatableProperties[] = 'out_of_stock';
        }
        if (null !== $properties->getPackStockType()) {
            $product->pack_stock_type = $properties->getPackStockType()->getValue();
            $updatableProperties[] = 'pack_stock_type';
        }
        if (null !== $properties->getStockModification()) {
            $product->quantity = $properties->getStockModification()->getDeltaQuantity() !== null ?
                $stockAvailable->quantity + $properties->getStockModification()->getDeltaQuantity() :
                $properties->getStockModification()->getFixedQuantity()
            ;
            $updatableProperties[] = 'quantity';
        }
        if (null !== $properties->getAvailableDate()) {
            $product->available_date = $properties->getAvailableDate()->format(DateTime::DEFAULT_DATE_FORMAT);
            $updatableProperties[] = 'available_date';
        }

        return $updatableProperties;
    }

    private function updateStockByShopConstraint(StockAvailable $stockAvailable, ProductStockProperties $properties, ShopConstraint $shopConstraint): void
    {
        if ($shopConstraint->forAllShops()) {
            // Since each stock has a distinct ID we can't use the ObjectModel multi shop feature based on id_shop_list,
            // so we manually loop to update each associated stocks
            $stockIds = $this->stockAvailableRepository->getAllShopsStockIds(
                new ProductId((int) $stockAvailable->id_product),
                (int) $stockAvailable->id_product_attribute === NoCombinationId::NO_COMBINATION_ID ? new NoCombinationId() : new CombinationId((int) $stockAvailable->id_product_attribute)
            );

            foreach ($stockIds as $stockId) {
                $shopStockAvailable = $this->stockAvailableRepository->get($stockId);
                $this->updateStockAvailable($shopStockAvailable, $properties);
            }
        } else {
            $this->updateStockAvailable($stockAvailable, $properties);
        }
    }

    private function updateStockAvailable(StockAvailable $stockAvailable, ProductStockProperties $properties)
    {
        $stockUpdateRequired = false;
        $previousQuantity = (int) $stockAvailable->quantity;

        if (null !== $properties->getOutOfStockType()) {
            $stockAvailable->out_of_stock = $properties->getOutOfStockType()->getValue();
            $stockUpdateRequired = true;
        }
        if (null !== $properties->getLocation()) {
            $stockAvailable->location = $properties->getLocation();
            $stockUpdateRequired = true;
        }

        if ($properties->getStockModification()) {
            $stockAvailable->quantity = $properties->getStockModification()->getDeltaQuantity() !== null ?
                $stockAvailable->quantity + $properties->getStockModification()->getDeltaQuantity() :
                $properties->getStockModification()->getFixedQuantity()
            ;
            $stockUpdateRequired = true;
        }

        if (!$stockUpdateRequired) {
            return;
        }

        $this->stockAvailableRepository->update($stockAvailable);

        if ($properties->getStockModification()) {
            if ($stockAvailable->getShopId()) {
                $shopId = $stockAvailable->getShopId();
            } else {
                // We can use any shop from the shop group, the stock movement will correctly associate the proper StockAvailable based on the shopId only
                $shopsFromGroup = $this->shopGroupRepository->getShopsFromGroup(new ShopGroupId((int) $stockAvailable->id_shop_group));
                $shopId = reset($shopsFromGroup)->getValue();
            }

            //Save movement only after stock has been updated
            $this->saveMovement($stockAvailable, $properties->getStockModification(), $previousQuantity, $shopId);

            // Update reserved and physical quantity for this stock
            $shopConstraint = ShopConstraint::shop($shopId);
            $this->stockAvailableRepository->updatePhysicalProductQuantity(
                new StockId((int) $stockAvailable->id),
                new OrderStateId((int) $this->configuration->get('PS_OS_ERROR', null, $shopConstraint)),
                new OrderStateId((int) $this->configuration->get('PS_OS_CANCELED', null, $shopConstraint))
            );
        }
    }

    /**
     * @param StockAvailable $stockAvailable
     * @param StockModification $stockModification
     * @param int $previousQuantity
     * @param int $affectedShopId
     */
    private function saveMovement(StockAvailable $stockAvailable, StockModification $stockModification, int $previousQuantity, int $affectedShopId): void
    {
        if ($stockModification->getDeltaQuantity()) {
            $deltaQuantity = $stockModification->getDeltaQuantity();
        } else {
            $deltaQuantity = $stockModification->getFixedQuantity() - $previousQuantity;
        }

        $movementReasonId = $this->movementReasonRepository->getEmployeeEditionReasonId($deltaQuantity > 0);

        $this->stockManager->saveMovement(
            $stockAvailable->id_product,
            $stockAvailable->id_product_attribute,
            $deltaQuantity,
            [
                'id_stock_mvt_reason' => $movementReasonId->getValue(),
                'id_shop' => (int) $affectedShopId,
            ]
        );

        $this->hookManager->exec(
            'actionUpdateQuantity',
            [
                'id_product' => $stockAvailable->id_product,
                'id_product_attribute' => $stockAvailable->id_product_attribute,
                'quantity' => $stockAvailable->quantity,
                'delta_quantity' => $deltaQuantity,
                'id_shop' => (int) $affectedShopId,
            ]
        );
    }
}
