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

namespace PrestaShop\PrestaShop\Adapter\CreditSlip\QueryHandler;

use OrderSlip;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception\CreditSlipException;
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
            $ids = OrderSlip::getSlipsIdByDate(
                $query->getDateTimeFrom()->format('Y-m-d'),
                $query->getDateTimeTo()->format('Y-m-d')
            );

            $creditSlipIds = [];
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    $creditSlipIds[] = new CreditSlipId($id);
                }
            }
        } catch (PrestaShopException $e) {
            throw new CreditSlipException(
                'Something went wrong when trying to get OrderSlip ids by date range',
                0,
                $e
            );
        }

        return $creditSlipIds;
    }
}
