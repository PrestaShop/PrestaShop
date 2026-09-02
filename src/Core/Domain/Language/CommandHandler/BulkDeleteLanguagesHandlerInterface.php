<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkDeleteLanguagesCommand;

/**
 * Interface for service that deletes languages in bulk action
 */
interface BulkDeleteLanguagesHandlerInterface
{
    /**
     * @param BulkDeleteLanguagesCommand $command
     */
    public function handle(BulkDeleteLanguagesCommand $command);
}
