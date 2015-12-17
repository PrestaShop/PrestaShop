<?php

class CheckoutSessionCore
{
    private $context;
    private $guest;

    public function setContext(Context $context)
    {
        $this->context = $context;
        return $this;
    }

    public function setGuest($guest)
    {
        $this->guest = (bool)$guest;
        return $this;
    }

    public function isGuest()
    {
        return $this->guest;
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
