<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class CheckoutDeliveryStepCore extends AbstractCheckoutStep
{
    protected $template = 'checkout/_partials/steps/shipping.tpl';

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
            $priceFormatter = new PriceFormatter();

            if ($this->getIncludeTaxes() && $this->getDisplayTaxesLabel()) {
                $taxLabel .= ' tax incl.';
            } elseif ($this->getDisplayTaxesLabel()) {
                $taxLabel .= ' tax excl.';
            }

            return sprintf(
                $this->getTranslator()->trans(
                    ' (additional cost of %giftcost% %taxlabel%)',
                    array(
                        '%giftcost%' => $priceFormatter->convertAndFormat($this->getGiftCost()),
                        '%taxlabel%' => $taxLabel,
                    ),
                    'Shop.Theme.Checkout'
                )
            );
        }

        return '';
    }

    public function handleRequest(array $requestParams = array())
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
            // - the module associated to the delivery option confirms
            $deliveryOptions = $this->getCheckoutSession()->getDeliveryOptions();
            $this->step_is_complete =
                !empty($deliveryOptions) && $this->getCheckoutSession()->getSelectedDeliveryOption() && $this->isModuleComplete($requestParams)
            ;
        }

        $this->setTitle(
            $this->getTranslator()->trans(
                'Shipping Method',
                array(),
                'Shop.Theme.Checkout'
            )
        );

        Hook::exec('actionCarrierProcess', array('cart' => $this->getCheckoutSession()->getCart()));
    }

    public function render(array $extraParams = array())
    {
        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams,
            array(
                'hookDisplayBeforeCarrier' => Hook::exec('displayBeforeCarrier', array('cart' => $this->getCheckoutSession()->getCart())),
                'hookDisplayAfterCarrier' => Hook::exec('displayAfterCarrier', array('cart' => $this->getCheckoutSession()->getCart())),
                'id_address' => $this->getCheckoutSession()->getIdAddressDelivery(),
                'delivery_options' => $this->getCheckoutSession()->getDeliveryOptions(),
                'delivery_option' => $this->getCheckoutSession()->getSelectedDeliveryOption(),
                'recyclable' => $this->getCheckoutSession()->isRecyclable(),
                'recyclablePackAllowed' => $this->isRecyclablePackAllowed(),
                'gift' => array(
                    'allowed' => $this->isGiftAllowed(),
                    'isGift' => $this->getCheckoutSession()->getGift()['isGift'],
                    'label' => $this->getTranslator()->trans(
                        'I would like my order to be gift wrapped'.$this->getGiftCostForLabel(),
                        array(),
                        'Checkout'
                    ),
                    'message' => $this->getCheckoutSession()->getGift()['message'],
                ),
            )
        );
    }

    protected function isModuleComplete($requestParams)
    {
        $deliveryOptions = $this->getCheckoutSession()->getDeliveryOptions();
        $currentDeliveryOption = $deliveryOptions[$this->getCheckoutSession()->getSelectedDeliveryOption()];
        if (!$currentDeliveryOption['is_module']) {
            return true;
        }

        $isComplete = true;
        Hook::exec(
            'actionValidateStepComplete',
            array(
                'step_name' => 'delivery',
                'request_params' => $requestParams,
                'completed' => &$isComplete,
            ),
            Module::getModuleIdByName($currentDeliveryOption['external_module_name']));
        return $isComplete;
    }
}
