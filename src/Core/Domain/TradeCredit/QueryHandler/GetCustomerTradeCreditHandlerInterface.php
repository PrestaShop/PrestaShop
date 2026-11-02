<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Query\GetCustomerTradeCredit;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\QueryResult\CustomerTradeCreditSummary;

interface GetCustomerTradeCreditHandlerInterface
{
    public function handle(GetCustomerTradeCredit $query): CustomerTradeCreditSummary;
}
