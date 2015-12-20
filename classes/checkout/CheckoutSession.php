<?php

class CheckoutSessionCore
{
    private $context;

    public function setContext(Context $context)
    {
        $this->context = $context;
        return $this;
    }

    public function customerHasLoggedIn()
    {
        return $this->context->customer->isLogged();
    }

    public function getCustomer()
    {
        return $this->context->customer;
    }

    public function getCustomerAddressesCount()
    {
        return count($this->getCustomer()->getSimpleAddresses());
    }

    public function setIdAddressDelivery($id_address)
    {
        $this->context->cart->id_address_delivery = $id_address;
        $this->context->cart->save();
        return $this;
    }

    public function setIdAddressInvoice($id_address)
    {
        $this->context->cart->id_address_invoice = $id_address;
        $this->context->cart->save();
        return $this;
    }

    public function getIdAddressDelivery()
    {
        return $this->context->cart->id_address_delivery;
    }

    public function getIdAddressInvoice()
    {
        return $this->context->cart->id_address_invoice;
    }
}
