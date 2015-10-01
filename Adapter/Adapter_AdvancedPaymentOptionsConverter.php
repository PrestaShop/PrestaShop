<?php

class Adapter_AdvancedPaymentOptionsConverter
{
    public function getPaymentOptions()
    {
        // Payment options coming from intermediate, deprecated version of the Advanced API
        $rawDisplayPaymentEUOptions = Hook::exec('displayPaymentEU', [], null, true);
        $displayPaymentEUOptions = array_map(
            ['Core_Business_Payment_PaymentOption', 'convertLegacyOption'],
            $rawDisplayPaymentEUOptions
        );

        // Payment options coming from regular Advanced API
        $advancedPaymentOptions = Hook::exec('advancedPaymentOptions', array(), null, true);
        if (!is_array($advancedPaymentOptions)) {
            $advancedPaymentOptions = [];
        }

        $paymentOptions = array_merge($displayPaymentEUOptions, $advancedPaymentOptions);

        return $paymentOptions;
    }

    public function getPaymentOptionsForTemplate()
    {
        $id = 0;
        return array_map(function (array $options) use (&$id) {
            return array_map(function (Core_Business_Payment_PaymentOption $option) use (&$id) {
                ++$id;
                $formattedOption = $option->toArray();
                $formattedOption['id'] = 'advanced-payment-option-' . $id;
                if (!$formattedOption['method']) {
                    $formattedOption['method'] = 'GET';
                }
                return $formattedOption;
            }, $options);
        }, $this->getPaymentOptions());
    }
}
