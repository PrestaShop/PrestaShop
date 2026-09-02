<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Payment\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command\AddPaymentCommand;

/**
 * Interface for service that handles adding payment for order.
 */
interface AddPaymentHandlerInterface
{
    /**
     * @param AddPaymentCommand $command
     */
    public function handle(AddPaymentCommand $command);
}
