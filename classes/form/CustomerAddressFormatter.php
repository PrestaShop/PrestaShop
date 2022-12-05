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
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerAddressFormatterCore implements FormFormatterInterface
{
    private $country;
    private $translator;
    private $availableCountries;
    private $definition;

    public function __construct(
        Country $country,
        TranslatorInterface $translator,
        array $availableCountries
    ) {
        $this->country = $country;
        $this->translator = $translator;
        $this->availableCountries = $availableCountries;
        $this->definition = Address::$definition['fields'];
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
            'back' => (new FormField())
                ->setName('back')
                ->setType('hidden'),
            'token' => (new FormField())
                ->setName('token')
                ->setType('hidden'),
            'alias' => (new FormField())
                ->setName('alias')
                ->setLabel(
                    $this->getFieldLabel('alias')
                ),
        ];

        foreach ($fields as $field) {
            $formField = new FormField();
            $formField->setName($field);

            $fieldParts = explode(':', $field, 2);

            if (count($fieldParts) === 1) {
                if ($field === 'postcode') {
                    if ($this->country->need_zip_code) {
                        $formField->setRequired(true);
                    }
                } elseif ($field === 'phone') {
                    $formField->setType('tel');
                } elseif ($field === 'dni' && null !== $this->country) {
                    if ($this->country->need_identification_number) {
                        $formField->setRequired(true);
                    }
                }
            } elseif (count($fieldParts) === 2) {
                list($entity, $entityField) = $fieldParts;

                // Fields specified using the Entity:field
                // notation are actually references to other
                // entities, so they should be displayed as a select
                $formField->setType('select');

                // Also, what we really want is the id of the linked entity
                $formField->setName('id_' . strtolower($entity));

                if ($entity === 'Country') {
                    $formField->setType('countrySelect');
                    $formField->setValue($this->country->id);
                    foreach ($this->availableCountries as $country) {
                        $formField->addAvailableValue(
                            $country['id_country'],
                            $country[$entityField]
                        );
                    }
                } elseif ($entity === 'State') {
                    if ($this->country->contains_states) {
                        $states = State::getStatesByIdCountry($this->country->id, true, 'name', 'asc');
                        foreach ($states as $state) {
                            $formField->addAvailableValue(
                                $state['id_state'],
                                $state[$entityField]
                            );
                        }
                        $formField->setRequired(true);
                    }
                }
            }

            $formField->setLabel($this->getFieldLabel($field));
            if (!$formField->isRequired()) {
                // Only trust the $required array for fields
                // that are not marked as required.
                // $required doesn't have all the info, and fields
                // may be required for other reasons than what
                // AddressFormat::getFieldsRequired() says.
                $formField->setRequired(
                    array_key_exists($field, $required)
                );
            }

            $format[$formField->getName()] = $formField;
        }

        //To add the extra fields in address form
        $additionalAddressFormFields = Hook::exec('additionalCustomerAddressFields', ['fields' => &$format], null, true);
        if (is_array($additionalAddressFormFields)) {
            foreach ($additionalAddressFormFields as $moduleName => $additionnalFormFields) {
                if (!is_array($additionnalFormFields)) {
                    continue;
                }

                foreach ($additionnalFormFields as $formField) {
                    $formField->moduleName = $moduleName;
                    $format[$moduleName . '_' . $formField->getName()] = $formField;
                }
            }
        }

        return $this->addConstraints(
                $this->addMaxLength(
                    $format
                )
        );
    }

    private function addConstraints(array $format)
    {
        foreach ($format as $field) {
            if (!empty($this->definition[$field->getName()]['validate'])) {
                $field->addConstraint(
                    $this->definition[$field->getName()]['validate']
                );
            }
        }

        return $format;
    }

    private function addMaxLength(array $format)
    {
        foreach ($format as $field) {
            if (!empty($this->definition[$field->getName()]['size'])) {
                $field->setMaxLength(
                    $this->definition[$field->getName()]['size']
                );
            }
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
                return $this->translator->trans('Alias', [], 'Shop.Forms.Labels');
            case 'firstname':
                return $this->translator->trans('First name', [], 'Shop.Forms.Labels');
            case 'lastname':
                return $this->translator->trans('Last name', [], 'Shop.Forms.Labels');
            case 'address1':
                return $this->translator->trans('Address', [], 'Shop.Forms.Labels');
            case 'address2':
                return $this->translator->trans('Address Complement', [], 'Shop.Forms.Labels');
            case 'postcode':
                return $this->translator->trans('Zip/Postal Code', [], 'Shop.Forms.Labels');
            case 'city':
                return $this->translator->trans('City', [], 'Shop.Forms.Labels');
            case 'Country':
                return $this->translator->trans('Country', [], 'Shop.Forms.Labels');
            case 'State':
                return $this->translator->trans('State', [], 'Shop.Forms.Labels');
            case 'phone':
                return $this->translator->trans('Phone', [], 'Shop.Forms.Labels');
            case 'phone_mobile':
                return $this->translator->trans('Mobile phone', [], 'Shop.Forms.Labels');
            case 'company':
                return $this->translator->trans('Company', [], 'Shop.Forms.Labels');
            case 'vat_number':
                return $this->translator->trans('VAT number', [], 'Shop.Forms.Labels');
            case 'dni':
                return $this->translator->trans('Identification number', [], 'Shop.Forms.Labels');
            case 'other':
                return $this->translator->trans('Other', [], 'Shop.Forms.Labels');
            default:
                return $field;
        }
    }
}
