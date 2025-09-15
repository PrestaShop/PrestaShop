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

    protected $template = 'checkout/one-page-checkout';

    /**
     * @var CustomerForm
     */
    private CustomerForm $formCustomer;

    /**
     * @var CustomerLoginForm
     */
    private CustomerLoginForm $formLogin;

    /**
     * @var GuestForm
     */
    private GuestForm $formGuest;


    /**
     * Initialize order controller.
     *
     * @see FrontController::init()
     */
    public function init(): void
    {
        parent::init();
        $this->formCustomer = $this->makeCustomerForm(true);
        $this->formLogin = $this->makeLoginForm(true);
        $this->formGuest = $this->makeGuestForm();

    }

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
            $this->formLogin->submit();
            $responseData['idCustomer'] = $this->cookie->id_customer;

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

        try{
            $this->formCustomer->fillWith(Tools::getAllValues());
            $this->formCustomer->submit();
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
        parent::initContent();
        $this->registerJavascript('one-page-checkout', '/themes/one-page-checkout.js', ['position' => 'bottom', 'priority' => 1000]);

        $this->context->smarty->assign([
            'guest_allowed' => false,
            'guest_form'=> $this->formGuest,
            'register_form' => $this->formCustomer,
            'login_form' => $this->formLogin,
            'display_transaction_updated_info' => Tools::getIsset('updatedTransaction'),
            'tos_cms' => $this->getDefaultTermsAndConditions(),
        ]);

        $this->setTemplate('checkout/one-page-checkout');
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
}
