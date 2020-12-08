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

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Stock\StockManager;
use PrestaShopException;

/**
 * Updates stock for product combination
 */
class CombinationStockUpdater
{
    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var StockManager
     */
    private $stockManager;

    /**
     * @param StockAvailableRepository $stockAvailableRepository
     * @param CombinationRepository $combinationRepository
     * @param StockManager $stockManager
     */
    public function __construct(
        StockAvailableRepository $stockAvailableRepository,
        CombinationRepository $combinationRepository,
        StockManager $stockManager
    ) {
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->combinationRepository = $combinationRepository;
        $this->stockManager = $stockManager;
    }

    /**
     * @param Combination $combination
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     */
    public function update(Combination $combination, array $propertiesToUpdate, bool $addMovement): void
    {
        $this->combinationRepository->partialUpdate(
            $combination,
            $propertiesToUpdate,
            CannotUpdateCombinationException::FAILED_UPDATE_STOCK
        );

        $this->updateStockAvailable($combination, $propertiesToUpdate, $addMovement);
    }

    /**
     * @param Combination $combination
     * @param array $propertiesToUpdate
     * @param bool $addMovement
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    private function updateStockAvailable(Combination $combination, array $propertiesToUpdate, bool $addMovement): void
    {
        $updateQuantity = false;
        $updateLocation = false;

        if (in_array('quantity', $propertiesToUpdate)) {
            $updateQuantity = true;
        }
        if (in_array('location', $propertiesToUpdate)) {
            $updateLocation = true;
        }
        if (!$updateQuantity && !$updateLocation) {
            return;
        }

        $stockAvailable = $this->stockAvailableRepository->getForCombination(new CombinationId((int) $combination->id));

        if ($updateQuantity) {
            $newQuantity = (int) $combination->quantity;
            if ($addMovement) {
                $this->saveMovement($combination, (int) $stockAvailable->quantity, $newQuantity);
            }
            $stockAvailable->quantity = $newQuantity;
        }

        if ($updateLocation) {
            $stockAvailable->location = $combination->location;
        }

        $this->stockAvailableRepository->update($stockAvailable);
    }

    /**
     * @param Combination $combination
     * @param int $oldQuantity
     * @param int $newQuantity
     *
     * @throws CoreException
     */
    private function saveMovement(Combination $combination, int $oldQuantity, int $newQuantity): void
    {
        $combinationId = $combination->id;
        $deltaQuantity = $newQuantity - $oldQuantity;

        if (0 === $deltaQuantity) {
            return;
        }

        try {
            $this->stockManager->saveMovement($combination->id_product, $combinationId, $deltaQuantity);
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to save stock movement for combination %d', $combinationId),
                0,
                $e
            );
        }
    }
}
