<?php

class CheckoutDeliveryStepCore extends AbstractCheckoutStep
{
    public function handleRequest(array $requestParams = [])
    {
        if (isset($requestParams['delivery_option'])) {
            $this->getCheckoutSession()->setDeliveryOption(
                $requestParams['delivery_option']
            );
        }

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
                'id_address'        => $this->getCheckoutSession()->getIdAddressDelivery(),
                'delivery_options'  => $this->getCheckoutSession()->getDeliveryOptions(),
                'delivery_option'   => $this->getCheckoutSession()->getSelectedDeliveryOption()
            ]
        );
    }
}
