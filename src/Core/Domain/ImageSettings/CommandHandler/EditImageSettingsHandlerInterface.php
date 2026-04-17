<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\EditImageSettingsCommand;

/**
 * Defines contract for EditImageSettingsHandler
 */
interface EditImageSettingsHandlerInterface
{
    /**
     * @param EditImageSettingsCommand $command
     */
    public function handle(EditImageSettingsCommand $command): void;
}
