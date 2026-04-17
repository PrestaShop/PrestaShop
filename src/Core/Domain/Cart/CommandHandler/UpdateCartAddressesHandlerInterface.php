<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartAddressesCommand;

/**
 * Interface for service that updates addresses for given cart
 */
interface UpdateCartAddressesHandlerInterface
{
    /**
     * @param UpdateCartAddressesCommand $command
     */
    public function handle(UpdateCartAddressesCommand $command);
}
