<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Doctrine\DBAL\Connection;
use ObjectModel;
use OrderSlip;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\ValueObject\CreditSlipId;
use PrestaShop\PrestaShop\Core\PDF\Exception\MissingDataException;
use PrestaShop\PrestaShop\Core\PDF\Exception\PdfException;
use PrestaShop\PrestaShop\Core\PDF\GeneratedPdf;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShopException;

/**
 * Responsible for generating CreditSlip PDF
 */
final class CreditSlipPdfGenerator implements PDFGeneratorInterface
{
    /**
     * @param string $dbPrefix
     */
    public function __construct(
        private readonly string $dbPrefix,
        private readonly Connection $connection,
        private readonly PDFGenerator $pdfGenerator
    ) {
    }

    /**
     * Generates PDF from given data using legacy object models
     *
     * @param CreditSlipId[] $creditSlipIds
     *
     * @throws PdfException
     */
    public function generatePDF(array $creditSlipIds): void
    {
        try {
            $this->pdfGenerator->generatePDF($this->getCreditSlips($creditSlipIds));
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    public function generatePDFForResponse(array $creditSlipIds): GeneratedPdf
    {
        try {
            return $this->pdfGenerator->generatePDFForResponse($this->getCreditSlips($creditSlipIds));
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
    }

    /**
     * @param CreditSlipId[] $creditSlipIds
     *
     * @return OrderSlip[]
     */
    private function getCreditSlips(array $creditSlipIds): array
    {
        $ids = [];
        foreach ($creditSlipIds as $creditSlipId) {
            $ids[] = $creditSlipId->getValue();
        }

        $slipsList = $this->getCreditSlipsList($ids);

        return ObjectModel::hydrateCollection('OrderSlip', $slipsList);
    }

    /**
     * Gets credit slips array from sql
     *
     * @param int[] $creditSlipIds
     *
     * @return OrderSlip[]
     *
     * @throws MissingDataException
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
