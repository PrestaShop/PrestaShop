<?php

class CheckoutAddressesStepCore extends AbstractCheckoutStep
{
    public function handleRequest(array $requestParams = [])
    {
        $this->setTitle(
            $this->getTranslator()->trans(
                'Addresses',
                [],
                'Checkout'
            )
        );
    }

    public function render()
    {
        return $this->renderTemplate(
            'checkout/addresses-step.tpl', [

            ]
        );
    }
}
