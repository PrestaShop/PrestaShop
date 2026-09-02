<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderInvoiceAddressCommand;

/**
 * Interface for service that handles changing order invoice address.
 */
interface ChangeOrderInvoiceAddressHandlerInterface
{
    /**
     * @param ChangeOrderInvoiceAddressCommand $command
     */
    public function handle(ChangeOrderInvoiceAddressCommand $command);
}
