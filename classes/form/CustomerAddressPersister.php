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
class CustomerAddressPersisterCore
{
    private $customer;
    private $token;
    private $cart;

    public function __construct(Customer $customer, Cart $cart, $token)
    {
        $this->customer = $customer;
        $this->cart = $cart;
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    private function authorizeChange(Address $address, $token)
    {
        if ($address->id_customer && (int) $address->id_customer !== (int) $this->customer->id) {
            // Can't touch anybody else's address
            return false;
        }

        if ($token !== $this->token) {
            // XSS?
            return false;
        }

        return true;
    }

    public function save(Address $address, $token)
    {
        if (!$this->authorizeChange($address, $token)) {
            return false;
        }

        $address->id_customer = $this->customer->id;

        if ($address->isUsed()) {
            return $this->updateUsedAddress($address);
        }

        return $address->save();
    }

    public function delete(Address $address, $token)
    {
        if (!$this->authorizeChange($address, $token)) {
            return false;
        }

        $id = $address->id;
        $ok = $address->delete();

        if ($ok) {
            if ($this->cart->id_address_invoice == $id) {
                unset($this->cart->id_address_invoice);
            }
            if ($this->cart->id_address_delivery == $id) {
                unset($this->cart->id_address_delivery);
                $this->cart->updateAddressId(
                    $id,
                    Address::getFirstCustomerAddressId($this->customer->id)
                );
            }
        }

        return $ok;
    }

    /**
     * When an address has already been used in a placed order, it is not edited directly,
     * instead it is set to "deleted" (but kept in database) and a new address
     * is created.
     *
     * @param Address $address
     *
     * @return bool
     */
    private function updateUsedAddress(Address $address)
    {
        $old_address = new Address($address->id);
        $address->id = $address->id_address = null;

        if ($address->save() && $old_address->delete()) {
            // a new address was created, we must update current cart
            $this->cart->updateAddressId($old_address->id, $address->id);

            return true;
        }

        return false;
    }
}
