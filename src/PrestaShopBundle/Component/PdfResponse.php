<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Component;

use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

use function Symfony\Component\String\u;

/**
 * Sends a generated PDF as a download.
 */
final class PdfResponse extends Response
{
    public function __construct(GeneratedPdf $pdf)
    {
        // Document prefixes are configurable by the merchant: no slash is allowed and the fallback must be ASCII without "%"
        $fileName = str_replace(['/', '\\'], '-', $pdf->getFileName());
        $fallbackFileName = str_replace(['/', '\\', '%'], ['-', '-', ''], u($fileName)->ascii()->toString());

        parent::__construct($pdf->getContent(), headers: [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $fileName, $fallbackFileName),
        ]);
    }
}
