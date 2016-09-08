<?php

use PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use PrestaShopBundle\Service\Hook\Finder;

class PaymentOptionsFinderCore extends Finder
{    
    public function find() //getPaymentOptions()
    {
        // Payment options coming from intermediate, deprecated version of the Advanced API
        $this->hookName = 'displayPaymentEU';
        $rawDisplayPaymentEUOptions = parent::find();

        if (!is_array($rawDisplayPaymentEUOptions)) {
            $rawDisplayPaymentEUOptions = array();
        }

        $displayPaymentEUOptions = array_map(
            array('PrestaShop\PrestaShop\Core\Payment\PaymentOption', 'convertLegacyOption'),
            $rawDisplayPaymentEUOptions
        );

        // Payment options coming from regular Advanced API
        $this->hookName = 'advancedPaymentOptions';
        $advancedPaymentOptions = parent::find();
        if (!is_array($advancedPaymentOptions)) {
            $advancedPaymentOptions = array();
        }

        // Payment options coming from regular Advanced API
        $this->hookName = 'paymentOptions';
        $this->expectedInstanceClasses = array('PrestaShop\PrestaShop\Core\Payment\PaymentOption');
        $newOption = parent::find();
        if (!is_array($newOption)) {
            $newOption = array();
        }

        $paymentOptions = array_merge($displayPaymentEUOptions, $advancedPaymentOptions, $newOption);

        foreach ($paymentOptions as $paymentOptionKey => $paymentOption) {
            if (!is_array($paymentOption)) {
                unset($paymentOptions[$paymentOptionKey]);
            }
        }

        return $paymentOptions;
    }

    public function present() //getPaymentOptionsForTemplate()
    {
        $id = 0;

        return array_map(function (array $options) use (&$id) {
            return array_map(function (PaymentOption $option) use (&$id) {
                ++$id;
                $formattedOption = $option->toArray();
                $formattedOption['id'] = 'payment-option-'.$id;

                if ($formattedOption['form']) {
                    $decorator = new PaymentOptionFormDecorator();
                    $formattedOption['form'] = $decorator->addHiddenSubmitButton(
                        $formattedOption['form'],
                        $formattedOption['id']
                    );
                }

                return $formattedOption;
            }, $options);
        }, $this->find());
    }
}
