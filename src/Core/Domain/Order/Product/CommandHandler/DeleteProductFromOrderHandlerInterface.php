<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\DeleteProductFromOrderCommand;

/**
 * Interface for service that handles deleting product from order.
 */
interface DeleteProductFromOrderHandlerInterface
{
    /**
     * @param DeleteProductFromOrderCommand $command
     */
    public function handle(DeleteProductFromOrderCommand $command);
}
