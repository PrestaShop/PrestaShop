<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Security\Command\DeleteEmployeeSessionCommand;

/**
 * Interface DeleteEmployeeSessionHandlerInterface defines session deletion handler.
 */
interface DeleteEmployeeSessionHandlerInterface
{
    /**
     * Delete session.
     *
     * @param DeleteEmployeeSessionCommand $command
     */
    public function handle(DeleteEmployeeSessionCommand $command): void;
}
