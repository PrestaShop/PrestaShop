<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Command\EditEmployeeCommand;

/**
 * Interface for services that handle command which edits employee.
 */
interface EditEmployeeHandlerInterface
{
    /**
     * @param EditEmployeeCommand $command
     */
    public function handle(EditEmployeeCommand $command);
}
