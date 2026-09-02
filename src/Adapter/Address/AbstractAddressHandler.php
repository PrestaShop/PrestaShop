<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Address;

use Address;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\InvalidAddressFieldException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShopException;

/**
 * Provides reusable methods for address command/query handlers
 */
abstract class AbstractAddressHandler
{
    /**
     * @param AddressId $addressId
     *
     * @return Address
     *
     * @throws AddressException
     * @throws AddressNotFoundException
     */
    protected function getAddress(AddressId $addressId)
    {
        try {
            $address = new Address($addressId->getValue());
        } catch (PrestaShopException $e) {
            throw new AddressException('Failed to create new address', 0, $e);
        }

        if ($address->id !== $addressId->getValue()) {
            throw new AddressNotFoundException(sprintf('Address with id "%s" was not found.', $addressId->getValue()));
        }

        return $address;
    }

    /**
     * Deletes legacy Address
     *
     * @param Address $address
     *
     * @return bool
     *
     * @throws AddressException
     */
    protected function deleteAddress(Address $address): bool
    {
        try {
            return $address->delete();
        } catch (PrestaShopException) {
            throw new AddressException(sprintf('An error occurred when deleting Address object with id "%s".', $address->id));
        }
    }

    /**
     * @param Address $address
     *
     * @throws InvalidAddressFieldException
     * @throws PrestaShopException
     */
    protected function validateAddress(Address $address): void
    {
        if (true !== ($validateResult = $address->validateFields(false, true))
            || true !== ($validateResult = $address->validateFieldsLang(false, true))) {
            throw new InvalidAddressFieldException(sprintf('Address fields contain invalid values: %s', $validateResult));
        }
    }
}
