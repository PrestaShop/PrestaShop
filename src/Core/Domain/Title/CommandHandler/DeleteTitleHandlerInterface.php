<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Title\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Title\Command\DeleteTitleCommand;

/**
 * Defines contract for DeleteTitleHandler
 */
interface DeleteTitleHandlerInterface
{
    /**
     * @param DeleteTitleCommand $command
     */
    public function handle(DeleteTitleCommand $command): void;
}
