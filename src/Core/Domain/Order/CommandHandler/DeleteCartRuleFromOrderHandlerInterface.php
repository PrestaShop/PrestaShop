<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\DeleteCartRuleFromOrderCommand;

/**
 * Interface for service that handles removing cart rule from given order.
 */
interface DeleteCartRuleFromOrderHandlerInterface
{
    /**
     * @param DeleteCartRuleFromOrderCommand $command
     */
    public function handle(DeleteCartRuleFromOrderCommand $command);
}
