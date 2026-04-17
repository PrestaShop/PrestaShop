<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductQuantityInCartCommand;

/**
 * Interface for service that updates (increases or decreases) product quantity in cart
 */
interface UpdateProductQuantityInCartHandlerInterface
{
    /**
     * @param UpdateProductQuantityInCartCommand $command
     */
    public function handle(UpdateProductQuantityInCartCommand $command);
}
