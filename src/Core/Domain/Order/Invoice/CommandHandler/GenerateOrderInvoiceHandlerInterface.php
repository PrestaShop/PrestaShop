<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Invoice\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;

/**
 * Interface for service that handles generating invoice for order.
 */
interface GenerateOrderInvoiceHandlerInterface
{
    /**
     * @param GenerateInvoiceCommand $command
     */
    public function handle(GenerateInvoiceCommand $command);
}
