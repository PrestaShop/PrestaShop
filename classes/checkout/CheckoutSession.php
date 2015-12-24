<?php

class CheckoutSessionCore
{
    private $context;
    private $deliveryOptionsFinder;

    public function __construct(Context $context, DeliveryOptionsFinder $deliveryOptionsFinder)
    {
        $this->context = $context;
        $this->deliveryOptionsFinder = $deliveryOptionsFinder;
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
        return count($this->getCustomer()->getSimpleAddresses(
            $this->context->language->id,
            true // no cache
        ));
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

    public function setDeliveryOption($option)
    {
        $this->context->cart->setDeliveryOption($option);
        return $this->context->cart->update();
    }

    public function getSelectedDeliveryOption()
    {
        return $this->deliveryOptionsFinder->getSelectedDeliveryOption();
    }

    public function getDeliveryOptions()
    {
        return $this->deliveryOptionsFinder->getDeliveryOptions();
    }
}
