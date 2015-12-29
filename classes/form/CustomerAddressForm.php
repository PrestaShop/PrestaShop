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

    private $submitted;
    private $back;

    private $formItems;
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
        $formItems = $this->addressFormatter->getFormat();
        foreach ($formItems as $key => $formItem) {
            if ($formItem['name'] === 'id_address') {
                $formItems[$key]['value'] = $address->id;
                continue;
            }
            if (!property_exists($address, $formItem['name'])) {
                continue;
            }
            $formItems[$key]['value'] = $address->{$formItem['name']};
        }

        $this->address = $address;
        $this->setNewFormItems($formItems);

        return $this;
    }

    private function setNewFormItems(array $formItems)
    {
        if (is_array($this->formItems)) {
            foreach ($formItems as $name => $formItem) {
                if ($formItem['value'] === null && isset($this->formItems[$name])) {
                    $formItems[$name]['value'] = $this->formItems[$name]['value'];
                }
            }
        }

        $this->formItems = $formItems;
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

        $formItems = $this->addressFormatter->getFormat();

        foreach ($formItems as $formItem) {
            if (array_key_exists($formItem['name'], $params)) {
                $formItems[$formItem['name']]['value'] = $params[$formItem['name']];
            }
        }

        $this->setNewFormItems($formItems);

        return $this;
    }

    public function handleRequest(array $params = [])
    {
        $this->fillWith($params);

        if (isset($params['submitAddress'])) {
            return $this->submit();
        } else {
            return true;
        }
    }

    public function wasSubmitted()
    {
        return $this->submitted;
    }

    public function submit()
    {
        $this->submitted = true;

        foreach ($this->formItems as $key => $formItem) {
            $this->formItems[$key]['errors'] = $this->validateFormItem($formItem);
        }

        if ($this->hasErrors()) {
            return false;
        }

        $address = new Address(
            $this->formItems['id_address']['value'],
            $this->language->id
        );

        foreach ($this->formItems as $formItem) {
            $address->{$formItem['name']} = $formItem['value'];
        }

        if (empty($address->alias)) {
            $address->alias = $this->translator->trans('My Address', [], 'Address');
        }

        $this->address = $address;

        return $this->persister->save(
            $this->address,
            $this->formItems['token']['value']
        );
    }

    public function getAddress()
    {
        return $this->address;
    }

    private function validateFormItem(array $formItem)
    {
        $field  = $formItem['name'];
        $value  = $formItem['value'];
        $errors = [];

        if ($formItem['type'] === 'hidden') {
            // There is no point validating a hidden field,
            // what could the user do about it?
            return [];
        }

        if ($formItem['required'] && empty($value)) {
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
        foreach ($this->formItems as $formItem) {
            if (!empty($formItem['errors'])) {
                $errors[$formItem['name']] = $formItem['errors'];
            } else {
                $errors[$formItem['name']] = [];
            }
        }
        return $errors;
    }

    public function getTemplateVariables()
    {
        if (!$this->formItems) {
            // This is usually done by fillWith but the form may be
            // rendered before fillWith is called.
            // I don't want to assign formItems in the constructor
            // because it accesses the DB and a constructor should not
            // have side effects.
            $this->formItems = $this->addressFormatter->getFormat();
        }

        $this->formItems['token']['value'] = $this->persister->getToken();

        return [
            'action'    => $this->action,
            'back'      => $this->back,
            'formItems' => $this->formItems
        ];
    }
}
