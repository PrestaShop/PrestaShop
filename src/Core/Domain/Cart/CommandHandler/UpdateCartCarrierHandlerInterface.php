<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateCartCarrierCommand;

/**
 * Interface for service that updates delivery options for cart
 */
interface UpdateCartCarrierHandlerInterface
{
    /**
     * @param UpdateCartCarrierCommand $command
     */
    public function handle(UpdateCartCarrierCommand $command);
}
