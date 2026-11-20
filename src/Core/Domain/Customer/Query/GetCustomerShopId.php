<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Query;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

class GetCustomerShopId
{
    private readonly CustomerId $customerId;

    public function __construct(
        int $customerId,
    ) {
        $this->customerId = new CustomerId($customerId);
    }

    public function getCustomerId(): CustomerId
    {
        return $this->customerId;
    }
}
