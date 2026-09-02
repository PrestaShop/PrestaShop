<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\RemoveAllFeatureValuesFromProductCommand;

/**
 * Defines contract to handle @see RemoveAllFeatureValuesFromProductCommand
 */
interface RemoveAllFeatureValuesFromProductHandlerInterface
{
    /**
     * @param RemoveAllFeatureValuesFromProductCommand $command
     */
    public function handle(RemoveAllFeatureValuesFromProductCommand $command): void;
}
