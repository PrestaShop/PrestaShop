<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Query\SearchCustomers;

/**
 * Interface for service that handles customers searching command
 */
interface SearchCustomersHandlerInterface
{
    /**
     * @param SearchCustomers $query
     *
     * @return array
     */
    public function handle(SearchCustomers $query);
}
