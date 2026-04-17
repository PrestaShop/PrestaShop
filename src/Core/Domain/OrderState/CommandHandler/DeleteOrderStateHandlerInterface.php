<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\DeleteOrderStateCommand;

/**
 * Defines contract for DeleteOrderStateHandler
 */
interface DeleteOrderStateHandlerInterface
{
    /**
     * @param DeleteOrderStateCommand $command
     */
    public function handle(DeleteOrderStateCommand $command): void;
}
