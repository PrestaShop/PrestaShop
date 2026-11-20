<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use OrderReturn;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use Validate;

/**
 * Wraps the legacy classes/pdf/HTMLTemplateOrderReturn + TCPDF stack so the Symfony controller
 * stays free of direct legacy calls (enforced by the phpstan-disallowed-calls ruleset).
 *
 * @internal
 */
final class OrderReturnPdfGenerator implements PDFGeneratorInterface
{
    public function __construct(
        private readonly PDFGenerator $pdfGenerator
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @param array<int, int> $orderReturnIds exactly one id is supported
     *
     * @return string raw PDF bytes, suitable for a Symfony Response body
     */
    public function generatePDF(array $orderReturnIds): string
    {
        if (count($orderReturnIds) !== 1) {
            throw new CoreException(sprintf('"%s" supports generating a PDF for a single order return only.', self::class));
        }

        $orderReturn = new OrderReturn((int) reset($orderReturnIds));
        if (!Validate::isLoadedObject($orderReturn)) {
            throw new OrderReturnNotFoundException();
        }

        // Rendered in string mode (never 'D'/download) so the caller controls the HTTP response
        // lifecycle - letting the legacy engine send headers and exit would bypass the Symfony kernel.
        return $this->pdfGenerator->generatePDFForResponse([$orderReturn])->getContent();
    }
}
