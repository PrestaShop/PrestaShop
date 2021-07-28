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
use Symfony\Component\Translation\TranslatorInterface;

/**
 * StarterTheme TODO: FIXME:
 * In the old days, when updating an address, we actually:
 * - checked if the address was used by an order
 * - if so, just mark it as deleted and create a new one
 * - otherwise, update it like a normal entity
 * I *think* this is not necessary now because the invoicing thing
 * does its own historization. But this should be checked more thoroughly.
 */
class CustomerAddressFormCore extends AbstractForm
{
    private $language;

    protected $template = 'customer/_partials/address-form.tpl';

    private $address;

    private $persister;

    public function __construct(
        Smarty $smarty,
        Language $language,
        TranslatorInterface $translator,
        CustomerAddressPersister $persister,
        CustomerAddressFormatter $formatter
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->language = $language;
        $this->persister = $persister;
    }

    public function loadAddressById($id_address)
    {
        $context = Context::getContext();

        $this->address = new Address($id_address, $this->language->id);

        if ($this->address->id === null) {
            return Tools::redirect('pagenotfound');
        }

        if (!$context->customer->isLogged() && !$context->customer->isGuest()) {
            return Tools::redirect('/index.php?controller=authentication');
        }

        if ($this->address->id_customer != $context->customer->id) {
            return Tools::redirect('pagenotfound');
        }

        $params = get_object_vars($this->address);
        $params['id_address'] = $this->address->id;

        return $this->fillWith($params);
    }

    public function fillWith(array $params = [])
    {
        // This form is very tricky: fields may change depending on which
        // country is being submitted!
        // So we first update the format if a new id_country was set.
        if (isset($params['id_country'])
            && $params['id_country'] != $this->formatter->getCountry()->id
        ) {
            $this->formatter->setCountry(new Country(
                $params['id_country'],
                $this->language->id
            ));
        }

        return parent::fillWith($params);
    }

    public function validate()
    {
        $is_valid = $this->validateFieldsValues();

        if (($hookReturn = Hook::exec('actionValidateCustomerAddressForm', ['form' => $this])) !== '') {
            $is_valid &= (bool) $hookReturn;
        }

        return $is_valid && parent::validate();
    }

    public function submit()
    {
        if (!$this->validate()) {
            return false;
        }

        $address = new Address(
            Tools::getValue('id_address'),
            $this->language->id
        );

        foreach ($this->formFields as $formField) {
            $address->{$formField->getName()} = $formField->getValue();
        }

        if (!isset($this->formFields['id_state'])) {
            $address->id_state = 0;
        }

        if (empty($address->alias)) {
            $address->alias = $this->translator->trans('My Address', [], 'Shop.Theme.Checkout');
        }

        Hook::exec('actionSubmitCustomerAddressForm', ['address' => &$address]);

        $this->setAddress($address);

        try {
            return $this->getPersister()->save(
                $address,
                $this->getValue('token')
            );
        } catch (PrestaShopException $e) {
            $this->errors[''][] = $this->translator->trans('Could not update your information, please check your data.', [], 'Shop.Notifications.Error');
        }

        return false;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return CustomerAddressPersister
     */
    protected function getPersister()
    {
        return $this->persister;
    }

    protected function setAddress(Address $address)
    {
        $this->address = $address;
    }

    public function getTemplateVariables()
    {
        $context = Context::getContext();

        if (!$this->formFields) {
            // This is usually done by fillWith but the form may be
            // rendered before fillWith is called.
            // I don't want to assign formFields in the constructor
            // because it accesses the DB and a constructor should not
            // have side effects.
            $this->formFields = $this->formatter->getFormat();
        }

        $this->setValue('token', $this->persister->getToken());
        $formFields = array_map(
            function (FormField $item) {
                return $item->toArray();
            },
            $this->formFields
        );

        if (empty($formFields['firstname']['value'])) {
            $formFields['firstname']['value'] = $context->customer->firstname;
        }

        if (empty($formFields['lastname']['value'])) {
            $formFields['lastname']['value'] = $context->customer->lastname;
        }

        return [
            'id_address' => (isset($this->address->id)) ? $this->address->id : 0,
            'action' => $this->action,
            'errors' => $this->getErrors(),
            'formFields' => $formFields,
        ];
    }

    /**
     * Performs validation on field values.
     * Returns true if all field values are correct, false otherwise.
     *
     * @return bool
     */
    private function validateFieldsValues(): bool
    {
        $isValid = true;

        $isValid &= $this->validatePostcode();
        $isValid &= $this->validateField('firstname', 'isName', $this->translator->trans(
            'Invalid name',
            [],
            'Shop.Forms.Errors'
        ));
        $isValid &= $this->validateField('lastname', 'isName', $this->translator->trans(
            'Invalid name',
            [],
            'Shop.Forms.Errors'
        ));
        $isValid &= $this->validateField('city', 'isCityName', $this->translator->trans(
            'Invalid format.',
            [],
            'Shop.Forms.Errors'
        ));

        return (bool) $isValid;
    }

    /**
     * @return bool
     */
    private function validatePostcode(): bool
    {
        $postcode = $this->getField('postcode');
        if ($postcode && $postcode->isRequired()) {
            $country = $this->formatter->getCountry();
            if (!$country->checkZipCode($postcode->getValue())) {
                $postcode->addError($this->translator->trans(
                    'Invalid postcode - should look like "%zipcode%"',
                    ['%zipcode%' => $country->zip_code_format],
                    'Shop.Forms.Errors'
                ));

                return false;
            }
        }

        return true;
    }

    /**
     * @param string $fieldName
     * @param string $validationFunction
     * @param string $validationFailMessage
     *
     * @return bool
     */
    private function validateField(string $fieldName, string $validationFunction, string $validationFailMessage): bool
    {
        $field = $this->getField($fieldName);
        if (null === $field) {
            return true;
        }
        $value = $field->getValue();
        if ($field->isRequired() && empty($value)) {
            return false;
        }
        if (!empty($value) && false === (bool) Validate::$validationFunction($value)) {
            $field->addError($validationFailMessage);

            return false;
        }

        return true;
    }
}
