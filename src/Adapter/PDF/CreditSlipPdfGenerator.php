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
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShopException;

/**
 * Responsible for generating CreditSlip PDF
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
     * Generates PDF from given data using legacy object models
     *
     * @param CreditSlipId[] $creditSlipIds
     *
     * @throws PdfException
     */
    public function generatePDF(array $creditSlipIds): string
    {
        $ids = [];
        foreach ($creditSlipIds as $creditSlipId) {
            $ids[] = $creditSlipId->getValue();
        }

        try {
            $slipsList = $this->getCreditSlipsList($ids);
            $slipsCollection = ObjectModel::hydrateCollection('OrderSlip', $slipsList);

            $pdf = new PDF($slipsCollection, PDF::TEMPLATE_ORDER_SLIP, Context::getContext()->smarty);

            return $pdf->render(true);
        } catch (PrestaShopException $e) {
            throw new PdfException('Something went wrong when trying to generate pdf', 0, $e);
        }
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
