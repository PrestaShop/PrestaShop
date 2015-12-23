<?php

use Symfony\Component\Translation\TranslatorInterface;

class CheckoutPaymentStepCore extends AbstractCheckoutStep
{
    private $selected_payment_option;

    public function __construct(
        Smarty $smarty,
        TranslatorInterface $translator,
        PaymentOptionsFinder $paymentOptionsFinder,
        ConditionsToApproveFinder $conditionsToApproveFinder
    ) {
        parent::__construct($smarty, $translator);
        $this->paymentOptionsFinder = $paymentOptionsFinder;
        $this->conditionsToApproveFinder = $conditionsToApproveFinder;
    }

    public function handleRequest(array $requestParams = [])
    {
        if (isset($requestParams['select_payment_option'])) {
            $this->selected_payment_option = $requestParams['select_payment_option'];
        }

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
                'payment_options' => $this
                    ->paymentOptionsFinder
                    ->getPaymentOptionsForTemplate(),
                'conditions_to_approve'   => $this
                    ->conditionsToApproveFinder
                    ->getConditionsToApproveForTemplate(),
                'selected_payment_option' => $this->selected_payment_option
            ]
        );
    }
}
