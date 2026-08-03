<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Command\RemoveAllFeatureValuesFromCombinationCommand;

/**
 * Defines contract to handle @see RemoveAllFeatureValuesFromCombinationCommand
 */
interface RemoveAllFeatureValuesFromCombinationHandlerInterface
{
    /**
     * @param RemoveAllFeatureValuesFromCombinationCommand $command
     */
    public function handle(RemoveAllFeatureValuesFromCombinationCommand $command): void;
}
