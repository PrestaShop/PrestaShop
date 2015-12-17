<?php

class CheckoutAddressesStepCore extends AbstractCheckoutStep
{
    public function init(array $requestParams = [])
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
