<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

class PaymentOptionsFinderCore
{
    public function getPaymentOptions()
    {
        // Payment options coming from intermediate, deprecated version of the Advanced API
        $rawDisplayPaymentEUOptions = Hook::exec('displayPaymentEU', [], null, true);

        if (!is_array($rawDisplayPaymentEUOptions)) {
            $rawDisplayPaymentEUOptions = [];
        }

        $displayPaymentEUOptions = array_map(
            ['PrestaShop\PrestaShop\Core\Payment\PaymentOption', 'convertLegacyOption'],
            $rawDisplayPaymentEUOptions
        );

        // Payment options coming from regular Advanced API
        $advancedPaymentOptions = Hook::exec('advancedPaymentOptions', array(), null, true);
        if (!is_array($advancedPaymentOptions)) {
            $advancedPaymentOptions = [];
        }


        // Payment options coming from regular Advanced API
        $newOption = Hook::exec('paymentOptions', array(), null, true);
        if (!is_array($newOption)) {
            $newOption = [];
        }

        $paymentOptions = array_merge($displayPaymentEUOptions, $advancedPaymentOptions, $newOption);

        foreach ($paymentOptions as $paymentOptionKey => $paymentOption) {
            if (!is_array($paymentOption)) {
                unset($paymentOptions[$paymentOptionKey]);
            }
        }  

        return $paymentOptions;
    }

    public function getPaymentOptionsForTemplate()
    {
        $id = 0;
        return array_map(function (array $options) use (&$id) {
            return array_map(function (PaymentOption $option) use (&$id) {
                ++$id;
                $formattedOption = $option->toArray();
                $formattedOption['id'] = 'payment-option-' . $id;

                if ($formattedOption['form']) {
                    $decorator = new PaymentOptionFormDecorator;
                    $formattedOption['form'] = $decorator->addHiddenSubmitButton(
                        $formattedOption['form'],
                        $formattedOption['id']
                    );
                }

                return $formattedOption;
            }, $options);
        }, $this->getPaymentOptions());
    }
}
