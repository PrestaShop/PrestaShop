<?php

class CheckoutPaymentStepCore extends AbstractCheckoutStep
{
    public function handleRequest(array $requestParams = [])
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
