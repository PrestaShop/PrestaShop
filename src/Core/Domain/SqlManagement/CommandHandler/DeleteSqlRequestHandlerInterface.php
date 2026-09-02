<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\DeleteSqlRequestCommand;

/**
 * Interface DeleteSqlRequestHandlerInterface defines contract for SqlRequest delete handler.
 */
interface DeleteSqlRequestHandlerInterface
{
    /**
     * Delete SqlRequest.
     *
     * @param DeleteSqlRequestCommand $command
     */
    public function handle(DeleteSqlRequestCommand $command);
}
