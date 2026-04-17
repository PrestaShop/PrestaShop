<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Cart\Command\RemoveCartRuleFromCartCommand;

/**
 * Interface for service that handling removing cart rule from cart.
 */
interface RemoveCartRuleFromCartHandlerInterface
{
    /**
     * @param RemoveCartRuleFromCartCommand $command
     */
    public function handle(RemoveCartRuleFromCartCommand $command);
}
