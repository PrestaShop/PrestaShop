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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\MovementReasonRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationStockHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;

/**
 * Handles @see UpdateCombinationStockCommand using legacy object model
 */
final class UpdateCombinationStockHandler implements UpdateCombinationStockHandlerInterface
{
    /**
     * @var CombinationStockUpdater
     */
    private $combinationStockUpdater;

    /**
     * @var MovementReasonRepository
     */
    private $movementReasonRepository;
    /**
     * @var StockAvailableRepository
     */
    private $stockAvailableRepository;

    /**
     * @param CombinationStockUpdater $combinationStockUpdater
     * @param MovementReasonRepository $movementReasonRepository
     * @param StockAvailableRepository $stockAvailableRepository
     */
    public function __construct(
        CombinationStockUpdater $combinationStockUpdater,
        MovementReasonRepository $movementReasonRepository,
        StockAvailableRepository $stockAvailableRepository
    ) {
        $this->combinationStockUpdater = $combinationStockUpdater;
        $this->movementReasonRepository = $movementReasonRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationStockCommand $command): void
    {
        $stockModification = null;
        if ($command->getDeltaQuantity()) {
            $stockModification = new StockModification(
                $command->getDeltaQuantity(),
                $this->movementReasonRepository->getIdForEmployeeEdition($command->getDeltaQuantity() > 0)
            );
        } elseif (null !== $command->getFixedQuantity()) {
            $currentQuantity = (int) $this->stockAvailableRepository->getForCombination($command->getCombinationId())->quantity;
            $deltaQuantity = $command->getFixedQuantity() - $currentQuantity;

            $stockModification = new StockModification(
                $deltaQuantity,
                $this->movementReasonRepository->getIdForEmployeeEdition($deltaQuantity > 0)
            );
        }

        $properties = new CombinationStockProperties(
            $stockModification,
            $command->getMinimalQuantity(),
            $command->getLocation(),
            $command->getLowStockThreshold(),
            $command->getLowStockAlertEnabled(),
            $command->getAvailableDate()
        );

        $this->combinationStockUpdater->update($command->getCombinationId(), $properties);
    }
}
