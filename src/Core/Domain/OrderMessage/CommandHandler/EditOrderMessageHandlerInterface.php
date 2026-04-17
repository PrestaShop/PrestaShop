<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\EditOrderMessageCommand;

/**
 * Interface for service that handles editing order message
 */
interface EditOrderMessageHandlerInterface
{
    /**
     * @param EditOrderMessageCommand $command
     */
    public function handle(EditOrderMessageCommand $command): void;
}
