<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\FeatureValue\Command\SetCombinationFeatureValuesCommand;

/**
 * Defines contract to handle @see SetCombinationFeatureValuesCommand
 */
interface SetCombinationFeatureValuesHandlerInterface
{
    /**
     * @param SetCombinationFeatureValuesCommand $command
     *
     * @return FeatureValueId[]
     */
    public function handle(SetCombinationFeatureValuesCommand $command): array;
}
