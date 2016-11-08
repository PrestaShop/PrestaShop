<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


use PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use PrestaShopBundle\Service\Hook\HookFinder;

class PaymentOptionsFinderCore extends HookFinder
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
