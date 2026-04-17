<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\AddSqlRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Interface AddSqlRequestHandlerInterface defines contract for SqlRequest creation handler.
 */
interface AddSqlRequestHandlerInterface
{
    /**
     * @param AddSqlRequestCommand $command
     *
     * @return SqlRequestId
     */
    public function handle(AddSqlRequestCommand $command);
}
