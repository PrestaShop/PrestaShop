<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\Query;

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Gets customer address for editing
 */
class GetCustomerAddressForEditing
{
    /**
     * @var AddressId
     */
    private $addressId;

    /**
     * @param int $addressId
     *
     * @throws AddressConstraintException
     */
    public function __construct($addressId)
    {
        $this->addressId = new AddressId($addressId);
    }

    /**
     * @return AddressId
     */
    public function getAddressId()
    {
        return $this->addressId;
    }
}
