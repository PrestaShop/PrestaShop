<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Address;

use Address;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShopException;

/**
 * Provides reusable methods for address command/query handlers
 */
abstract class AbstractAddressHandler
{
    /**
     * Gets legacy address
     *
     * @param AddressId $addressId
     *
     * @return Address
     */
    protected function getAddress(AddressId $addressId)
    {
        try {
            $address = new Address($addressId->getValue());
        } catch (PrestaShopException $e) {
            throw new AddressException('Failed to create new address', 0, $e);
        }

        if ($address->id !== $addressId->getValue()) {
            throw new AddressNotFoundException(
                sprintf('Address with id "%s" was not found.', $addressId->getValue())
            );
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
    protected function deleteAddress(Address $address)
    {
        try {
            return $address->delete();
        } catch (PrestaShopException $e) {
            throw new AddressException(sprintf(
                'An error occurred when deleting Address object with id "%s".',
                $address->id
            ));
        }
    }
}
