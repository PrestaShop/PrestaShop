<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Invoice\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\UpdateInvoiceNoteCommand;

/**
 * Interface for service that handles updating invoice note
 */
interface UpdateInvoiceNoteHandlerInterface
{
    /**
     * @param UpdateInvoiceNoteCommand $command
     */
    public function handle(UpdateInvoiceNoteCommand $command): void;
}
