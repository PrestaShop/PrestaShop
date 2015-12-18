<?php

use Symfony\Component\Translation\TranslatorInterface;

class CheckoutAddressesStepCore extends AbstractCheckoutStep
{
    private $addressForm;

    public function __construct(
        Smarty $smarty,
        TranslatorInterface $translator,
        CustomerAddressForm $addressForm
    ) {
        parent::__construct($smarty, $translator);
        $this->addressForm = $addressForm;
    }

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
                'address_form' => $this->addressForm->getProxy()
            ]
        );
    }
}
