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
    public function isCurrent();
    public function getIdentifier();
    public function getDataToPersist();
    public function restorePersistedData(array $data);
}
