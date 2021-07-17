<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        } catch (PrestaShopException $e) {
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
