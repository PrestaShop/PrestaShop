<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
