<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Query;

use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;

/**
 * Gets detailed customer information for viewing in Back Office.
 */
class GetCustomerForViewing
{
    /**
     * @var CustomerId
     */
    private $customerId;

    /**
     * @param int $customerId
     */
    public function __construct($customerId)
    {
        $this->customerId = new CustomerId($customerId);
    }

    /**
     * @return CustomerId
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }
}
