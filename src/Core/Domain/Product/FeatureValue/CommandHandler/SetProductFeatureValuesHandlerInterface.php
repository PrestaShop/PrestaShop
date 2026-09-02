<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;
use PrestaShop\PrestaShop\Core\Domain\Product\FeatureValue\Command\SetProductFeatureValuesCommand;

/**
 * Defines contract to handle @see SetProductFeatureValuesCommand
 */
interface SetProductFeatureValuesHandlerInterface
{
    /**
     * @param SetProductFeatureValuesCommand $command
     *
     * @return FeatureValueId[]
     */
    public function handle(SetProductFeatureValuesCommand $command): array;
}
