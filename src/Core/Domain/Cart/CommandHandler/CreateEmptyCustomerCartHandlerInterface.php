<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\CreateEmptyCustomerCartCommand;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Interface for service that handles creating empty customer cart.
 */
interface CreateEmptyCustomerCartHandlerInterface
{
    /**
     * @param CreateEmptyCustomerCartCommand $command
     *
     * @return CartId
     */
    public function handle(CreateEmptyCustomerCartCommand $command);
}
