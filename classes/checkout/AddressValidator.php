<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class AddressValidatorCore
 *
 * Validates addresses held by common PrestaShop objects (cart, customer...)
 */
class AddressValidatorCore
{
    /**
     * Validates cart addresses
     * Returns an array of invalid address IDs.
     *
     * @param Cart $cart
     *   The cart holding the addresses to be inspected
     *
     * @return array
     *   The invalid address ids. Empty if everything is ok.
     */
    public function validateCartAddresses(Cart $cart)
    {
        $invalidAddressIds = array();
        $addressesIds      = array(
            $cart->id_address_delivery,
            $cart->id_address_invoice,
        );

        foreach ($addressesIds as $idAddress) {
            $address = new CustomerAddress((int)$idAddress);
            try {
                $address->validateFields();
            } catch (PrestaShopException $e) {
                $invalidAddressIds[] = (int)$idAddress;
            }
        }

        return $invalidAddressIds;
    }

    /**
     * Validates given customer's addresses
     * Returns an array of invalid address IDs.
     *
     * @param Customer $customer
     *   The customer holding the addresses to be inspected
     *
     * @param Language $language
     *   The language in which addresses should be validated
     *
     * @return array The invalid address ids. Empty if everything is ok.
     * The invalid address ids. Empty if everything is ok.
     */
    public function validateCustomerAddresses(Customer $customer, Language $language)
    {
        $invalidAddresses = array();
        $addresses        = $customer->getAddresses($language->id);

        if (is_array($addresses)) {
            foreach ($addresses as $address) {
                try {
                    $adressObject = new CustomerAddress((int)$address['id_address']);
                    $adressObject->validateFields();
                } catch (PrestaShopException $e) {
                    $invalidAddresses[] = (int)$address['id_address'];
                }
            }
        }

        return $invalidAddresses;
    }
}
