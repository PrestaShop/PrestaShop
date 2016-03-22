<?php

class CustomerAddressPersisterCore
{
    private $customer;
    private $token;
    private $cart;

    public function __construct(Customer $customer, Cart $cart, $token)
    {
        $this->customer = $customer;
        $this->cart     = $cart;
        $this->token    = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    private function authorizeChange(Address $address, $token)
    {
        if ($address->id_customer && (int)$address->id_customer !== (int)$this->customer->id) {
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
            $old_address = new Address($address->id);
            $address->id = $address->id_address = null;

            return $address->save() && $old_address->delete();
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
}
