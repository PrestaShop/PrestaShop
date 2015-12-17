<?php

class CheckoutProcessCore
{
    private $steps = [];
    private $checkoutSession;

    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function init(array $requestParameters = [])
    {
        foreach ($this->getSteps() as $step) {
            $step->init($requestParameters);
        }

        return $this;
    }

    public function getCheckoutSession()
    {
        return $this->checkoutSession;
    }

    public function addStep(CheckoutStepInterface $step)
    {
        $step->setCheckoutProcess($this);
        $this->steps[] = $step;

        return $this;
    }

    public function getSteps()
    {
        return $this->steps;
    }

    public function render()
    {
        return implode('', array_map(function (CheckoutStepInterface $step) {
            return $step->render();
        }, $this->getSteps()));
    }
}
