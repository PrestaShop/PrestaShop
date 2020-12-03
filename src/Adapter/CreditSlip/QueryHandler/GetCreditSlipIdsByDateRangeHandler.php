<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\CreditSlip\QueryHandler;

use OrderSlip;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception\CreditSlipException;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception\CreditSlipNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Query\GetCreditSlipIdsByDateRange;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\QueryHandler\GetCreditSlipIdsByDateRangeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\ValueObject\CreditSlipId;
use PrestaShopException;

/**
 * Handles query which gets CreditSlipIds by provided date range
 */
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
