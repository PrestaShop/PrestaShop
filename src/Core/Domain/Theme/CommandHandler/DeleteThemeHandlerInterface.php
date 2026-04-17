<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Theme\Command\DeleteThemeCommand;

/**
 * Interface DeleteThemeHandlerInterface
 */
interface DeleteThemeHandlerInterface
{
    /**
     * @param DeleteThemeCommand $command
     */
    public function handle(DeleteThemeCommand $command);
}
