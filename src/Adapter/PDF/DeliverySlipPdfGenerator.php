<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
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
 * Generates order delivery slips for printing with the goods being dispatched.
 *
 * The input contains exactly one order ID. Its invoice collection supplies the delivery-slip
 * documents, which are combined into one PDF file. This generator does not select shipment IDs.
 *
 * @internal
 */
final class DeliverySlipPdfGenerator implements PDFGeneratorInterface
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
     * Sends the delivery slips for one order directly to the browser as a single PDF.
     *
     * Available for backward compatibility with external callers; not called by core.
     * Core PDF download controllers use generatePDFForResponse().
     *
     * @param int[] $orderId Exactly one order ID; its invoice collection supplies the delivery slips
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
     * Renders the delivery slips for one order and returns their content and download filename.
     *
     * @param int[] $orderId Exactly one order ID; its invoice collection supplies the delivery slips
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
     * Loads the order and its invoice collection and builds the order delivery-slip PDF.
     *
     * @param int[] $orderId Exactly one order ID; its invoice collection supplies the delivery slips
     *
     * @return PDF The document ready for rendering
     */
    private function createPdf(array $orderId): PDF
    {
        // Require exactly one order ID
        if (count($orderId) !== 1) {
            throw new CoreException(sprintf('"%s" supports generating delivery slip for single order only.', self::class));
        }

        // Load and validate the order
        $orderId = reset($orderId);
        $order = new Order((int) $orderId);

        if (!Validate::isLoadedObject($order)) {
            throw new RuntimeException($this->translator->trans('The order cannot be found within your database.', [], 'Admin.Orderscustomers.Notification'));
        }

        // Build the delivery slips from the order invoice collection
        $order_invoice_collection = $order->getInvoicesCollection();
        return new PDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP, Context::getContext()->smarty);
    }
}
