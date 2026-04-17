<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;

/**
 * Interface for service that handles adding product to an exiting order.
 */
interface AddProductToOrderHandlerInterface
{
    /**
     * @param AddProductToOrderCommand $command
     */
    public function handle(AddProductToOrderCommand $command);
}
