<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use Hook;
use Order;
use PDF;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\Exception\PdfException;
use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShopException;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validate;

/**
 * Generates a PDF containing all invoices belonging to one order, for the order invoice download.
 *
 * The input contains exactly one order ID. An order can have several invoices,
 * which are combined into a single PDF file. Individual invoice IDs are not accepted.
 */
final class OrderInvoicePdfGenerator implements PDFGeneratorInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Sends all invoices belonging to one order directly to the browser as a single PDF.
     *
     * Available for backward compatibility with external callers; not called by core.
     * Core PDF download controllers use generatePDFForResponse().
     *
     * @param int[] $orderId Exactly one order ID; all of its invoices are included
     *
     * @return string TCPDF return value after directly sending the document
     */
    public function generatePDF(array $orderId): string
    {
        try {
            return $this->createPdf($orderId)->render(true);
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Renders all invoices of one order and returns their content and download filename.
     *
     * @param int[] $orderId Exactly one order ID; all of its invoices are included
     *
     * @return GeneratedPdf PDF bytes and filename; no HTTP headers or content are sent
     */
    public function generatePDFForResponse(array $orderId): GeneratedPdf
    {
        try {
            $pdf = $this->createPdf($orderId);

            return new GeneratedPdf(
                $pdf->render(false),
                $pdf->getFilename()
            );
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Loads an order and its invoices, runs actionPDFInvoiceRender and builds their PDF.
     *
     * @param int[] $orderId Exactly one order ID; all of its invoices are included
     *
     * @return PDF The document ready for rendering
     */
    private function createPdf(array $orderId): PDF
    {
        // Require exactly one order ID
        if (count($orderId) !== 1) {
            throw new CoreException(sprintf('"%s" supports generating invoice for single order only.', self::class));
        }

        // Load and validate the order
        $orderId = reset($orderId);
        $order = new Order((int) $orderId);
        if (!Validate::isLoadedObject($order)) {
            throw new RuntimeException($this->translator->trans('The order cannot be found within your database.', [], 'Admin.Orderscustomers.Notification'));
        }

        // Load the invoices and allow modules to prepare them for rendering
        $order_invoice_list = $order->getInvoicesCollection();
        Hook::exec('actionPDFInvoiceRender', ['order_invoice_list' => $order_invoice_list]);

        // Build one document containing all invoices of the order
        return new PDF($order_invoice_list, PDF::TEMPLATE_INVOICE, Context::getContext()->smarty);
    }
}
