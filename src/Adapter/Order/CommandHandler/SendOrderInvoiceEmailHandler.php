<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Customer;
use Mail;
use PrestaShop\PrestaShop\Adapter\PDF\OrderInvoicePdfGenerator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\SendOrderInvoiceEmailCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\SendOrderInvoiceEmailHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderEmailSendException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Sends the invoices of an order to its customer on demand.
 *
 * WHY this exists next to ResendOrderEmailCommand: that one replays a status email, so an invoice can
 * only be sent by moving the order through a status configured to attach one. A merchant who simply
 * corrected a customer's email address, or who was asked for the invoice again, has no reason to
 * change the order's status to do it.
 *
 * @internal
 */
#[AsCommandHandler]
final class SendOrderInvoiceEmailHandler extends AbstractOrderCommandHandler implements SendOrderInvoiceEmailHandlerInterface
{
    public function __construct(
        private readonly OrderInvoicePdfGenerator $invoicePdfGenerator,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function handle(SendOrderInvoiceEmailCommand $command): void
    {
        $order = $this->getOrder($command->getOrderId());

        if (!$order->hasInvoice()) {
            throw new OrderException(sprintf('Order with id "%d" has no invoice to send.', $order->id));
        }

        $customer = new Customer((int) $order->id_customer);
        if (empty($customer->email)) {
            throw new OrderException(sprintf('Customer of order with id "%d" has no email address.', $order->id));
        }

        $generatedPdf = $this->invoicePdfGenerator->generatePDFForResponse([(int) $order->id]);

        $orderLanguage = $order->getAssociatedLanguage();

        $sent = Mail::Send(
            (int) $orderLanguage->getId(),
            'invoice',
            $this->translator->trans(
                'Invoice for your order',
                [],
                'Emails.Subject',
                $orderLanguage->locale
            ),
            [
                '{lastname}' => $customer->lastname,
                '{firstname}' => $customer->firstname,
                '{id_order}' => $order->id,
                '{order_name}' => $order->getUniqReference(),
            ],
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            [
                'invoice' => [
                    'content' => $generatedPdf->getContent(),
                    'name' => $generatedPdf->getFileName(),
                    'mime' => 'application/pdf',
                ],
            ],
            null,
            _PS_MAIL_DIR_,
            true,
            (int) $order->id_shop
        );

        if (!$sent) {
            throw new OrderEmailSendException('Failed to send the invoice email.', OrderEmailSendException::FAILED_SEND_INVOICE);
        }
    }
}
