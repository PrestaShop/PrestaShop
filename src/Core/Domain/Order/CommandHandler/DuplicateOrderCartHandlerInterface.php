<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\DuplicateOrderCartCommand;

/**
 * Interface for service that handles duplicating order cart
 */
interface DuplicateOrderCartHandlerInterface
{
    /**
     * @param DuplicateOrderCartCommand $command
     *
     * @return CartId Duplicated cart id
     */
    public function handle(DuplicateOrderCartCommand $command);
}
