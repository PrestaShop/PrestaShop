<?php

use Symfony\Component\Translation\TranslatorInterface;

class CheckoutPaymentStepCore extends AbstractCheckoutStep
{
    protected $template = 'checkout/_partials/steps/payment.tpl';
    private $selected_payment_option;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        PaymentOptionsFinder $paymentOptionsFinder,
        ConditionsToApproveFinder $conditionsToApproveFinder
    ) {
        parent::__construct($context, $translator);
        $this->paymentOptionsFinder = $paymentOptionsFinder;
        $this->conditionsToApproveFinder = $conditionsToApproveFinder;
    }

    public function handleRequest(array $requestParams = array())
    {
        if (isset($requestParams['select_payment_option'])) {
            $this->selected_payment_option = $requestParams['select_payment_option'];
        }

        $this->setTitle(
            $this->getTranslator()->trans(
                'Payment',
                array(),
                'Checkout'
            )
        );
    }

    public function render(array $extraParams = array())
    {
        $deliveryOptions = $this->getCheckoutSession()->getDeliveryOptions();
        $deliveryOptionKey = $this->getCheckoutSession()->getSelectedDeliveryOption();
        $selectedDeliveryOption = $deliveryOptions[$deliveryOptionKey];
        unset($selectedDeliveryOption['product_list']);

        $assignedVars = array(
            'payment_options' => $this
                ->paymentOptionsFinder
                ->getPaymentOptionsForTemplate(),
            'conditions_to_approve' => $this
                ->conditionsToApproveFinder
                ->getConditionsToApproveForTemplate(),
            'selected_payment_option' => $this->selected_payment_option,
            'selected_delivery_option' => $selectedDeliveryOption,
            'show_final_summary' => Configuration::get('PS_FINAL_SUMMARY_ENABLED'),
            );

        return $this->renderTemplate($this->getTemplate(), $extraParams, $assignedVars);
    }
}
