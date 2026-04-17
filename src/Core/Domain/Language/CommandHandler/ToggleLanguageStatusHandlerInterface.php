<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Language\Command\ToggleLanguageStatusCommand;

/**
 * Interface for service that toggles language's status
 */
interface ToggleLanguageStatusHandlerInterface
{
    /**
     * @param ToggleLanguageStatusCommand $command
     */
    public function handle(ToggleLanguageStatusCommand $command);
}
