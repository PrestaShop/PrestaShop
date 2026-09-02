<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerOrders;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\OrderSummary;

/**
 * Interface for handling GetCustomerOrders query
 */
interface GetCustomerOrdersHandlerInterface
{
    /**
     * @param GetCustomerOrders $query
     *
     * @return OrderSummary[]
     */
    public function handle(GetCustomerOrders $query): array;
}
