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
}
