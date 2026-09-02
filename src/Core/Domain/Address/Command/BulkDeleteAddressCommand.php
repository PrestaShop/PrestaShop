<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\Command;

use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;

/**
 * Deletes addresses in bulk action
 */
class BulkDeleteAddressCommand
{
    /**
     * @var AddressId[]
     */
    private $addressIds;

    /**
     * @param int[] $addressIds
     *
     * @throws AddressConstraintException
     */
    public function __construct($addressIds)
    {
        $this->setAddressIds($addressIds);
    }

    /**
     * @return AddressId[]
     */
    public function getAdressIds()
    {
        return $this->addressIds;
    }

    /**
     * @param int[] $addressIds
     *
     * @throws AddressConstraintException
     */
    private function setAddressIds(array $addressIds)
    {
        foreach ($addressIds as $addressId) {
            $this->addressIds[] = new AddressId($addressId);
        }
    }
}
