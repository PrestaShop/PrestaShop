<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\DeleteCombinationCommand;

/**
 * Defines contract to handle @see DeleteCombinationCommand
 */
interface DeleteCombinationHandlerInterface
{
    /**
     * @param DeleteCombinationCommand $command
     */
    public function handle(DeleteCombinationCommand $command): void;
}
