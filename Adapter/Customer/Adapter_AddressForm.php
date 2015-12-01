<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Adapter_AddressForm
{
    private $address_formatter;
    private $fields_value = [];
    private $language;
    private $translator;

    private $ordered_address_fields = [];
    private $required_fields = [];

    public function __construct(Adapter_AddressFormatter $address_formatter, array $fields_value, Language $language, Adapter_Translator $translator)
    {
        $this->address_formatter = $address_formatter;
        $this->language = $language;
        $this->translator = $translator;
        $this->required_fields = $this->address_formatter->getRequired();
        $this->fields_value = $fields_value;
    }

    public function getAddressFormat()
    {
        $this->setOrderedFields()->addLabelTranslation()->addRequired()->addEmptyErrors();
        return $this->ordered_address_fields;
    }

    public function getAddressFormatWithErrors()
    {
        $this->setOrderedFields()->addLabelTranslation()->addRequired()->addEmptyErrors()->checkErrors();
        return $this->ordered_address_fields;
    }

    public function hasErrors()
    {
        $this->setOrderedFields()->checkErrors();
        foreach ($this->ordered_address_fields as $item) {
            if (!empty($item['errors'])) {
                return true;
            }
        }
        return false;
    }

    public function getCountryList()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->language->id, true);
        }

        return $countries;
    }

    protected function setOrderedFields()
    {
        $ordered = $this->address_formatter->getFormat();

        $ordered_address_fields = [];
        foreach ($ordered as $f) {
            $ordered_address_fields[$f] = [];
        }

        $this->ordered_address_fields = $ordered_address_fields;
        return $this;
    }

    protected function addRequired()
    {
        $required = $this->address_formatter->getRequired();
        foreach ($this->ordered_address_fields as $field => $null) {
            $this->ordered_address_fields[$field]['required'] = in_array($field, $required);
        }
        return $this;
    }

    protected function addLabelTranslation()
    {
        foreach ($this->ordered_address_fields as $field => $null) {
            switch ($field) {
                case 'firstname':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('First name', 'Address');
                    break;
                case 'lastname':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Last name', 'Address');
                    break;
                case 'address1':
                case 'address2':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Address', 'Address');
                    break;
                case 'postcode':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Zip/Postal Code', 'Address');
                    break;
                case 'city':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('City', 'Address');
                    break;
                case 'Country:name':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Country', 'Address');
                    break;
                case 'State:name':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('State', 'Address');
                    break;
                case 'phone':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Phone', 'Address');
                    break;
                case 'phone_mobile':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Mobile phone', 'Address');
                    break;
                case 'company':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Company', 'Address');
                    break;
                case 'vat_number':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('VAT number', 'Address');
                    break;
                case 'dni':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Identification number', 'Address');
                    break;
                default:
                    // StarterTheme: All EVERY address fields available in backoffice
                    $this->ordered_address_fields[$field]['label'] = '';
                    break;
            }
        }

        return $this;
    }

    protected function checkErrors()
    {
        $form_errors = [];

        // Validate required fields
        foreach ($this->fields_value as $field_name => $val) {
            if (in_array($field_name, $this->required_fields) && empty($val)) {
                $form_errors[$field_name][] = $this->translator->l('This information is required.', 'Address');
            }
        }

        // Check phone
        if (isset($this->fields_value['phone']) && $this->fields_value['phone'] && Validate::isPhoneNumber($this->fields_value['phone'])) {
            $form_errors['phone'][] = $this->translator->l('The phone number is invalid.', 'Address');
        }
        if (isset($this->fields_value['phone_mobile']) && $this->fields_value['phone_mobile'] && Validate::isPhoneNumber($this->fields_value['phone_mobile'])) {
            $form_errors['phone_mobile'][] = $this->translator->l('The phone number is invalid.', 'Address');
        }

        if ($this->fields_value['id_country']) {
            // Check country
            if (!($country = new Country($this->fields_value['id_country'])) || !Validate::isLoadedObject($country)) {
                throw new PrestaShopException('Country cannot be loaded with '.$this->fields_value['id_country']);
            }

            if ((int)$country->contains_states && !(int)$this->fields_value['id_state']) {
                $form_errors['state'][] = $this->translator->l('This country requires you to chose a State.', 'Address');
            }

            if (!$country->active) {
                $form_errors['country'][] = $this->translator->l('This country is not active.', 'Address');
            }

            $postcode = $this->fields_value['postcode'];
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $form_errors['postcode'][] = sprintf($this->translator->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s', 'Address'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
            } elseif (empty($postcode) && $country->need_zip_code) {
                $form_errors['postcode'][] = $this->translator->l('A Zip/Postal code is required.', 'Address');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $form_errors['postcode'][] = $this->translator->l('The Zip/Postal code is invalid.', 'Address');
            }

            // Check country DNI
            if ($country->isNeedDni() && (!$this->fields_value['dni'] || !Validate::isDniLite($this->fields_value['dni']))) {
                $form_errors['dni'][] = $this->translator->l('The identification number is incorrect or has already been used.', 'Address');
            }
        } else {
            $form_errors['country'][] = $this->translator->l('This information is required.', 'Address');
        }

        // Add errors to address fields
        foreach ($this->ordered_address_fields as $field => $null) {
            if (isset($form_errors[$field])) {
                $this->ordered_address_fields[$field]['errors'] = $form_errors[$field];
            }
        }

        return $this;
    }

    protected function addEmptyErrors()
    {
        foreach ($this->ordered_address_fields as $field => $null) {
            $this->ordered_address_fields[$field]['errors'] = [];
        }

        return $this;
    }
}
