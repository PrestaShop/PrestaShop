<?php

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
    private $translator;
    private $persister;
    private $addressFormatter;
    private $constraintTranslator;

    protected $template = 'customer/_partials/address-form.tpl';

    private $back;

    private $formFields;
    private $address;

    public function __construct(
        Smarty $smarty,
        Language $language,
        TranslatorInterface $translator,
        CustomerAddressPersister $persister,
        CustomerAddressFormatter $addressFormatter
    ) {
        parent::__construct($smarty);

        $this->language = $language;
        $this->translator = $translator;
        $this->persister = $persister;
        $this->addressFormatter = $addressFormatter;
        $this->constraintTranslator = new ValidateConstraintTranslator(
            $this->translator
        );
    }

    public function loadAddressById($id_address)
    {
        $address = new Address($id_address, $this->language->id);
        $formFields = $this->addressFormatter->getFormat();
        foreach ($formFields as $key => $formField) {
            if ($formField->getName() === 'id_address') {
                $formField->setValue($address->id);
                continue;
            }
            if (!property_exists($address, $formField->getName())) {
                continue;
            }
            $formField->setValue($address->{$formField->getName()});
        }

        $this->address = $address;
        $this->setNewFormFields($formFields);

        return $this;
    }

    private function setNewFormFields(array $formFields)
    {
        if (is_array($this->formFields)) {
            foreach ($formFields as $name => $formField) {
                if ($formField->getValue() === null && isset($this->formFields[$name])) {
                    $formField->setValue($this->formFields[$name]->getValue());
                }
            }
        }

        $this->formFields = $formFields;
        return $this;
    }

    public function fillWith(array $params = [])
    {
        // This form is very tricky: fields may change depending on which
        // country is being submitted!
        // So we first update the format if a new id_country was set.
        if (isset($params['id_country']) && $params['id_country'] != $this->addressFormatter->getCountry()->id) {
            $this->addressFormatter->setCountry(new Country(
                $params['id_country'],
                $this->language->id
            ));
        }

        $formFields = $this->addressFormatter->getFormat();

        foreach ($formFields as $formField) {
            if (array_key_exists($formField->getName(), $params)) {
                $formField->setValue($params[$formField->getName()]);
            }
        }

        $this->setNewFormFields($formFields);

        return $this;
    }

    public function submit()
    {
        foreach ($this->formFields as $key => $formField) {
            $this->formFields[$key]->setErrors($this->validateFormField($formField));
        }

        if ($this->hasErrors()) {
            return false;
        }

        $address = new Address(
            $this->formFields['id_address']->getValue(),
            $this->language->id
        );

        foreach ($this->formFields as $formField) {
            $address->{$formField->getName()} = $formField->getValue();
        }

        if (empty($address->alias)) {
            $address->alias = $this->translator->trans('My Address', [], 'Address');
        }

        $this->address = $address;

        return $this->persister->save(
            $this->address,
            $this->formFields['token']->getValue()
        );
    }

    public function getAddress()
    {
        return $this->address;
    }

    private function validateFormField(FormField $formField)
    {
        $field  = $formField->getName();
        $value  = $formField->getValue();
        $errors = [];

        if ($formField->getType() === 'hidden') {
            // There is no point validating a hidden field,
            // what could the user do about it?
            return [];
        }

        if ($formField->isRequired() && empty($value)) {
            $errors[] = $this->constraintTranslator->translate('required');
        }

        $constraints = Address::$definition['fields'];

        if (isset($constraints[$field]['validate'])) {
            $validator = $constraints[$field]['validate'];
            if (!Validate::$validator($value)) {
                $errors[] = $this->constraintTranslator->translate($validator);
            }
        }

        if ($field === 'postcode') {
            $country = $this->addressFormatter->getCountry();
            if (!$country->checkZipCode($value)) {
                // FIXME: the translator adapter is crap at the moment,
                // but once it is not, the sprintf needs to go away.
                $errors[] = sprintf(
                    $this->translator->trans(
                        'invalid postcode - should look like "%1$s"', [], 'Address'
                    ),
                    $country->zip_code_format
                );
            }
        }

        return $errors;
    }

    public function getErrors()
    {
        $errors = [];
        foreach ($this->formFields as $formField) {
            if (!empty($formField->getErrors())) {
                $errors[$formField->getName()] = $formField->getErrors();
            } else {
                $errors[$formField->getName()] = [];
            }
        }
        return $errors;
    }

    public function getTemplateVariables()
    {
        if (!$this->formFields) {
            // This is usually done by fillWith but the form may be
            // rendered before fillWith is called.
            // I don't want to assign formFields in the constructor
            // because it accesses the DB and a constructor should not
            // have side effects.
            $this->formFields = $this->addressFormatter->getFormat();
        }

        $this->formFields['token']->setValue($this->persister->getToken());

        return [
            'action'    => $this->action,
            'back'      => $this->back,
            'formFields' => array_map(
                function (FormField $item) {
                    return $item->toArray();
                },
                $this->formFields
            )
        ];
    }
}
