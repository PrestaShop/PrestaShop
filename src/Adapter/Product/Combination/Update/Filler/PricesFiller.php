<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler;

use Combination;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;

/**
 * Fills combination properties which can be considered as combination details
 */
class PricesFiller implements CombinationFillerInterface
{
    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Combination $combination, UpdateCombinationCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getImpactOnPrice()) {
            $combination->price = (float) (string) $command->getImpactOnPrice();
            $updatableProperties[] = 'price';
        }

        if (null !== $command->getEcoTax()) {
            $combination->ecotax = (float) (string) $command->getEcoTax();
            $updatableProperties[] = 'ecotax';
        }

        if (null !== $command->getImpactOnUnitPrice()) {
            $combination->unit_price_impact = (float) (string) $command->getImpactOnUnitPrice();
            $updatableProperties[] = 'unit_price_impact';
        }

        if (null !== $command->getWholesalePrice()) {
            $combination->wholesale_price = (float) (string) $command->getWholesalePrice();
            $updatableProperties[] = 'wholesale_price';
        }

        return $updatableProperties;
    }
}
