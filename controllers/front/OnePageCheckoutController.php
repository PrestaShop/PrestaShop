<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Checkout\TermsAndConditions;
use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy;
use PrestaShopBundle\Translation\TranslatorComponent;

class OnePageCheckoutControllerCore extends CheckoutController
{
    /** @var string */
    public $php_self = 'one-page-checkout';
    /** @var string */
    public $page_name = 'one-page-checkout';

    protected $template = 'checkout/checkout';

    public function postProcess(): void
    {
        parent::postProcess();

        if(Tools::isSubmit('ajax')){
            if(Tools::isSubmit('submitCreateGuest')){
                $this->displayAjaxGuestCreate();
            } else if( Tools::isSubmit('submitLogin')){
                $this->displayAjaxLogin();
            } else if( Tools::isSubmit('submitCustomer')){
                $this->displayAjaxCustomer();
            } else if( Tools::isSubmit('submitCustomer')){
                $this->displayAjaxCustomerAddress();
            }

        }
    }

    /**
     * @return CheckoutProcess
     */
    public function getCheckoutProcess(): CheckoutProcess
    {
        return $this->checkoutProcess;
    }

    public function displayAjaxGuestCreate(): void
    {
        $responseData = [
            'errors' => false,
            'idCustomer' => '',
        ];

        try{

            $customerPersister = new CustomerPersister($this->context,
                $this->get('hashing'),
                $this->getTranslator(),
                true
            );
            $customer = new Customer(Tools::getValue('id_customer')??null);
            $customer->is_guest = true;
            $customer->email = Tools::getValue('email');
            $customer->firstname = '';
            $customer->lastname = '';
            $ok = $customerPersister->save($customer,'');

            if($ok === false){
                $responseData['errors'] = true;
            } else {
                $responseData['idCustomer'] = $customer->id;
            }

        }catch (Exception $e){
            $responseData['errors'] = true;
            $responseData['message'] = $e->getMessage();
        }
        header('Content-Type: application/json');
        $this->ajaxRender(json_encode($responseData));
    }

    public function displayAjaxLogin(): void
    {
        $responseData = [
            'errors' => false,
            'idCustomer' => '',
        ];

        try{
            $formLogin = $this->makeLoginForm();
            $formLogin->fillWith(Tools::getAllValues());
            $formLogin->submit();
            $responseData['idCustomer'] = $this->context->customer->id;

        }catch (Exception $e){
            $responseData['errors'] = true;
            $responseData['message'] = $e->getMessage();
        }
        header('Content-Type: application/json');
        $this->ajaxRender(json_encode($responseData));
    }

    public function displayAjaxCustomer(): void
    {
        $responseData = [
            'errors' => false,
            'idCustomer' => '',
        ];

        $formCustomer = $this->makeCustomerForm();
        try{
           $formCustomer->fillWith(Tools::getAllValues());
           $formCustomer->submit();
            $responseData['idCustomer'] = $this->context->customer->id;

        }catch (Exception $e){
            $responseData['errors'] = true;
            $responseData['message'] = $e->getMessage();
        }
        header('Content-Type: application/json');
        $this->ajaxRender(json_encode($responseData));
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        $this->registerJavascript('one-page-checkout', '/themes/one-page-checkout.js', ['position' => 'bottom', 'priority' => 1000]);
        parent::initContent();

        foreach ($this->checkoutProcess->getSteps() as $step){
            switch ($step->getIdentifier()){
                case 'checkout-personal-information-step':
                    $step->setReachable(true);
                    $step->setCurrent(true);
                    $step->setTemplate('checkout/_partials/opc/personal-information.tpl');
                    break;
                case 'checkout-addresses-step':
                    $step->setReachable(true);
                    $step->setCurrent(true);
                    $step->setTemplate('checkout/_partials/opc/addresses.tpl');
                    break;
            }
        }

    }

    public function displayAjaxAddressForm(): void
    {
        $addressForm = $this->makeAddressForm();

        if (Tools::getIsset('id_address') && ($id_address = (int) Tools::getValue('id_address'))) {
            $addressForm->loadAddressById($id_address);
        }

        if (Tools::getIsset('id_country')) {
            $addressForm->fillWith(['id_country' => Tools::getValue('id_country')]);
        }

        $stepTemplateParameters = [];
        foreach ($this->checkoutProcess->getSteps() as $step) {
            if ($step instanceof CheckoutAddressesStep) {
                $stepTemplateParameters = $step->getTemplateParameters();
            }
        }

        $templateParams = array_merge(
            $addressForm->getTemplateVariables(),
            $stepTemplateParameters,
            ['type' => 'delivery']
        );

        ob_end_clean();
        header('Content-Type: application/json');

        $this->ajaxRender(json_encode([
            'address_form' => $this->render(
                'checkout/_partials/address-form',
                $templateParams
            ),
        ]));
    }

    /**
     * @param CheckoutSession $session
     * @param TranslatorComponent $translator
     *
     * @return CheckoutProcess
     */
    protected function buildCheckoutProcess(CheckoutSession $session, $translator)
    {
        $this->checkoutProcess = new CheckoutProcess(
            $this->context,
            $session
        );

        $this->checkoutProcess
            ->addStep(new CheckoutPersonalInformationStep(
                $this->context,
                $translator,
                $this->makeLoginForm(),
                $this->makeCustomerForm(),
                $this->makeGuestForm(),
            ))
            ->addStep(new CheckoutAddressesStep(
                $this->context,
                $translator,
                $this->makeAddressForm()
            ));

        if (!$this->context->cart->isVirtualCart()) {
            $checkoutDeliveryStep = new CheckoutDeliveryStep(
                $this->context,
                $translator
            );

            $checkoutDeliveryStep
                ->setRecyclablePackAllowed((bool) Configuration::get('PS_RECYCLABLE_PACK'))
                ->setGiftAllowed((bool) Configuration::get('PS_GIFT_WRAPPING'))
                ->setIncludeTaxes(
                    !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer)
                    && (int) Configuration::get('PS_TAX')
                )
                ->setDisplayTaxesLabel(Configuration::get('PS_TAX'))
                ->setGiftCost(
                    $this->context->cart->getGiftWrappingPrice(
                        $checkoutDeliveryStep->getIncludeTaxes()
                    )
                );

            $this->checkoutProcess ->addStep($checkoutDeliveryStep);
        }

        $this->checkoutProcess
            ->addStep(new CheckoutPaymentStep(
                $this->context,
                $translator,
                new PaymentOptionsFinder(),
                new ConditionsToApproveFinder(
                    $this->context,
                    $translator
                )
            ));

        return $this->checkoutProcess;
    }

    public function makeCustomerForm()
    {
        $customerForm = parent::makeCustomerForm();
        $customerForm->setAction($this->getCurrentURL().'?ajax=1&action=customer=1');
        return $customerForm;
    }

    public function makeCustomerFormatter()
    {
        $customerFormatter = parent::makeCustomerFormatter();
        $customerFormatter->setPasswordRequired(true);
        return $customerFormatter;
    }

    public function makeGuestForm()
    {
        $guestForm = parent::makeGuestForm();
        $guestForm->setAction($this->getCurrentURL().'?ajax=1&action=createGuest');
        return $guestForm;
    }

    public function makeLoginForm()
    {
        $loginForm = parent::makeLoginForm();
        $loginForm->setAction($this->getCurrentURL().'?ajax=1&action=login');
        return $loginForm;
    }

    public function makeAddressForm()
    {
        $addressForm = parent::makeAddressForm();
        $addressForm->setAction($this->getCurrentURL().'?ajax=1&action=customerAddress');
        return $addressForm;

    }

    public function displayAjaxCustomerAddress()
    {
        foreach ($this->getCheckoutProcess()->getSteps() as $step){
            if($step->getIdentifier() == 'checkout-addresses-step'){
                $step->handleRequest(Tools::getAllValues());
            }
        }
    }
}
