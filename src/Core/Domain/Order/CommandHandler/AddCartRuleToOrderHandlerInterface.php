<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddCartRuleToOrderCommand;

/**
 * @internal
 */
interface AddCartRuleToOrderHandlerInterface
{
    /**
     * @param AddCartRuleToOrderCommand $command
     */
    public function handle(AddCartRuleToOrderCommand $command): void;
}
