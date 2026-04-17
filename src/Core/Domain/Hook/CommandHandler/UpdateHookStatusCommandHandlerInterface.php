<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Hook\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Hook\Command\UpdateHookStatusCommand;

/**
 * Interface for service that set hook to be enabled or disabled
 */
interface UpdateHookStatusCommandHandlerInterface
{
    /**
     * @param UpdateHookStatusCommand $command
     */
    public function handle(UpdateHookStatusCommand $command);
}
