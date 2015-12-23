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

        if ($this->step_is_reachable) {
            // we're done if
            // - the step was reached (= all previous steps complete)
            // - there are delivery options
            // - the is a selected delivery option 
            $this->step_is_complete = !empty($this->getCheckoutSession()->getDeliveryOptions()) && $this->getCheckoutSession()->getSelectedDeliveryOption();
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
