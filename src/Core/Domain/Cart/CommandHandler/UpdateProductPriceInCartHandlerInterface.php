<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\UpdateProductPriceInCartCommand;

/**
 * Interface for service that updates product price in cart.
 */
interface UpdateProductPriceInCartHandlerInterface
{
    /**
     * @param UpdateProductPriceInCartCommand $command
     */
    public function handle(UpdateProductPriceInCartCommand $command);
}
