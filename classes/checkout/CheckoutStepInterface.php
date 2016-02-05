<?php

interface CheckoutStepInterface
{
    public function render();
    public function getTitle();
    public function handleRequest(array $requestParameters = []);
    public function setCheckoutProcess(CheckoutProcess $checkoutProcess);
    public function getCheckoutProcess();
    public function isReachable();
    public function isComplete();
}
