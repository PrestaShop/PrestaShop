<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Customer\Query\GetRequiredFieldsForCustomer;

/**
 * Defines interface for service that get required fields for customer sign up
 */
interface GetRequiredFieldsForCustomerHandlerInterface
{
    /**
     * @param GetRequiredFieldsForCustomer $query
     *
     * @return string[]
     */
    public function handle(GetRequiredFieldsForCustomer $query);
}
