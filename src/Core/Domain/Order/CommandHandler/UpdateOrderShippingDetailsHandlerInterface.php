<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderShippingDetailsCommand;

/**
 * Interface for service that handling updating shipping details for given order.
 */
interface UpdateOrderShippingDetailsHandlerInterface
{
    /**
     * @param UpdateOrderShippingDetailsCommand $command
     */
    public function handle(UpdateOrderShippingDetailsCommand $command);
}
