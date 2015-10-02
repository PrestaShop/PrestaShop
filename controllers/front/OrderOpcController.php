<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Business\Checkout\TermsAndConditions;

class OrderOpcControllerCore extends FrontController
{
    public $php_self = 'order-opc';

    private function render($template, array $params)
    {
        $this->context->smarty->assign($params);
        return $this->context->smarty->fetch($template);
    }

    private function getConditionsToApprove()
    {
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link = $this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool)Configuration::get('PS_SSL_ENABLED'));

        $termsAndConditions = new TermsAndConditions;
        $termsAndConditions
            ->setText(
                $this->l('I agree to the [terms of service] and will adhere to them unconditionally.'),
                $link
            )
            ->setIdentifier('terms-and-conditions')
        ;

        $allConditions = Hook::exec('termsAndConditions', [], null, true);
        if (!is_array($allConditions)) {
            $allConditions = [];
        }

        array_unshift($allConditions, $termsAndConditions);

        // TODO StarterTheme : currently we don't handle opening the link inside a modal.
        return TermsAndConditions::formatForTemplate($allConditions);
    }

    protected function renderPaymentOptions()
    {
        $advanced_payment_api = (bool)Configuration::get('PS_ADVANCED_PAYMENT_API');

        $all_conditions_approved = $this->checkWhetherAllConditionsAreApproved();

        if ($advanced_payment_api) {
            $payment_options = (new Adapter_AdvancedPaymentOptionsConverter)->getPaymentOptionsForTemplate();
            $selected_payment_option = Tools::getValue('select_payment_option');
            if ($selected_payment_option) {
                $all_conditions_approved = true;
            }
        } else {
            $payment_options = Hook::exec('displayPayment');
            $selected_payment_option = null;
        }

        return $this->render('checkout/payment.tpl', [
            'advanced_payment_api' => $advanced_payment_api,
            'payment_options' => $payment_options,
            'conditions_to_approve' => $this->getConditionsToApprove(),
            'approved_conditions' => $this->getSubmittedConditionsApproval(),
            'all_conditions_approved' => $all_conditions_approved,
            'selected_payment_option' => $selected_payment_option
        ]);
    }

    /**
     * Terms and conditions and other conditions are posted as an associative
     * array with the condition identifier as key.
     */
    private function getSubmittedConditionsApproval()
    {
        $required  = $this->getConditionsToApprove();
        $submitted = Tools::getValue('conditions_to_approve');
        if (!is_array($submitted)) {
            $submitted = [];
        }

        $approval = [];
        foreach ($required as $requiredConditionName => $unused) {
            $approval[$requiredConditionName] = !empty($submitted[$requiredConditionName]);
        }

        return $approval;
    }

    private function checkWhetherAllConditionsAreApproved()
    {
        foreach ($this->getSubmittedConditionsApproval() as $approved) {
            if (!$approved) {
                return false;
            }
        }
        return true;
    }

    public function getPaymentOptionsAction()
    {
        return $this->renderPaymentOptions();
    }

    public function init()
    {
        parent::init();
        if (($action = Tools::getValue('action'))) {
            $result = $this->{$action . 'Action'}();
            ob_end_clean();
            die($result);
        }

        $cart_presenter = new Adapter_CartPresenter;
        $this->context->smarty->assign([
            'payment_options' => $this->renderPaymentOptions(),
            'cart' => $cart_presenter->present($this->context->cart),
        ]);

        $this->setTemplate('checkout/opc.tpl');
    }
}
