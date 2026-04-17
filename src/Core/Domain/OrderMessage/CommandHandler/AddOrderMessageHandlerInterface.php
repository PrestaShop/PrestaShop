<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\AddOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;

/**
 * Interface for service that handles adding order message
 */
interface AddOrderMessageHandlerInterface
{
    /**
     * @param AddOrderMessageCommand $command
     *
     * @return OrderMessageId
     */
    public function handle(AddOrderMessageCommand $command): OrderMessageId;
}
