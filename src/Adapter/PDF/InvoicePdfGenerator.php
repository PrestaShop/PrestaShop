<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Hook;
use OrderInvoice;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use RuntimeException;
use Validate;

/**
 * Generates invoice by invoice ID.
 */
final class InvoicePdfGenerator implements PDFGeneratorInterface
{
    public function __construct(
        private readonly PDFGenerator $pdfGenerator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function generatePDF(array $invoiceId): void
    {
        $this->pdfGenerator->generatePDF([$this->getOrderInvoice($invoiceId)]);
    }

    public function generatePDFForResponse(array $invoiceId): GeneratedPdf
    {
        return $this->pdfGenerator->generatePDFForResponse([$this->getOrderInvoice($invoiceId)]);
    }

    private function getOrderInvoice(array $invoiceId): OrderInvoice
    {
        if (count($invoiceId) !== 1) {
            throw new CoreException(sprintf('"%s" supports generating PDF for single invoice only.', self::class));
        }

        $invoiceId = reset($invoiceId);
        $orderInvoice = new OrderInvoice((int) $invoiceId);
        if (!Validate::isLoadedObject($orderInvoice)) {
            throw new RuntimeException('The invoice cannot be found within your database.');
        }

        Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => [$orderInvoice]]);

        return $orderInvoice;
    }
}
