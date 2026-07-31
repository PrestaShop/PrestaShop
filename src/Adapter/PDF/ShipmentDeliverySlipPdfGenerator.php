<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Order;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validate;

/**
 * Generates delivery slip for given shipment(s)
 *
 * @internal
 */
final class ShipmentDeliverySlipPdfGenerator implements PDFGeneratorInterface
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly ShipmentRepository $shipmentRepository,
        private readonly PDFGenerator $pdfGenerator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function generatePDF(array $shipmentIds): string
    {
        if (empty($shipmentIds)) {
            throw new CoreException($this->translator->trans(
                '%class% requires at least one shipment ID.',
                ['%class%' => self::class],
                'Admin.Orderscustomers.Notification'
            ));
        }

        $shipmentData = [];

        $intShipmentIds = array_map('intval', $shipmentIds);
        $shipments = $this->shipmentRepository->findByIds($intShipmentIds);

        // Index fetched shipments by their ID for quick lookup
        $shipmentsById = [];
        foreach ($shipments as $shipment) {
            $shipmentsById[$shipment->getId()] = $shipment;
        }

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

        // Rendered in string mode: the legacy engine's 'D' (download) mode sends its own headers
        // and returns an empty string, which is useless to the BinaryFileResponse the caller builds.
        return $this->pdfGenerator->generatePDFForResponse($shipmentData)->getContent();
    }
}
