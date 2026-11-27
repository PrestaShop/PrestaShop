<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Command\EditEmployeeCommand;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\GeneratedEmployeeBackupCodes;

/**
 * Interface for services that handle command which edits employee.
 */
interface EditEmployeeHandlerInterface
{
    /**
     * @param EditEmployeeCommand $command
     *
     * @return GeneratedEmployeeBackupCodes|null Newly generated codes after successful persistence
     */
    public function handle(EditEmployeeCommand $command);
}
