<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;

/**
 * Interface for service that updates product in given order.
 */
interface UpdateProductInOrderHandlerInterface
{
    /**
     * @param UpdateProductInOrderCommand $command
     */
    public function handle(UpdateProductInOrderCommand $command);
}
