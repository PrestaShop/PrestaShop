<?php

class CheckoutPaymentStepCore extends AbstractCheckoutStep
{
    public function init(array $requestParams = [])
    {
        $this->setTitle(
            $this->getTranslator()->trans(
                'Payment',
                [],
                'Checkout'
            )
        );
    }

    public function render()
    {
        return $this->renderTemplate(
            'checkout/payment-step.tpl', [

            ]
        );
    }
}
