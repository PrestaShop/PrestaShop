<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\BulkDeleteSqlRequestCommand;

/**
 * Interface BulkDeleteSqlRequestHandlerInterface defines contract for bulk deleting handler of SqlRequest.
 */
interface BulkDeleteSqlRequestHandlerInterface
{
    /**
     * @param BulkDeleteSqlRequestCommand $command
     */
    public function handle(BulkDeleteSqlRequestCommand $command);
}
