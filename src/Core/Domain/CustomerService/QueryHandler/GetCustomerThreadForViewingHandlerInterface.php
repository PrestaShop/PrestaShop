<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerThreadForViewing;
use PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryResult\CustomerThreadView;

/**
 * Interface for service that gets customer thread for viewing
 */
interface GetCustomerThreadForViewingHandlerInterface
{
    /**
     * @param GetCustomerThreadForViewing $query
     *
     * @return CustomerThreadView
     */
    public function handle(GetCustomerThreadForViewing $query);
}
