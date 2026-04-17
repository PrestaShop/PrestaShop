<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Title\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Title\Command\EditTitleCommand;

/**
 * Defines contract for EditTitleHandler
 */
interface EditTitleHandlerInterface
{
    /**
     * @param EditTitleCommand $command
     */
    public function handle(EditTitleCommand $command): void;
}
