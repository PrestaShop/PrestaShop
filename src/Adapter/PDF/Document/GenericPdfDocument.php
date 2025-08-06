<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF\Document;

use PrestaShop\PrestaShop\Core\PDF\PdfDocumentInterface;

/**
 * Immutable holder for the 4 HTML blocks of a rendered PDF document, shared by
 * every *PdfDocumentBuilder (Invoice, CreditSlip, OrderReturn, DeliverySlip,
 * ShipmentDeliverySlip): they render their Twig templates upfront and return
 * one of these, so no per-type PdfDocumentInterface implementation is needed.
 */
final class GenericPdfDocument implements PdfDocumentInterface
{
    public function __construct(
        private readonly string $header,
        private readonly string $footer,
        private readonly string $pagination,
        private readonly string $content,
        private readonly string $filename
    ) {
    }

    public function getHeader(): string
    {
        return $this->header;
    }

    public function getFooter(): string
    {
        return $this->footer;
    }

    public function getPagination(): string
    {
        return $this->pagination;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
