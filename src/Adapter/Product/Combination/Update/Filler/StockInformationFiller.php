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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler;

use Combination;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime;

/**
 * Fills combination properties related to stock. But just the ones in Combination entity and not the ones in StockAvailable.
 * For properties like quantity, out_of_stock and location @see UpdateCombinationStockAvailableCommand
 */
class StockInformationFiller implements CombinationFillerInterface
{
    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Combination $combination, UpdateCombinationCommand $command): array
    {
        $updatableProperties = [];

        $localizedLaterLabels = $command->getLocalizedAvailableLaterLabels();
        if (null !== $localizedLaterLabels) {
            $combination->available_later = $localizedLaterLabels;
            $updatableProperties['available_later'] = array_keys($localizedLaterLabels);
        }

        $localizedNowLabels = $command->getLocalizedAvailableNowLabels();
        if (null !== $localizedNowLabels) {
            $combination->available_now = $localizedNowLabels;
            $updatableProperties['available_now'] = array_keys($localizedNowLabels);
        }

        if (null !== $command->getAvailableDate()) {
            $combination->available_date = $command->getAvailableDate()->format(DateTime::DEFAULT_DATE_FORMAT);
            $updatableProperties[] = 'available_date';
        }

        $lowStockThreshold = $command->getLowStockThreshold();
        if (null !== $lowStockThreshold) {
            $combination->low_stock_threshold = $lowStockThreshold->getValue();
            $combination->low_stock_alert = $lowStockThreshold->isEnabled();
            $updatableProperties[] = 'low_stock_threshold';
            $updatableProperties[] = 'low_stock_alert';
        }

        if (null !== $command->getMinimalQuantity()) {
            $combination->minimal_quantity = $command->getMinimalQuantity();
            $updatableProperties[] = 'minimal_quantity';
        }

        return $updatableProperties;
    }
}
