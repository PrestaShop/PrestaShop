<?php

interface CheckoutStepInterface
{
    public function render();
    public function getTitle();
    public function init(array $requestParameters = []);
    public function setCheckoutProcess(CheckoutProcess $checkoutProcess);
    public function getCheckoutProcess();
}
