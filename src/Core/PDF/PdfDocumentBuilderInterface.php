<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\PDF;

/**
 * Builds a {@see PdfDocumentInterface} for a given object of a PDF template type
 * (see {@see PDFTemplateTypeProviderInterface::getPDFTemplateType()}).
 *
 * One implementation per document type (Invoice, CreditSlip, OrderReturn,
 * DeliverySlip, ShipmentDeliverySlip), tagged as `prestashop.pdf.document_builder`
 * and collected by {@see PDFRendererInterface} implementations.
 */
interface PdfDocumentBuilderInterface
{
    public function supports(string $type): bool;

    /**
     * @param mixed $object one element of the collection passed to {@see PDFGeneratorInterface::generatePDF()}
     */
    public function build($object, bool $bulkMode): PdfDocumentInterface;

    /**
     * Filename used when several objects are rendered as a single PDF (bulk mode).
     */
    public function getBulkFilename(): string;
}
