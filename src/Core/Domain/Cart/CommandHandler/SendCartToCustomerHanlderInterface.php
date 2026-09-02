<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\SendCartToCustomerCommand;

/**
 * Interface for service that handles sending cart to customer.
 *
 * @deprecated Since 9.0 and will be removed in the next major.
 */
interface SendCartToCustomerHanlderInterface
{
    /**
     * @param SendCartToCustomerCommand $command
     */
    public function handle(SendCartToCustomerCommand $command);
}
