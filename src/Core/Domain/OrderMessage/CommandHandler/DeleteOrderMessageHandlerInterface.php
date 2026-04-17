<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\DeleteOrderMessageCommand;

/**
 * Interface for service that handles deleting order message
 */
interface DeleteOrderMessageHandlerInterface
{
    /**
     * @param DeleteOrderMessageCommand $command
     */
    public function handle(DeleteOrderMessageCommand $command): void;
}
