<?php

class CheckoutDeliveryStepCore extends AbstractCheckoutStep
{
    protected $template = 'checkout/delivery-step.tpl';

    private $recyclablePackAllowed = false;
    private $giftAllowed = false;
    private $giftCost = 0;
    private $includeTaxes = false;
    private $displayTaxesLabel = false;

    public function setRecyclablePackAllowed($recyclablePackAllowed)
    {
        $this->recyclablePackAllowed = $recyclablePackAllowed;
        return $this;
    }

    public function isRecyclablePackAllowed()
    {
        return $this->recyclablePackAllowed;
    }

    public function setGiftAllowed($giftAllowed)
    {
        $this->giftAllowed = $giftAllowed;
        return $this;
    }

    public function isGiftAllowed()
    {
        return $this->giftAllowed;
    }

    public function setGiftCost($giftCost)
    {
        $this->giftCost = $giftCost;
        return $this;
    }

    public function getGiftCost()
    {
        return $this->giftCost;
    }

    public function setIncludeTaxes($includeTaxes)
    {
        $this->includeTaxes = $includeTaxes;
        return $this;
    }

    public function getIncludeTaxes()
    {
        return $this->includeTaxes;
    }

    public function setDisplayTaxesLabel($displayTaxesLabel)
    {
        $this->displayTaxesLabel = $displayTaxesLabel;
        return $this;
    }

    public function getDisplayTaxesLabel()
    {
        return $this->displayTaxesLabel;
    }

    public function getGiftCostForLabel()
    {
        if ($this->getGiftCost() != 0) {
            $taxLabel = '';
            $pricePresenter = new Adapter_PricePresenter();

            if ($this->getIncludeTaxes() && $this->getDisplayTaxesLabel()) {
                $taxLabel .= ' tax incl.';
            } elseif ($this->getDisplayTaxesLabel()) {
                $taxLabel .= ' tax excl.';
            }

            return sprintf(
                $this->getTranslator()->trans(
                    ' (additional cost of %s%s)',
                    [],
                    'Checkout'
                ),
                $pricePresenter->convertAndFormat($this->getGiftCost()),
                $taxLabel
            );
        }

        return '';
    }

    public function handleRequest(array $requestParams = [])
    {
        if (isset($requestParams['delivery_option'])) {
            $this->getCheckoutSession()->setDeliveryOption(
                $requestParams['delivery_option']
            );
            $this->getCheckoutSession()->setRecyclable(
                isset($requestParams['recyclable']) ? $requestParams['recyclable'] : false
            );
            $this->getCheckoutSession()->setGift(
                isset($requestParams['gift']) ? $requestParams['gift'] : false,
                (isset($requestParams['gift']) && isset($requestParams['gift_message'])) ? $requestParams['gift_message'] : ''
            );
        }

        if ($this->step_is_reachable && isset($requestParams['confirmDeliveryOption'])) {
            // we're done if
            // - the step was reached (= all previous steps complete)
            // - user has clicked on "continue"
            // - there are delivery options
            // - the is a selected delivery option
            $this->step_is_complete =
                !empty($this->getCheckoutSession()->getDeliveryOptions()) && $this->getCheckoutSession()->getSelectedDeliveryOption()
            ;
        }

        $this->setTitle(
            $this->getTranslator()->trans(
                'Delivery Method',
                [],
                'Checkout'
            )
        );

        Hook::exec('actionCarrierProcess', array('cart' => $this->getCheckoutSession()->getCart()));
    }

    public function render(array $extraParams = [])
    {
        return $this->renderTemplate(
            $this->template,
            $extraParams,
            [
                'id_address'            => $this->getCheckoutSession()->getIdAddressDelivery(),
                'delivery_options'      => $this->getCheckoutSession()->getDeliveryOptions(),
                'delivery_option'       => $this->getCheckoutSession()->getSelectedDeliveryOption(),
                'recyclable'            => $this->getCheckoutSession()->isRecyclable(),
                'recyclablePackAllowed' => $this->isRecyclablePackAllowed(),
                'gift'                  => [
                    'allowed'   => $this->isGiftAllowed(),
                    'isGift'    => $this->getCheckoutSession()->getGift()['isGift'],
                    'label'     => $this->getTranslator()->trans(
                        'I would like my order to be gift wrapped'.$this->getGiftCostForLabel(),
                        [],
                        'Checkout'
                    ),
                    'message'   => $this->getCheckoutSession()->getGift()['message']
                ]
            ]
        );
    }
}
