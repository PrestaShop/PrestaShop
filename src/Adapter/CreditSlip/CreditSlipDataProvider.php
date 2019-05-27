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

namespace PrestaShop\PrestaShop\Adapter\CreditSlip;

use DateTimeInterface;
use Db;
use ObjectModel;
use OrderSlip;
use PrestaShop\PrestaShop\Core\CreditSlip\CreditSlipDataProviderInterface;

/**
 * Provides Credit Slip data using legacy object model
 */
final class CreditSlipDataProvider implements CreditSlipDataProviderInterface
{
    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * string $dbPrefix
     */
    public function __construct(
        $dbPrefix
    ) {
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getByDateInterval(DateTimeInterface $dateFrom, DateTimeInterface $dateTo)
    {
        $slipIds = OrderSlip::getSlipsIdByDate(
            $dateFrom->format('Y-m-d'),
            $dateTo->format('Y-m-d')
        );

        $slipIds = '(' . implode(',', $slipIds) . ')';

        $slipsList = Db::getInstance()->executeS(
            'SELECT * FROM ' . $this->dbPrefix .
            'order_slip WHERE id_order_slip IN ' . $slipIds
        );

        return ObjectModel::hydrateCollection('OrderSlip', $slipsList);
    }
}
