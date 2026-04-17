<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Language\Command\EditLanguageCommand;

/**
 * Interface for service that implements edit language command handling
 */
interface EditLanguageHandlerInterface
{
    /**
     * @param EditLanguageCommand $command
     */
    public function handle(EditLanguageCommand $command);
}
