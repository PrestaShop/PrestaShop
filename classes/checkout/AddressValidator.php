<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class AddressValidatorCore.
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
     *                   The cart holding the addresses to be inspected
     *
     * @return array
     *               The invalid address ids. Empty if everything is ok.
     */
    public function validateCartAddresses(Cart $cart)
    {
        $invalidAddressIds = [];
        $addressesIds = [
            $cart->id_address_delivery,
            $cart->id_address_invoice,
        ];

        foreach ($addressesIds as $idAddress) {
            $address = new CustomerAddress((int) $idAddress);

            try {
                $address->validateFields();
            } catch (PrestaShopException $e) {
                $invalidAddressIds[] = (int) $idAddress;
            }
        }

        return $invalidAddressIds;
    }

    /**
     * Validates given customer's addresses
     * Returns an array of invalid address IDs.
     *
     * @param Customer $customer
     *                           The customer holding the addresses to be inspected
     * @param Language $language
     *                           The language in which addresses should be validated
     *
     * @return array The invalid address ids. Empty if everything is ok.
     *               The invalid address ids. Empty if everything is ok.
     */
    public function validateCustomerAddresses(Customer $customer, Language $language)
    {
        $invalidAddresses = [];
        $addresses = $customer->getAddresses($language->id);

        if (is_array($addresses)) {
            foreach ($addresses as $address) {
                try {
                    $adressObject = new CustomerAddress((int) $address['id_address']);
                    $adressObject->validateFields();
                } catch (PrestaShopException $e) {
                    $invalidAddresses[] = (int) $address['id_address'];
                }
            }
        }

        return $invalidAddresses;
    }
}
