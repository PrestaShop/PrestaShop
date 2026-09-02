<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Notification\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Notification\Command\UpdateEmployeeNotificationLastElementCommand;

/**
 * Interface for service that handles ACK employee notifications last elements
 */
interface UpdateEmployeeNotificationLastElementCommandHandlerInterface
{
    /**
     * @param UpdateEmployeeNotificationLastElementCommand $command
     */
    public function handle(UpdateEmployeeNotificationLastElementCommand $command);
}
