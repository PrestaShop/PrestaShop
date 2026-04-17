<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command\UpdateOrderReturnStateCommand;

interface UpdateOrderReturnStateHandlerInterface
{
    /**
     * @param UpdateOrderReturnStateCommand $command
     *
     * @return void
     */
    public function handle(UpdateOrderReturnStateCommand $command): void;
}
