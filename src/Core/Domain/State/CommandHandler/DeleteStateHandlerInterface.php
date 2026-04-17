<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\State\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\State\Command\DeleteStateCommand;

/**
 * Defines contract for DeleteStateHandler
 */
interface DeleteStateHandlerInterface
{
    /**
     * @param DeleteStateCommand $command
     */
    public function handle(DeleteStateCommand $command): void;
}
