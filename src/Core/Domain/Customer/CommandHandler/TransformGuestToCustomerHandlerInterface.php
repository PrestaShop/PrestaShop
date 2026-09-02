<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Command\TransformGuestToCustomerCommand;

/**
 * Defines contract for service that handles command which transforms guest into customer
 */
interface TransformGuestToCustomerHandlerInterface
{
    /**
     * @param TransformGuestToCustomerCommand $command
     */
    public function handle(TransformGuestToCustomerCommand $command);
}
