<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerMessage\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command\AddOrderCustomerMessageCommand;

/**
 * Interface AddOrderCustomerMessageHandlerInterface
 */
interface AddOrderCustomerMessageHandlerInterface
{
    public function handle(AddOrderCustomerMessageCommand $command);
}
