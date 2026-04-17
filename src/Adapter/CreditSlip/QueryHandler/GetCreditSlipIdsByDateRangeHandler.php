<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CreditSlip\QueryHandler;

use OrderSlip;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception\CreditSlipException;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception\CreditSlipNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Query\GetCreditSlipIdsByDateRange;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\QueryHandler\GetCreditSlipIdsByDateRangeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\ValueObject\CreditSlipId;
use PrestaShopException;

/**
 * Handles query which gets CreditSlipIds by provided date range
 */
#[AsQueryHandler]
final class GetCreditSlipIdsByDateRangeHandler implements GetCreditSlipIdsByDateRangeHandlerInterface
{
    /**
     * Handles the query using legacy object model
     *
     * {@inheritdoc}
     */
    public function handle(GetCreditSlipIdsByDateRange $query)
    {
        try {
            $from = $query->getDateTimeFrom()->format('Y-m-d');
            $to = $query->getDateTimeTo()->format('Y-m-d');
            $ids = OrderSlip::getSlipsIdByDate($from, $to);

            if (empty($ids)) {
                throw new CreditSlipNotFoundException(sprintf('No credit slips found for date range "%s - %s"', $from, $to), CreditSlipNotFoundException::BY_DATE_RANGE);
            }

            $creditSlipIds = [];
            foreach ($ids as $id) {
                $creditSlipIds[] = new CreditSlipId($id);
            }
        } catch (PrestaShopException $e) {
            throw new CreditSlipException('Something went wrong when trying to get OrderSlip ids by date range', 0, $e);
        }

        return $creditSlipIds;
    }
}
