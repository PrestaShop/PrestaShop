<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use OrderReturn;
use PDF;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\Exception\PdfException;
use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShopException;
use Validate;

/**
 * Generates the merchandise return document for one return request, for use when returning goods.
 *
 * Accepts exactly one return ID and produces one return document.
 * generatePDF() returns raw PDF bytes; generatePDFForResponse() also provides the download filename.
 *
 * Uses the legacy classes/pdf/HTMLTemplateOrderReturn and TCPDF stack so Symfony controllers
 * stay free of direct legacy calls, as required by the phpstan-disallowed-calls ruleset.
 *
 * @internal
 */
final class OrderReturnPdfGenerator implements PDFGeneratorInterface
{
    /**
     * Renders a single merchandise return in TCPDF string mode without sending HTTP output.
     *
     * Available for backward compatibility with external callers; not called by core.
     * Core PDF download controllers use generatePDFForResponse().
     *
     * @param int[] $orderReturnIds Exactly one merchandise return ID
     *
     * @return string Raw PDF bytes suitable for a Symfony Response body
     */
    public function generatePDF(array $orderReturnIds): string
    {
        try {
            return (string) $this->createPdf($orderReturnIds)->render(false);
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Renders one merchandise return and returns its content and download filename.
     *
     * @param int[] $orderReturnIds Exactly one merchandise return ID
     *
     * @return GeneratedPdf PDF bytes and filename; no HTTP headers or content are sent
     */
    public function generatePDFForResponse(array $orderReturnIds): GeneratedPdf
    {
        try {
            $pdf = $this->createPdf($orderReturnIds);

            return new GeneratedPdf((string) $pdf->render(false), $pdf->getFilename());
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Loads and validates the merchandise return and builds its PDF document.
     *
     * @param int[] $orderReturnIds Exactly one merchandise return ID
     *
     * @return PDF The document ready for rendering
     */
    private function createPdf(array $orderReturnIds): PDF
    {
        // Require exactly one merchandise return
        if (count($orderReturnIds) !== 1) {
            throw new CoreException(sprintf('"%s" supports generating a PDF for a single order return only.', self::class));
        }

        // Load and validate the merchandise return
        $orderReturn = new OrderReturn((int) reset($orderReturnIds));
        if (!Validate::isLoadedObject($orderReturn)) {
            throw new OrderReturnNotFoundException();
        }

        // Build the merchandise return document using its dedicated template
        return new PDF($orderReturn, PDF::TEMPLATE_ORDER_RETURN, Context::getContext()->smarty);
    }
}
