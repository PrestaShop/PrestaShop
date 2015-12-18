<?php

class CheckoutProcessCore
{
    private $steps = [];
    private $checkoutSession;
    private $has_errors;

    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function handleRequest(array $requestParameters = [])
    {
        foreach ($this->getSteps() as $step) {
            $step->handleRequest($requestParameters);
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

    public function setHasErrors($has_errors = true)
    {
        $this->has_errors = $has_errors;
        return $this;
    }

    public function hasErrors()
    {
        return $this->has_errors;
    }

    public function getDataToPersist()
    {
        $data = [];
        foreach ($this->getSteps() as $step) {
            $stepData = [
                'step_is_reachable' => $step->isReachable(),
                'step_is_complete'  => $step->isComplete()
            ];
            $data[$step->getIdentifier()] = $stepData;
        }
        return $data;
    }

    public function restorePersistedData(array $data)
    {
        foreach ($this->getSteps() as $step) {
            $id = $step->getIdentifier();
            if (array_key_exists($id, $data)) {
                $stepData = $data[$id];
                $step
                    ->setReachable($stepData['step_is_reachable'])
                    ->setComplete($stepData['step_is_complete'])
                ;
            }
        }
    }
}
