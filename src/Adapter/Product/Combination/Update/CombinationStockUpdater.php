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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\MovementReasonRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;
use StockAvailable;

/**
 * Updates stock for product combination
 */
class CombinationStockUpdater
{
    /**
     * @var StockAvailableMultiShopRepository
     */
    private $stockAvailableRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var MovementReasonRepository
     */
    private $movementReasonRepository;

    /**
     * @var StockManager
     */
    private $stockManager;

    /**
     * @var ShopConfigurationInterface
     */
    private $configuration;

    public function __construct(
        StockAvailableMultiShopRepository $stockAvailableRepository,
        CombinationRepository $combinationRepository,
        MovementReasonRepository $movementReasonRepository,
        StockManager $stockManager,
        ShopConfigurationInterface $configuration
    ) {
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->combinationRepository = $combinationRepository;
        $this->stockManager = $stockManager;
        $this->configuration = $configuration;
        $this->movementReasonRepository = $movementReasonRepository;
    }

    /**
     * @param CombinationId $combinationId
     * @param CombinationStockProperties $properties
     */
    public function update(
        CombinationId $combinationId,
        CombinationStockProperties $properties,
        ShopConstraint $shopConstraint
    ): void {
        $combination = $this->combinationRepository->getByShopConstraint($combinationId, $shopConstraint);
        $this->combinationRepository->partialUpdate(
            $combination,
            $this->fillUpdatableProperties($combination, $properties),
            $shopConstraint,
            CannotUpdateCombinationException::FAILED_UPDATE_STOCK
        );

        $this->updateStockByShopConstraint(
            $combination,
            $properties,
            $shopConstraint
        );
    }

    /**
     * @param Combination $combination
     * @param CombinationStockProperties $properties
     *
     * @return string[]
     */
    private function fillUpdatableProperties(Combination $combination, CombinationStockProperties $properties): array
    {
        $updatableProperties = [];

        $localizedLaterLabels = $properties->getLocalizedAvailableLaterLabels();
        if (null !== $localizedLaterLabels) {
            $combination->available_later = $localizedLaterLabels;
            $updatableProperties['available_later'] = array_keys($localizedLaterLabels);
        }

        $localizedNowLabels = $properties->getLocalizedAvailableNowLabels();
        if (null !== $localizedNowLabels) {
            $combination->available_now = $localizedNowLabels;
            $updatableProperties['available_now'] = array_keys($localizedNowLabels);
        }

        if (null !== $properties->getAvailableDate()) {
            $combination->available_date = $properties->getAvailableDate()->format(DateTime::DEFAULT_DATE_FORMAT);
            $updatableProperties[] = 'available_date';
        }

        if (null !== $properties->getLowStockThreshold()) {
            $combination->low_stock_threshold = $properties->getLowStockThreshold();
            $updatableProperties[] = 'low_stock_threshold';
        }

        if (null !== $properties->getMinimalQuantity()) {
            $combination->minimal_quantity = $properties->getMinimalQuantity();
            $updatableProperties[] = 'minimal_quantity';
        }

        if (null !== $properties->isLowStockAlertEnabled()) {
            $combination->low_stock_alert = $properties->isLowStockAlertEnabled();
            $updatableProperties[] = 'low_stock_alert';
        }

        return $updatableProperties;
    }

    /**
     * @param StockAvailable $stockAvailable
     * @param CombinationStockProperties $properties
     */
    private function updateStockAvailable(StockAvailable $stockAvailable, CombinationStockProperties $properties): void
    {
        $updateLocation = null !== $properties->getLocation();
        $stockModification = $properties->getStockModification();

        if (!$stockModification && !$updateLocation) {
            return;
        }

        if ($stockModification) {
            $previousQuantity = (int) $stockAvailable->quantity;
            if (null !== $stockModification->getDeltaQuantity()) {
                $stockAvailable->quantity += $stockModification->getDeltaQuantity();
            } else {
                $stockAvailable->quantity = $stockModification->getFixedQuantity();
            }
        }

        if ($updateLocation) {
            $stockAvailable->location = $properties->getLocation();
        }

        $fallbackShopId = $this->stockAvailableRepository->getFallbackShopId($stockAvailable);
        $this->stockAvailableRepository->update($stockAvailable, $fallbackShopId);

        // save movement only after stockAvailable has been updated
        if ($stockModification) {
            $this->saveMovement($stockAvailable, $stockModification, $previousQuantity, $fallbackShopId->getValue());

            // Update reserved and physical quantity for this stock
            $shopConstraint = ShopConstraint::shop($fallbackShopId->getValue());
            $this->stockAvailableRepository->updatePhysicalProductQuantity(
                new StockId((int) $stockAvailable->id),
                new OrderStateId((int) $this->configuration->get('PS_OS_ERROR', null, $shopConstraint)),
                new OrderStateId((int) $this->configuration->get('PS_OS_CANCELED', null, $shopConstraint))
            );
        }
    }

    private function saveMovement(StockAvailable $stockAvailable, StockModification $stockModification, int $previousQuantity, int $affectedShopId): void
    {
        if (null !== $stockModification->getDeltaQuantity()) {
            $deltaQuantity = $stockModification->getDeltaQuantity();
        } else {
            $deltaQuantity = $stockModification->getFixedQuantity() - $previousQuantity;
        }

        $movementReasonId = $this->movementReasonRepository->getEmployeeEditionReasonId($deltaQuantity > $previousQuantity);

        $this->stockManager->saveMovement(
            $stockAvailable->id_product,
            $stockAvailable->id_product_attribute,
            $deltaQuantity,
            [
                'id_stock_mvt_reason' => $movementReasonId->getValue(),
                'id_shop' => (int) $affectedShopId,
            ]
        );
    }

    private function updateStockByShopConstraint(
        Combination $combination,
        CombinationStockProperties $properties,
        ShopConstraint $shopConstraint
    ): void {
        $combinationId = new CombinationId((int) $combination->id);
        if ($shopConstraint->forAllShops()) {
            // Since each stock has a distinct ID we can't use the ObjectModel multi shop feature based on id_shop_list,
            // so we manually loop to update each associated stocks
            $shops = $this->combinationRepository->getAssociatedShopIds($combinationId);
            foreach ($shops as $shopId) {
                $this->updateStockAvailable(
                    $this->stockAvailableRepository->getForCombination($combinationId, $shopId),
                    $properties
                );
            }
        } else {
            $this->updateStockAvailable(
                $this->stockAvailableRepository->getForCombination($combinationId, new ShopId($combination->getShopId())),
                $properties
            );
        }
    }
}
