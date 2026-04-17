<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\Query\GetCustomerServiceSignature;

/**
 * Interface for service that gets customer service signature
 */
interface GetCustomerServiceSignatureHandlerInterface
{
    /**
     * @param GetCustomerServiceSignature $query
     *
     * @return string
     */
    public function handle(GetCustomerServiceSignature $query);
}
