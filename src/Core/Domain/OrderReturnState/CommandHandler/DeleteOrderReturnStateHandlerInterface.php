<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\DeleteOrderReturnStateCommand;

/**
 * Defines contract for DeleteOrderStateHandler
 */
interface DeleteOrderReturnStateHandlerInterface
{
    /**
     * @param DeleteOrderReturnStateCommand $command
     */
    public function handle(DeleteOrderReturnStateCommand $command): void;
}
