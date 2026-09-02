<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\AddCartRuleToCartCommand;

/**
 * Interface for service that handles adding cart rule to cart.
 */
interface AddCartRuleToCartHandlerInterface
{
    /**
     * @param AddCartRuleToCartCommand $command
     */
    public function handle(AddCartRuleToCartCommand $command);
}
