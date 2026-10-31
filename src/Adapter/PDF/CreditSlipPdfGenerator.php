<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use Doctrine\DBAL\Connection;
use ObjectModel;
use OrderSlip;
use PDF;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\ValueObject\CreditSlipId;
use PrestaShop\PrestaShop\Core\PDF\Exception\MissingDataException;
use PrestaShop\PrestaShop\Core\PDF\Exception\PdfException;
use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShopException;

/**
 * Generates credit slip PDFs documenting credited amounts for refunded orders.
 *
 * Accepts one or more CreditSlipId value objects for individual downloads or bulk exports.
 * The matching credit slips are combined into a single PDF file.
 */
final class CreditSlipPdfGenerator implements PDFGeneratorInterface
{
    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param string $dbPrefix
     * @param Connection $connection
     */
    public function __construct(
        $dbPrefix,
        Connection $connection
    ) {
        $this->dbPrefix = $dbPrefix;
        $this->connection = $connection;
    }

    /**
     * Generates credit slips using legacy object models and sends their PDF directly to the browser.
     *
     * Available for backward compatibility with external callers; not called by core.
     * Core PDF download controllers use generatePDFForResponse().
     *
     * @param CreditSlipId[] $creditSlipIds One or more credit slip identifiers
     *
     * @return string TCPDF return value after directly sending the document
     */
    public function generatePDF(array $creditSlipIds): string
    {
        try {
            return $this->createPdf($creditSlipIds)->render(true);
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Renders the selected credit slips and returns their content and download filename.
     *
     * @param CreditSlipId[] $creditSlipIds One or more credit slip identifiers
     *
     * @return GeneratedPdf PDF bytes and filename; no HTTP headers or content are sent
     */
    public function generatePDFForResponse(array $creditSlipIds): GeneratedPdf
    {
        try {
            $pdf = $this->createPdf($creditSlipIds);

            return new GeneratedPdf(
                $pdf->render(false),
                $pdf->getFilename()
            );
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * Loads credit slip records, hydrates their legacy objects and builds one PDF document.
     *
     * @param CreditSlipId[] $creditSlipIds One or more credit slip identifiers
     *
     * @return PDF The document ready for rendering
     */
    private function createPdf(array $creditSlipIds): PDF
    {
        // Extract the numeric identifiers for the credit slip query
        $ids = [];
        foreach ($creditSlipIds as $creditSlipId) {
            $ids[] = $creditSlipId->getValue();
        }

        // Load and hydrate the credit slips for the PDF template
        $slipsList = $this->getCreditSlipsList($ids);
        $slipsCollection = ObjectModel::hydrateCollection('OrderSlip', $slipsList);

        // Build one document containing the matching credit slips
        return new PDF($slipsCollection, PDF::TEMPLATE_ORDER_SLIP, Context::getContext()->smarty);
    }

    /**
     * Gets credit slips array from sql
     *
     * @param int[] $creditSlipIds
     *
     * @return OrderSlip[]
     */
    private function getCreditSlipsList($creditSlipIds)
    {
        if (!empty($creditSlipIds)) {
            $qb = $this->connection->createQueryBuilder()
                ->select('*')
                ->from($this->dbPrefix . 'order_slip', 'os')
                ->where('id_order_slip IN (:creditSlipIds)')
                ->setParameter('creditSlipIds', $creditSlipIds, Connection::PARAM_INT_ARRAY)
            ;

            $slipsList = $qb->executeQuery()->fetchAll();
        }

        if (!empty($slipsList)) {
            return $slipsList;
        }

        throw new MissingDataException('Missing data required to generate PDF');
    }
}
