<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Security\Command\DeleteCustomerSessionCommand;

/**
 * Interface DeleteCustomerSessionHandlerInterface defines session deletion handler.
 */
interface DeleteCustomerSessionHandlerInterface
{
    /**
     * Delete session.
     *
     * @param DeleteCustomerSessionCommand $command
     */
    public function handle(DeleteCustomerSessionCommand $command): void;
}
