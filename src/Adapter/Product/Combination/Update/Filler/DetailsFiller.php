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
class DetailsFiller implements CombinationFillerInterface
{
    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Combination $combination, UpdateCombinationCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getGtin()) {
            $combination->ean13 = $command->getGtin()->getValue();
            $updatableProperties[] = 'ean13';
        }

        if (null !== $command->getIsbn()) {
            $combination->isbn = $command->getIsbn()->getValue();
            $updatableProperties[] = 'isbn';
        }

        if (null !== $command->getMpn()) {
            $combination->mpn = $command->getMpn();
            $updatableProperties[] = 'mpn';
        }

        if (null !== $command->getReference()) {
            $combination->reference = $command->getReference()->getValue();
            $updatableProperties[] = 'reference';
        }

        if (null !== $command->getUpc()) {
            $combination->upc = $command->getUpc()->getValue();
            $updatableProperties[] = 'upc';
        }

        if (null !== $command->getImpactOnWeight()) {
            $combination->weight = (float) (string) $command->getImpactOnWeight();
            $updatableProperties[] = 'weight';
        }

        return $updatableProperties;
    }
}
