<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Security\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Security\Command\ClearOutdatedCustomerSessionCommand;

/**
 * Interface ClearOutdatedCustomerSessionHandlerInterface defines session deletion handler.
 */
interface ClearOutdatedCustomerSessionHandlerInterface
{
    /**
     * Delete session.
     *
     * @param ClearOutdatedCustomerSessionCommand $command
     */
    public function handle(ClearOutdatedCustomerSessionCommand $command): void;
}
