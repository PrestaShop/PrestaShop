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

    /*
     * Saves or updates an address for the current customer.
     */
    public function save(Address $address, $token)
    {
        /*
         * First, we need to validate if the customer is allowed to change this address.
         * It must belong to him and the token must match.
         */
        if (!$this->authorizeChange($address, $token)) {
            return false;
        }

        /*
         * Ensure the address is linked to the current customer.
         * We check this in authorizeChange already, but here we set it for new addresses,
         * that don't have id_customer set yet.
         */
        $address->id_customer = $this->customer->id;

        /*
         * If the address has already been used in a placed order,
         * we cannot update it directly, we need to create copy instead.
         *
         * Otherwise, just call a regular save().
         */
        if ($address->isUsed()) {
            return $this->updateUsedAddress($address);
        }

        return $address->save();
    }

    /*
     * Handles deletion of an address for the current customer from my account zone
     * or during checkout.
     */
    public function delete(Address $address, $token)
    {
        if (!$this->authorizeChange($address, $token)) {
            return false;
        }

        // First, we mark the address ID we are deleting, we will need it to update a cart
        $id = $address->id;

        /*
         * And run the deletion process. The address may be hard or soft deleted, depending
         * on whether it has been used in orders already.
         */
        $ok = $address->delete();

        /*
         * If the address was successfully deleted, we need to update the current cart.
         * Deleted address ID was already unassigned from all non-ordered carts in the database in Address:delete() method,
         * but we can still have the deleted ID assigned in context->cart.
         */
        if ($ok) {
            // Unsetting the addresses from the cart is probably not necessary, because
            // it's doing it again inside updateAddressId method.
            if ($this->cart->id_address_invoice == $id) {
                unset($this->cart->id_address_invoice);
            }
            if ($this->cart->id_address_delivery == $id) {
                unset($this->cart->id_address_delivery);
            }

            // Now we update the cart to use another address (the first one) instead of the deleted one
            $this->cart->updateAddressId($id, Address::getFirstCustomerAddressId($this->customer->id));
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
        // We load the current data of this address
        $oldAddress = new Address($address->id);

        // Reset the ID to create a new address
        $address->id = $address->id_address = null;

        // Save the new address and delete the old one
        if ($address->save() && $oldAddress->delete()) {
            /*
             * If the address was successfully changed, we need to update the current cart. Old address ID
             * was already unassigned from all non-ordered carts in the database in Address:delete() method,
             * but we can still have the deleted ID assigned in context->cart.
             *
             * Please note that even if we assign a new address ID to the cart here, if the cart is not saved
             * later and the customer logs out and logs in again, the cart may receive Address::getFirstCustomerAddressId
             * instead of this $address->id.
             *
             * In PrestaShop, we don't have any functionality to remember which address was last used in the cart,
             * if the cart is not saved.
             */
            $this->cart->updateAddressId($oldAddress->id, $address->id);

            return true;
        }

        return false;
    }
}
