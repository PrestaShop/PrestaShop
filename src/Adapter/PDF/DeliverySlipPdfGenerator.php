<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use ObjectModel;
use Order;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validate;

/**
 * Generates delivery slip for given order
 *
 * @internal
 */
final class DeliverySlipPdfGenerator implements PDFGeneratorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly PDFGenerator $pdfGenerator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function generatePDF(array $orderId): void
    {
        $this->pdfGenerator->generatePDF($this->getOrderInvoiceList($orderId));
    }

    public function generatePDFForResponse(array $orderId): GeneratedPdf
    {
        return $this->pdfGenerator->generatePDFForResponse($this->getOrderInvoiceList($orderId));
    }

    /**
     * @return ObjectModel[]
     */
    private function getOrderInvoiceList(array $orderId): array
    {
        if (count($orderId) !== 1) {
            throw new CoreException(sprintf('"%s" supports generating delivery slip for single order only.', self::class));
        }

        $orderId = reset($orderId);
        $order = new Order((int) $orderId);

        if (!Validate::isLoadedObject($order)) {
            throw new RuntimeException($this->translator->trans('The order cannot be found within your database.', [], 'Admin.Orderscustomers.Notification'));
        }

        return iterator_to_array($order->getInvoicesCollection());
    }
}
