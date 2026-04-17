<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkToggleLanguagesStatusCommand;

/**
 * Interface for service that toggles status for multiple languages
 */
interface BulkToggleLanguagesStatusHandlerInterface
{
    /**
     * @param BulkToggleLanguagesStatusCommand $command
     */
    public function handle(BulkToggleLanguagesStatusCommand $command);
}
