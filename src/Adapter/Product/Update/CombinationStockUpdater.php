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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

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
     * @param StockAvailableRepository $stockAvailableRepository
     * @param CombinationRepository $combinationRepository
     */
    public function __construct(
        StockAvailableRepository $stockAvailableRepository,
        CombinationRepository $combinationRepository
    ) {
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->combinationRepository = $combinationRepository;
    }

    /**
     * @param Combination $combination
     * @param array $propertiesToUpdate
     *
     * @throws CombinationConstraintException
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function update(Combination $combination, array $propertiesToUpdate): void
    {
        $this->combinationRepository->partialUpdate(
            $combination,
            $propertiesToUpdate,
            CannotUpdateCombinationException::FAILED_UPDATE_STOCK
        );

        if (in_array('quantity', $propertiesToUpdate)) {
            $this->updateStockAvailableQuantity($combination);
        }
    }

    /**
     * @param Combination $combination
     *
     * @throws CombinationConstraintException
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    private function updateStockAvailableQuantity(Combination $combination): void
    {
        $combinationId = new CombinationId((int) $combination->id);
        $stockAvailable = $this->stockAvailableRepository->getForCombination($combinationId);

        try {
            //@todo: refactor as in ProductStockUpdater
            $stockAvailable::setQuantity((int) $combination->id_product, $combinationId->getValue(), $combination->quantity);
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to update combination %d quantity', $combinationId->getValue()),
                0,
                $e
            );
        }
    }
}
