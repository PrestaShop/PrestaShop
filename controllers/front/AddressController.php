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

    public $form_errors = [];

    /**
     * Initialize address controller
     * @see FrontController::init()
     */
    public function init()
    {
        parent::init();

        // Get address ID
        $id_address = 0;
        if ($this->ajax && Tools::isSubmit('type')) {
            if (Tools::getValue('type') == 'delivery' && isset($this->context->cart->id_address_delivery)) {
                $id_address = (int)$this->context->cart->id_address_delivery;
            } elseif (Tools::getValue('type') == 'invoice' && isset($this->context->cart->id_address_invoice)
                        && $this->context->cart->id_address_invoice != $this->context->cart->id_address_delivery) {
                $id_address = (int)$this->context->cart->id_address_invoice;
            }
        } else {
            $id_address = (int)Tools::getValue('id_address', 0);
        }

        // Initialize address
        if ($id_address) {
            $this->_address = new Address($id_address);
            if (Validate::isLoadedObject($this->_address) && Customer::customerHasAddress($this->context->customer->id, $id_address)) {
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
                    }
                    $this->errors[] = Tools::displayError('This address cannot be deleted.');
                }
            } elseif ($this->ajax) {
                exit;
            } else {
                Tools::redirect('index.php?controller=addresses');
            }
        }
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
        $address = new Address();
        $address->validateController();
        $this->validateFormController();
        $address->id_customer = (int)$this->context->customer->id;

        // Check page token
        if ($this->context->customer->isLogged() && !$this->isTokenValid()) {
            $this->errors[] = Tools::displayError('Invalid token.');
        }

        // Check phone
        if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && !Tools::getValue('phone') && !Tools::getValue('phone_mobile')) {
            $this->form_errors['phone'][] = $this->form_errors['phone_mobile'][] = $this->l('You must register at least one phone number.');
        }
        if ($address->id_country) {
            // Check country
            if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country)) {
                throw new PrestaShopException('Country cannot be loaded with address->id_country');
            }

            if ((int)$country->contains_states && !(int)$address->id_state) {
                $this->form_errors['state'][] = $this->l('This country requires you to chose a State.');
            }

            if (!$country->active) {
                $this->form_errors['country'][] = $this->l('This country is not active.');
            }

            $postcode = Tools::getValue('postcode');
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $this->form_errors['postcode'][] = sprintf($this->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->form_errors['postcode'][] = $this->l('A Zip/Postal code is required.');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->form_errors['postcode'][] = $this->l('The Zip/Postal code is invalid.');
            }

            // Check country DNI
            if ($country->isNeedDni() && (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni')))) {
                $this->form_errors['dni'][] = $this->l('The identification number is incorrect or has already been used.');
            } elseif (!$country->isNeedDni()) {
                $address->dni = null;
            }
        } else {
            $this->form_errors['country'][] = $this->l('This information is required.');
        }

        // Check if the alias exists
        if (!$this->context->customer->is_guest && !empty($_POST['alias']) && (int)$this->context->customer->id > 0) {
            $id_address = Tools::getValue('id_address');
            if (Configuration::get('PS_ORDER_PROCESS_TYPE') && (int)Tools::getValue('opc_id_address_'.Tools::getValue('type')) > 0) {
                $id_address = Tools::getValue('opc_id_address_'.Tools::getValue('type'));
            }

            if (Address::aliasExist(Tools::getValue('alias'), (int)$id_address, (int)$this->context->customer->id)) {
                $this->form_errors['alias'][] = sprintf($this->l('The alias "%s" has already been used. Please select another one.'), Tools::safeOutput(Tools::getValue('alias')));
            }
        }

        // Check the requires fields which are settings in the BO
        $this->form_errors = array_merge($this->form_errors, $address->validateFieldsRequiredDatabase());

        // Don't continue this process if we have errors !
        if (($this->form_errors || $this->errors) && !$this->ajax) {
            return;
        }

        // If we edit this address, delete old address and create a new one
        if (Validate::isLoadedObject($this->_address)) {
            if (Validate::isLoadedObject($country) && !$country->contains_states) {
                $address->id_state = 0;
            }
            $address_old = $this->_address;
            if (Customer::customerHasAddress($this->context->customer->id, (int)$address_old->id)) {
                if ($address_old->isUsed()) {
                    $address_old->delete();
                } else {
                    $address->id = (int)$address_old->id;
                    $address->date_add = $address_old->date_add;
                }
            }
        }

        if ($this->ajax && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
            $this->errors = array_unique(array_merge($this->errors, $address->validateController()));
            if (count($this->errors)) {
                $return = array(
                    'hasError' => (bool)$this->errors,
                    'errors' => $this->errors
                );
                $this->ajaxDie(json_encode($return));
            }
        }

        // Save address
        if ($result = $address->save()) {
            // Update id address of the current cart if necessary
            if (isset($address_old) && $address_old->isUsed()) {
                $this->context->cart->updateAddressId($address_old->id, $address->id);
            } else { // Update cart address
                $this->context->cart->autosetProductAddress();
            }

            if ((bool)Tools::getValue('select_address', false) == true || (Tools::getValue('type') == 'invoice' && Configuration::get('PS_ORDER_PROCESS_TYPE'))) {
                $this->context->cart->id_address_invoice = (int)$address->id;
            } elseif (Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                $this->context->cart->id_address_invoice = (int)$this->context->cart->id_address_delivery;
            }
            $this->context->cart->update();

            if ($this->ajax) {
                $return = array(
                    'hasError' => (bool)$this->errors,
                    'errors' => $this->errors,
                    'id_address_delivery' => (int)$this->context->cart->id_address_delivery,
                    'id_address_invoice' => (int)$this->context->cart->id_address_invoice
                );
                $this->ajaxDie(json_encode($return));
            }

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
        $this->errors[] = Tools::displayError('An error occurred while updating your address.');
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

        $countries = $this->getFormCountries();
        $this->assignVatNumber();
        $this->assignAddressFormat();

        $back = Tools::getValue('back');
        $mod = Tools::getValue('mod');

        $this->context->smarty->assign(array(
            'form_validation' => Address::$definition['fields'],
            'form_errors' => $this->form_errors,
            'errors' => $this->errors,
            'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
            'ajaxurl' => _MODULE_DIR_,
            'token' => Tools::getToken(false),
            'select_address' => (int)Tools::getValue('select_address'),
            'address' => $address,
            'countries' => $countries,
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
     * Assign template vars related to countries display
     */
    protected function getFormCountries()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }

        return $countries;
    }

    /**
     * Assign template vars related to address format
     */
    protected function assignAddressFormat()
    {
        $id_country = is_null($this->_address)? (int)$this->id_country : (int)$this->_address->id_country;
        $requireFormFieldsList = AddressFormat::getFieldsRequired();
        $ordered = AddressFormat::getOrderedAddressFields($id_country, true, true);
        $ordered = array_unique(array_merge($ordered, $requireFormFieldsList));

        $ordered_address_fields = [];
        foreach ($ordered as $field) {
            $ordered_address_fields[$field] = [
                'required' => in_array($field, $requireFormFieldsList),
            ];
        }

        $this->context->smarty->assign(array(
            'address_fields' => $ordered_address_fields,
        ));
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

    public function validateFormController()
    {
        $requireFormFieldsList = AddressFormat::getFieldsRequired();

        foreach ($_POST as $field_name => $val) {
            if (in_array($field_name, $requireFormFieldsList) && empty($val)) {
                $this->form_errors[$field_name][] = $this->l('This information is required.');
            }
        }
    }
}
