<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\SetCombinationImagesCommand;

/**
 * Defines contract to handle @see SetCombinationImagesCommand
 */
interface SetCombinationImagesHandlerInterface
{
    /**
     * @param SetCombinationImagesCommand $command
     */
    public function handle(SetCombinationImagesCommand $command): void;
}
