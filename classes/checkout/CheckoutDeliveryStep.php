<?php

class CheckoutDeliveryStepCore extends AbstractCheckoutStep
{
    public function init(array $requestParams = [])
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
