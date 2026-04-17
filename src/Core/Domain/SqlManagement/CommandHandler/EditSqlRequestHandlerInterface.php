<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\EditSqlRequestCommand;

/**
 * Interface EditSqlRequestHandlerInterface defines contract SqlRequest editing handler.
 */
interface EditSqlRequestHandlerInterface
{
    /**
     * @param EditSqlRequestCommand $command
     */
    public function handle(EditSqlRequestCommand $command);
}
