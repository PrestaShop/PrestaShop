<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Notification\CommandHandler;

use Notification;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Notification\Command\UpdateEmployeeNotificationLastElementCommand;
use PrestaShop\PrestaShop\Core\Domain\Notification\CommandHandler\UpdateEmployeeNotificationLastElementCommandHandlerInterface;

/**
 * Handle update employee's last notification element of a given type
 *
 * @internal
 */
#[AsCommandHandler]
final class UpdateEmployeeNotificationLastElementHandler implements UpdateEmployeeNotificationLastElementCommandHandlerInterface
{
    /**
     * @param UpdateEmployeeNotificationLastElementCommand $command
     */
    public function handle(UpdateEmployeeNotificationLastElementCommand $command)
    {
        (new Notification())->updateEmployeeLastElement($command->getType()->getValue());
    }
}
