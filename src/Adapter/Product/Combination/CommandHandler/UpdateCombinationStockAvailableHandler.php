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

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\CombinationStockUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\MovementReasonRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationStockAvailableHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Updates combination stock available using legacy object model
 */
class UpdateCombinationStockAvailableHandler implements UpdateCombinationStockAvailableHandlerInterface
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
     * @var StockAvailableMultiShopRepository
     */
    private $stockAvailableRepository;

    /**
     * @var CombinationMultiShopRepository
     */
    private $combinationMultiShopRepository;

    /**
     * @param CombinationStockUpdater $combinationStockUpdater
     * @param MovementReasonRepository $movementReasonRepository
     * @param StockAvailableMultiShopRepository $stockAvailableRepository
     * @param CombinationMultiShopRepository $combinationMultiShopRepository
     */
    public function __construct(
        CombinationStockUpdater $combinationStockUpdater,
        MovementReasonRepository $movementReasonRepository,
        StockAvailableMultiShopRepository $stockAvailableRepository,
        CombinationMultiShopRepository $combinationMultiShopRepository
    ) {
        $this->combinationStockUpdater = $combinationStockUpdater;
        $this->movementReasonRepository = $movementReasonRepository;
        $this->stockAvailableRepository = $stockAvailableRepository;
        $this->combinationMultiShopRepository = $combinationMultiShopRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationStockAvailableCommand $command): void
    {
        $stockModification = null;
        if ($command->getDeltaQuantity()) {
            $stockModification = new StockModification(
                $command->getDeltaQuantity(),
                $this->movementReasonRepository->getEmployeeEditionReasonId($command->getDeltaQuantity() > 0)
            );
        } elseif (null !== $command->getFixedQuantity()) {
            $combination = $this->combinationMultiShopRepository->getByShopConstraint($command->getCombinationId(), $command->getShopConstraint());
            $currentQuantity = (int) $this->stockAvailableRepository->getForCombination(
                $command->getCombinationId(),
                new ShopId($combination->getShopId())
            )->quantity;

            $deltaQuantity = $command->getFixedQuantity() - $currentQuantity;
            $stockModification = new StockModification(
                $deltaQuantity,
                $this->movementReasonRepository->getEmployeeEditionReasonId($deltaQuantity > 0)
            );
        }

        // Now we only fill the properties existing in StockAvailable object model.
        // Other properties related to stock (which exists in Combination object model) should be taken care by a unified UpdateProductCommand.
        // @todo: once the unification is done this should be refacto as the CombinationStockProperties contains too many fields now
        $properties = new CombinationStockProperties(
            $stockModification,
            null,
            $command->getLocation(),
            null,
            null,
            null,
            null,
            null
        );

        $this->combinationStockUpdater->update($command->getCombinationId(), $properties, $command->getShopConstraint());
    }
}
