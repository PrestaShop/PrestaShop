<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Responsible for generating CreditSlip PDF
 */
final class CreditSlipPdfGenerator implements PDFGeneratorInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param TranslatorInterface $translator
     * @param string $dbPrefix
     * @param Connection $connection
     */
    public function __construct(
        TranslatorInterface $translator,
        $dbPrefix,
        Connection $connection
    ) {
        $this->translator = $translator;
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
    public function generatePDF(array $creditSlipIds)
    {
        $ids = [];
        foreach ($creditSlipIds as $creditSlipId) {
            $ids[] = $creditSlipId->getValue();
        }

        try {
            $slipsList = $this->getCreditSlipsList($ids);
            $slipsCollection = ObjectModel::hydrateCollection('OrderSlip', $slipsList);

            $pdf = new PDF($slipsCollection, PDF::TEMPLATE_ORDER_SLIP, Context::getContext()->smarty);
            $pdf->render();
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

            $slipsList = $qb->execute()->fetchAll();
        }

        if (!empty($slipsList)) {
            return $slipsList;
        }

        throw new MissingDataException('Missing data required to generate PDF');
    }
}
