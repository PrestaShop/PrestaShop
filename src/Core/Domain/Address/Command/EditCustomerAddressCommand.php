<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\Command;

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Command responsible for holding edits customer address data
 */
class EditCustomerAddressCommand extends AbstractEditAddressCommand
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
    public function __construct(int $addressId)
    {
        $this->addressId = new AddressId($addressId);
    }

    /**
     * @return AddressId
     */
    public function getAddressId(): AddressId
    {
        return $this->addressId;
    }
}
