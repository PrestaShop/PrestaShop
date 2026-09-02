<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Command\ReplyToCustomerThreadCommand;

/**
 * Interface for service that handles replying to customer thread
 */
interface ReplyToCustomerThreadHandlerInterface
{
    /**
     * @param ReplyToCustomerThreadCommand $command
     */
    public function handle(ReplyToCustomerThreadCommand $command);
}
