<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use Hook;
use OrderInvoice;
use PDF;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\Exception\PdfException;
use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShopException;
use RuntimeException;
use Validate;

/**
 * Generates invoice PDFs selected by invoice ID, for individual downloads or bulk exports.
 *
 * generatePDFForResponse() combines one or more selected invoices into a single PDF file.
 * generatePDF() sends exactly one invoice directly to the browser.
 * The selection is based on invoice IDs and can span multiple orders.
 */
final class InvoicePdfGenerator implements PDFGeneratorInterface
{
    /**
     * Sends a single invoice PDF directly to the browser.
     *
     * Available for backward compatibility with external callers; not called by core.
     * Core PDF download controllers use generatePDFForResponse().
     *
     * @param int[] $invoiceId Exactly one invoice ID
     *
     * @return void
     */
    public function generatePDF(array $invoiceId): void
    {
        try {
            // Require a single invoice for direct PDF output
            if (count($invoiceId) !== 1) {
                throw new CoreException(sprintf('"%s" supports generating PDF for single invoice only.', self::class));
            }

            $this->createPdf($invoiceId)->render();
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Renders one or more selected invoices and returns their content and download filename.
     *
     * @param int[] $invoiceId One or more invoice IDs in document order
     *
     * @return GeneratedPdf PDF bytes and filename; no HTTP headers or content are sent
     */
    public function generatePDFForResponse(array $invoiceId): GeneratedPdf
    {
        try {
            $pdf = $this->createPdf($invoiceId);

            return new GeneratedPdf(
                $pdf->render(false),
                $pdf->getFilename()
            );
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Loads the selected invoices, runs actionPDFInvoiceRender and builds their PDF.
     *
     * @param int[] $invoiceId One or more invoice IDs in document order
     *
     * @return PDF The document ready for rendering
     */
    private function createPdf(array $invoiceId): PDF
    {
        // Require at least one invoice before building the document
        if (empty($invoiceId)) {
            throw new CoreException(sprintf('"%s" requires at least one invoice ID.', self::class));
        }

        // Load and validate each invoice in the requested order
        $invoices = [];
        foreach ($invoiceId as $id) {
            $orderInvoice = new OrderInvoice((int) $id);
            if (!Validate::isLoadedObject($orderInvoice)) {
                throw new RuntimeException('The invoice cannot be found within your database.');
            }
            $invoices[] = $orderInvoice;
        }

        // Allow modules to prepare the invoices before rendering their PDF
        Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => $invoices]);

        // Build one PDF containing the requested invoices
        return new PDF($invoices, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
    }
}
