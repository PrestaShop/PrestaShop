<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetCustomerForAddressCreation;
use PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult\AddressCreationCustomerInformation;

/**
 * Defines contract for customer by email search handler
 */
interface GetCustomerForAddressCreationHandlerInterface
{
    /**
     * @param GetCustomerForAddressCreation $query
     *
     * @return AddressCreationCustomerInformation
     */
    public function handle(GetCustomerForAddressCreation $query): AddressCreationCustomerInformation;
}
