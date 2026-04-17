<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Command\AddEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

/**
 * Interface for services that handle command which adds new employee
 */
interface AddEmployeeHandlerInterface
{
    /**
     * @param AddEmployeeCommand $command
     *
     * @return EmployeeId Added employee's ID
     */
    public function handle(AddEmployeeCommand $command);
}
