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

use Symfony\Component\Translation\TranslatorInterface;

class CustomerAddressFormatterCore
{
    private $country;
    private $translator;
    private $availableCountries;

    public function __construct(
        Country $country,
        TranslatorInterface $translator,
        array $availableCountries
    ) {
        $this->country = $country;
        $this->translator = $translator;
        $this->availableCountries = $availableCountries;
    }

    public function setCountry(Country $country)
    {
        $this->country = $country;
        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getFormat()
    {
        $fields = AddressFormat::getOrderedAddressFields(
            $this->country->id,
            true,
            true
        );
        $required = array_flip(AddressFormat::getFieldsRequired());

        $format = [
            'id_address'  => [
                'name'      => 'id_address',
                'type'      => 'hidden',
                'required'  => false,
                'label'     => '',
                'value'     => null,
                'values'    => null,
                'errors'    => []
            ],
            'id_customer' => [
                'name'      => 'id_customer',
                'type'      => 'hidden',
                'required'  => true,
                'label'     => '',
                'value'     => null,
                'values'    => null,
                'errors'    => []
            ],
            'token' => [
                'name'      => 'token',
                'type'      => 'hidden',
                'required'  => true,
                'label'     => '',
                'value'     => null,
                'values'    => null,
                'errors'    => []
            ],
            'alias' => [
                'name'      => 'alias',
                'type'      => 'text',
                'required'  => false,
                'label'     => $this->getFieldLabel('alias'),
                'value'     => null,
                'values'    => null,
                'errors'    => []
            ]
        ];

        foreach ($fields as $field) {
            $type   = 'text';
            $value  = null;
            $values = null;
            $name   = $field;

            $fieldParts = explode(':', $field, 2);

            if (count($fieldParts) === 2) {
                list($entity, $entityField) = $fieldParts;

                // Fields specified using the Entity:field
                // notation are actually references to other
                // entities, so they should be displayed as a select
                $type = 'select';

                // Also, what we really want is the id of the linked entity
                $name = 'id_'.strtolower($entity);

                if ($entity === 'Country') {
                    $value = $this->country->id;
                    $values = [];
                    foreach ($this->availableCountries as $country) {
                        $values[$country['id_country']] = $country[$entityField];
                    }
                } elseif ($entity === 'State') {
                    $states = State::getStatesByIdCountry($this->country->id);
                    $values = [];
                    foreach ($states as $state) {
                        $values[$state['id_state']] = $state[$entityField];
                    }
                }
            }

            $format[$name] = [
                'name'     => $name,
                'type'     => $type,
                'required' => array_key_exists($field, $required),
                'label'    => $this->getFieldLabel($field),
                'value'    => $value,
                'values'   => $values,
                'errors'   => []
            ];
        }
        return $format;
    }

    private function getFieldLabel($field)
    {
        // Country:name => Country, Country:iso_code => Country,
        // same label regardless of which field is used for mapping.
        $field = explode(':', $field)[0];

        switch ($field) {
            case 'alias':
                return $this->translator->trans('Alias', [], 'Address');
            case 'firstname':
                return $this->translator->trans('First name', [], 'Address');
            case 'lastname':
                return $this->translator->trans('Last name', [], 'Address');
            case 'address1':
                return $this->translator->trans('Address', [], 'Address');
            case 'address2':
                return $this->translator->trans('Address Complement', [], 'Address');
            case 'postcode':
                return $this->translator->trans('Zip/Postal Code', [], 'Address');
            case 'city':
                return $this->translator->trans('City', [], 'Address');
            case 'Country':
                return $this->translator->trans('Country', [], 'Address');
            case 'State':
                return $this->translator->trans('State', [], 'Address');
            case 'phone':
                return $this->ordered_address_fields[$field]['label'] = $this->translator->trans('Phone', [], 'Address');
            case 'phone_mobile':
                return $this->translator->trans('Mobile phone', [], 'Address');
            case 'company':
                return $this->translator->trans('Company', [], 'Address');
            case 'vat_number':
                return $this->translator->trans('VAT number', [], 'Address');
            case 'dni':
                return $this->translator->trans('Identification number', [], 'Address');
            default:
                return $field;
        }
    }
}
