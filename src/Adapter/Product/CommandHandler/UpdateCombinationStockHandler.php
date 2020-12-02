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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\CombinationStockUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationStockHandlerInterface;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

/**
 * Handles @see UpdateCombinationStockCommand using legacy object model
 */
final class UpdateCombinationStockHandler implements UpdateCombinationStockHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var CombinationStockUpdater
     */
    private $combinationStockUpdater;

    /**
     * @param CombinationRepository $combinationRepository
     * @param CombinationStockUpdater $combinationStockUpdater
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        CombinationStockUpdater $combinationStockUpdater
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->combinationStockUpdater = $combinationStockUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationStockCommand $command): void
    {
        $combination = $this->combinationRepository->get($command->getCombinationId());
        $updatableProperties = $this->fillUpdatableProperties($combination, $command);

        $this->combinationStockUpdater->update($combination, $updatableProperties);
    }

    /**
     * @param Combination $combination
     * @param UpdateCombinationStockCommand $command
     *
     * @return string[]
     */
    private function fillUpdatableProperties(Combination $combination, UpdateCombinationStockCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getQuantity()) {
            $combination->quantity = $command->getQuantity();
            $updatableProperties[] = 'quantity';
        }

        if (null !== $command->getAvailableDate()) {
            $combination->available_date = $command->getAvailableDate()->format(DateTime::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'available_date';
        }

        if (null !== $command->getLocation()) {
            $combination->location = $command->getLocation();
            $updatableProperties[] = 'location';
        }

        if (null !== $command->getLowStockThreshold()) {
            $combination->low_stock_threshold = $command->getLowStockThreshold();
            $updatableProperties[] = 'low_stock_threshold';
        }

        if (null !== $command->getMinimalQuantity()) {
            $combination->minimal_quantity = $command->getMinimalQuantity();
            $updatableProperties[] = 'minimal_quantity';
        }

        if (null !== $command->isLowStockAlertOn()) {
            $combination->low_stock_alert = $command->isLowStockAlertOn();
            $updatableProperties[] = 'low_stock_alert';
        }

        return $updatableProperties;
    }
}
