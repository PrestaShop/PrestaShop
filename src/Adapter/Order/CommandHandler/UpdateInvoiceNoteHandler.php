<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use OrderInvoice;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\UpdateInvoiceNoteCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\CommandHandler\UpdateInvoiceNoteHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Exception\InvoiceException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Exception\InvoiceNotFoundException;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class UpdateInvoiceNoteHandler implements UpdateInvoiceNoteHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(UpdateInvoiceNoteCommand $command): void
    {
        $note = $command->getNote();
        $orderInvoice = new OrderInvoice($command->getOrderInvoiceId()->getValue());

        if (!Validate::isLoadedObject($orderInvoice) && Validate::isCleanHtml($note)) {
            throw new InvoiceNotFoundException(sprintf('Order invoice with id "%d" was not found', $command->getOrderInvoiceId()->getValue()));
        }

        $orderInvoice->note = $note;

        if (!$orderInvoice->save()) {
            throw new InvoiceException('The invoice note was not saved.');
        }
    }
}
