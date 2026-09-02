<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Command\BulkUpdateEmployeeStatusCommand;

/**
 * Interface UpdateEmployeeStatusHandlerInterface.
 */
interface BulkUpdateEmployeeStatusHandlerInterface
{
    /**
     * @param BulkUpdateEmployeeStatusCommand $command
     */
    public function handle(BulkUpdateEmployeeStatusCommand $command);
}
