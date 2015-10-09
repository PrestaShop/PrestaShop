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

class AddressControllerCore extends FrontController
{
    public $auth = true;
    public $guestAllowed = true;
    public $php_self = 'address';
    public $authRedirection = 'addresses';
    public $ssl = true;

    /**
     * @var Address Current address
     */
    protected $_address;
    protected $id_country;

    protected $address_form;
    protected $address_formatter;
    protected $address_fields = [];

    public $form_errors = [];

    /**
     * Initialize address controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        $id_address = (int)Tools::getValue('id_address', 0);
        $this->_address = new Address($id_address);

        if (!Validate::isLoadedObject($this->_address) || !Customer::customerHasAddress($this->context->customer->id, $id_address)) {
            Tools::redirect('index.php?controller=addresses');
        }

        if (Tools::isSubmit('delete')) {
            if ($this->_address->delete()) {
                if ($this->context->cart->id_address_invoice == $this->_address->id) {
                    unset($this->context->cart->id_address_invoice);
                }
                if ($this->context->cart->id_address_delivery == $this->_address->id) {
                    unset($this->context->cart->id_address_delivery);
                    $this->context->cart->updateAddressId($this->_address->id, (int)Address::getFirstCustomerAddressId(Context::getContext()->customer->id));
                }

                Tools::redirect('index.php?controller=addresses');
            } else {
                $this->errors[] = Tools::displayError('This address cannot be deleted.');
            }
        }

        $this->address_formatter = new Adapter_AddressFormatter(new Country(is_null($this->_address)? (int)$this->id_country : (int)$this->_address->id_country));
        $this->address_form = new Adapter_AddressForm(
            $this->address_formatter,
            Tools::getAllValues(),
            $this->context->customer,
            $this->context->language,
            new Adapter_Translator()
        );
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitAddress')) {
            $this->processSubmitAddress();
        } elseif (!Validate::isLoadedObject($this->_address) && Validate::isLoadedObject($this->context->customer)) {
            $_POST['firstname'] = $this->context->customer->firstname;
            $_POST['lastname'] = $this->context->customer->lastname;
            $_POST['company'] = $this->context->customer->company;
        }
    }

    /**
     * Process changes on an address
     */
    protected function processSubmitAddress()
    {
        // Check page token
        if ($this->context->customer->isLogged() && !$this->isTokenValid()) {
            $this->errors[] = Tools::displayError('Invalid token.');
            return false;
        }

        if ($this->address_form->hasErrors()) {
            $this->address_fields = $this->address_form->getAddressFormatWithErrors();
            return false;
        }

        //  StarterTheme: Save data
        $errors = $this->_address->validateController();
        $this->errors = array_merge($this->errors, $errors);

        if (empty($this->errors)) {
            $saved = $this->_address->save();

            if (!$saved) {
                $this->errors[] = $this->l('An error occurred while updating your address.');
                return false;
            }
        }

        // StarterTheme: Handle ajax for address validation !

        // Redirect to old page or current page
        if ($back = Tools::getValue('back')) {
            if ($back == Tools::secureReferrer(Tools::getValue('back'))) {
                Tools::redirect(html_entity_decode($back));
            }
            $mod = Tools::getValue('mod');
            Tools::redirect('index.php?controller='.$back.($mod ? '&back='.$mod : ''));
        } else {
            Tools::redirect('index.php?controller=addresses');
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->id_country = (int)Tools::getCountry();

        $address = $this->context->customer->getSimpleAddress(Tools::getValue('id_address'));
        foreach ($address as $key => $value) {
            if (isset($_POST[$key])) {
                $address[$key] = $_POST[$key];
            }
        }

        $this->assignVatNumber();

        $back = Tools::getValue('back');
        $mod = Tools::getValue('mod');

        if (empty($this->address_fields)) {
            $this->address_fields = $this->address_form->getAddressFormat();
        }

        $this->context->smarty->assign(array(
            'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
            'token' => Tools::getToken(false),
            'select_address' => (int)Tools::getValue('select_address'),
            'address' => $address,
            'countries' => $this->address_form->getCountryList(),
            'address_fields' => $this->address_fields,
            'back' => Tools::safeOutput($back),
            'mod' => Tools::safeOutput($mod),
        ));

        if (isset($this->context->cookie->account_created)) {
            $this->context->smarty->assign('account_created', 1);
            unset($this->context->cookie->account_created);
        }

        $this->setTemplate('customer/address.tpl');
    }

    /**
     * Assign template vars related to vat number
     * @todo move this in vatnumber module !
     */
    protected function assignVatNumber()
    {
        $vat_number_exists = file_exists(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');
        $vat_number_management = Configuration::get('VATNUMBER_MANAGEMENT');
        if ($vat_number_management && $vat_number_exists) {
            include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');
        }

        if ($vat_number_management && $vat_number_exists && VatNumber::isApplicable((int)Tools::getCountry())) {
            $vat_display = 2;
        } elseif ($vat_number_management) {
            $vat_display = 1;
        } else {
            $vat_display = 0;
        }

        $this->context->smarty->assign(array(
            'vatnumber_ajax_call' => file_exists(_PS_MODULE_DIR_.'vatnumber/ajax.php'),
            'vat_display' => $vat_display,
        ));
    }

    public function displayAjax()
    {
        if (count($this->errors)) {
            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors
            );
            $this->ajaxDie(json_encode($return));
        }
    }
}
