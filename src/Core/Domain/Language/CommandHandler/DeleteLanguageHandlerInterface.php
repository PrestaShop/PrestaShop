<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Language\Command\DeleteLanguageCommand;

/**
 * Interface for service that deletes language
 */
interface DeleteLanguageHandlerInterface
{
    /**
     * @param DeleteLanguageCommand $command
     */
    public function handle(DeleteLanguageCommand $command);
}
