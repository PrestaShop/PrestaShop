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
    private $country;
    private $translator;
    private $ordered_address_fields = [];
    private $required_fields = [];

    public function __construct(Country $country, Adapter_Translator $translator)
    {
        $this->country = $country;
        $this->translator = $translator;
        $this->required_fields = AddressFormat::getFieldsRequired();
    }

    public function getAddressFormat()
    {
        $this->setOrderedFields()->addLabelTranslation()->addRequired();
        return $this->ordered_address_fields;
    }

    protected function setOrderedFields()
    {
        $ordered = AddressFormat::getOrderedAddressFields($this->country->id, true, true);
        $ordered = array_unique(array_merge(['alias'], $ordered, $this->required_fields));

        $ordered_address_fields = [];
        foreach ($ordered as $f) {
            $ordered_address_fields[$f] = [];
        }

        $this->ordered_address_fields = $ordered_address_fields;
        return $this;
    }

    protected function addRequired()
    {
        $required = AddressFormat::getFieldsRequired();
        foreach ($this->ordered_address_fields as $field => $null) {
            $this->ordered_address_fields[$field]['required'] = in_array($field, $required);
        }
        return $this;
    }

    protected function addLabelTranslation()
    {
        foreach ($this->ordered_address_fields as $field => $null) {
            switch ($field) {
                case 'alias':
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Address alias', 'Address');
                    break;
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
                    $this->ordered_address_fields[$field]['label'] = $this->translator->l('Home phone', 'Address');
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
}
