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
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationStockAvailableHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;

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
     * @param CombinationStockUpdater $combinationStockUpdater
     */
    public function __construct(
        CombinationStockUpdater $combinationStockUpdater
    ) {
        $this->combinationStockUpdater = $combinationStockUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationStockAvailableCommand $command): void
    {
        $stockModification = null;
        if ($command->getDeltaQuantity()) {
            $stockModification = StockModification::buildDeltaQuantity($command->getDeltaQuantity());
        } elseif (null !== $command->getFixedQuantity()) {
            $stockModification = StockModification::buildFixedQuantity($command->getFixedQuantity());
        }

        $properties = new CombinationStockProperties(
            $stockModification,
            $command->getLocation()
        );

        $this->combinationStockUpdater->update($command->getCombinationId(), $properties, $command->getShopConstraint());
    }
}
