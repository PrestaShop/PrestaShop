<?php

class CheckoutDeliveryStepCore extends AbstractCheckoutStep
{
    public function handleRequest(array $requestParams = [])
    {
        $this->setTitle(
            $this->getTranslator()->trans(
                'Delivery Method',
                [],
                'Checkout'
            )
        );
    }

    public function render()
    {
        return $this->renderTemplate(
            'checkout/delivery-step.tpl', [

            ]
        );
    }
}
