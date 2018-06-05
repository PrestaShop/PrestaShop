<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


use PrestaShop\PrestaShop\Core\Payment\PaymentOptionFormDecorator;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;
use PrestaShopBundle\Service\Hook\HookFinder;

class PaymentOptionsFinderCore extends HookFinder
{
    /**
     * Collects available payment options from three different hooks
     * 
     * @return array An array of available payment options
     * 
     * @see HookFinder::find()
     */
    public function find() //getPaymentOptions()
    {
        // Payment options coming from intermediate, deprecated version of the Advanced API
        $this->hookName = 'displayPaymentEU';
        $rawDisplayPaymentEUOptions = parent::find();
        $paymentOptions = array_map(
            array('PrestaShop\PrestaShop\Core\Payment\PaymentOption', 'convertLegacyOption'),
            $rawDisplayPaymentEUOptions
        );

        // Advanced payment options coming from regular Advanced API
        $this->hookName = 'advancedPaymentOptions';
        $paymentOptions = array_merge($paymentOptions, parent::find());

        // Payment options coming from regular Advanced API
        $this->hookName = 'paymentOptions';
        $this->expectedInstanceClasses = array('PrestaShop\PrestaShop\Core\Payment\PaymentOption');
        $paymentOptions = array_merge($paymentOptions, parent::find());
        
        // Safety check
        foreach ($paymentOptions as $moduleName => $paymentOption) {	
            if (!is_array($paymentOption)) {	
                unset($paymentOptions[$moduleName]);	
            }	
        }
        
        return $paymentOptions;
    }

    public function findFree()
    {
        $freeOption = new PaymentOption();
        $freeOption->setModuleName('free_order')
            ->setCallToActionText(Context::getContext()->getTranslator()->trans('Free order', array(), 'Admin.Orderscustomers.Feature'))
            ->setAction(Context::getContext()->link->getPageLink('order-confirmation', null, null, 'free_order=1'));

        return array('free_order' => array($freeOption));
    }

    public function present($free = false) //getPaymentOptionsForTemplate()
    {
        $id = 0;

        $find = $free ? $this->findFree() : $this->find();

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
        }, $find);
    }
}
