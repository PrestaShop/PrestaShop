<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\PDF;

/**
 * A single renderable PDF document (one invoice, credit slip, order return...).
 *
 * Rendering is split in the same 4 HTML blocks TCPDF composes a page group from:
 * header/footer are repeated on every page, pagination is written last in the
 * footer area, content is the actual page body.
 */
interface PdfDocumentInterface
{
    public function getHeader(): string;

    public function getFooter(): string;

    public function getPagination(): string;

    public function getContent(): string;

    public function getFilename(): string;
}
