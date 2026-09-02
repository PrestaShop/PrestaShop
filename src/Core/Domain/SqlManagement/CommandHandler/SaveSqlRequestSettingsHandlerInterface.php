<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\SaveSqlRequestSettingsCommand;

/**
 * Interface SaveSqlRequestSettingsHandlerInterface.
 */
interface SaveSqlRequestSettingsHandlerInterface
{
    /**
     * @param SaveSqlRequestSettingsCommand $command
     */
    public function handle(SaveSqlRequestSettingsCommand $command);
}
