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
 * This class wraps up all the product property fillers and merges the updatable properties into a single array.
 * It is intentional that this class doesn't have the same tag as all the internal property fillers.
 *
 * All the internal property fillers are split just to contain less code and be more readable (because the Product contains many of properties).
 */
class CombinationFiller implements CombinationFillerInterface
{
    /**
     * @var CombinationFillerInterface[]
     */
    private $updatablePropertyFillers;

    /**
     * @param CombinationFillerInterface[] $updatablePropertyFillers
     */
    public function __construct(
        iterable $updatablePropertyFillers
    ) {
        $this->updatablePropertyFillers = $updatablePropertyFillers;
    }

    /**
     * {@inheritDoc}
     */
    public function fillUpdatableProperties(Combination $combination, UpdateCombinationCommand $command): array
    {
        $updatableProperties = [];

        foreach ($this->updatablePropertyFillers as $filler) {
            $properties = $filler->fillUpdatableProperties($combination, $command);

            if (empty($properties)) {
                continue;
            }

            $updatableProperties = array_merge($updatableProperties, $properties);
        }

        return $updatableProperties;
    }
}
