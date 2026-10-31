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
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopException;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validate;

/**
 * Generates shipment delivery slips for printing with individual dispatched shipments.
 *
 * Accepts one or more shipment IDs and combines their delivery slips into a single PDF file.
 * Each slip uses its shipment, associated order and order invoices as document data.
 *
 * @internal
 */
final class ShipmentDeliverySlipPdfGenerator implements PDFGeneratorInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    public function __construct(
        TranslatorInterface $translator,
        ShipmentRepository $shipmentRepository
    ) {
        $this->translator = $translator;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Sends the selected shipment delivery slips directly to the browser as a single PDF.
     *
     * Available for backward compatibility with external callers; not called by core.
     * Core PDF download controllers use generatePDFForResponse().
     *
     * @param int[] $shipmentIds One or more shipment IDs in document order
     *
     * @return string TCPDF return value after directly sending the document
     */
    public function generatePDF(array $shipmentIds): string
    {
        try {
            return $this->createPdf($shipmentIds)->render(true);
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Renders the selected shipment delivery slips and returns their content and download filename.
     *
     * @param int[] $shipmentIds One or more shipment IDs in document order
     *
     * @return GeneratedPdf PDF bytes and filename; no HTTP headers or content are sent
     */
    public function generatePDFForResponse(array $shipmentIds): GeneratedPdf
    {
        try {
            $pdf = $this->createPdf($shipmentIds);

            return new GeneratedPdf($pdf->render(false), $pdf->getFilename());
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Loads shipments with their orders and invoices and builds their delivery-slip PDF.
     *
     * @param int[] $shipmentIds One or more shipment IDs in document order
     *
     * @return PDF The document ready for rendering
     */
    private function createPdf(array $shipmentIds): PDF
    {
        // Require at least one shipment before loading its delivery slip data
        if (empty($shipmentIds)) {
            throw new CoreException($this->translator->trans(
                '%class% requires at least one shipment ID.',
                ['%class%' => self::class],
                'Admin.Orderscustomers.Notification'
            ));
        }

        // Fetch the requested shipments and prepare their document data
        $shipmentData = [];

        $intShipmentIds = array_map('intval', $shipmentIds);
        $shipments = $this->shipmentRepository->findByIds($intShipmentIds);

        // Index fetched shipments by their ID for quick lookup
        $shipmentsById = [];
        foreach ($shipments as $shipment) {
            $shipmentsById[$shipment->getId()] = $shipment;
        }

        // Collect the validated shipment, order and invoices for each delivery slip
        foreach ($intShipmentIds as $shipmentId) {
            if (!isset($shipmentsById[$shipmentId])) {
                throw new RuntimeException($this->translator->trans(
                    'The shipment with ID %id% cannot be found within your database.',
                    ['%id%' => $shipmentId],
                    'Admin.Orderscustomers.Notification'
                ));
            }

            $shipment = $shipmentsById[$shipmentId];
            $order = new Order($shipment->getOrderId());

            if (!Validate::isLoadedObject($order)) {
                throw new RuntimeException($this->translator->trans(
                    'The order cannot be found within your database.',
                    [],
                    'Admin.Orderscustomers.Notification'
                ));
            }

            $orderInvoiceCollection = $order->getInvoicesCollection();

            $shipmentData[] = [
                'shipment' => $shipment,
                'order' => $order,
                'order_invoice_collection' => $orderInvoiceCollection,
            ];
        }

        // The PDF class will iterate through the collection and call HTMLTemplateShipmentDeliverySlip for each
        return new PDF($shipmentData, PDF::TEMPLATE_SHIPMENT_DELIVERY_SLIP, Context::getContext()->smarty);
    }
}
