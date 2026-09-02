<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderCurrencyCommand;

/**
 * Interface for service that handles changing order currency.
 */
interface ChangeOrderCurrencyHandlerInterface
{
    /**
     * @param ChangeOrderCurrencyCommand $command
     */
    public function handle(ChangeOrderCurrencyCommand $command);
}
