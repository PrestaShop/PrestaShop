<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerAddressFormCore extends AbstractForm
{
    private $context;
    private $translator;
    private $addressFormatter;
    private $constraintTranslator;

    protected $template = 'customer/_partials/address-form.tpl';


    private $submitted;
    private $back;

    private $formItems = [];

    protected $errors = [

    ];

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        CustomerAddressFormatter $addressFormatter
    ) {
        parent::__construct($smarty);

        $this->context = $context;
        $this->translator = $translator;
        $this->addressFormatter = $addressFormatter;
        $this->constraintTranslator = new ValidateConstraintTranslator(
            $this->translator
        );
    }

    public function fillWith(array $params = [])
    {
        // This form is very tricky: fields may change depending on which
        // country is submitted!
        // So we first update the format if a new id_country was set.
        if (isset($params['id_country']) && $params['id_country'] != $this->addressFormatter->getCountry()->id) {
            $this->addressFormatter->setCountry(new Country(
                $params['id_country'],
                $this->context->language->id
            ));
        }

        $this->formItems = $this->addressFormatter->getFormat();

        foreach ($this->formItems as $formItem) {
            if (array_key_exists($formItem['name'], $params)) {
                $this->formItems[$formItem['name']]['value'] = $params[$formItem['name']];
            }
        }

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
            $this->context->language->id
        );

        if ($address->id_customer && $address->id_customer !== $this->context->customer->id) {
            // cannot update somebody else's address
            return false;
        }

        foreach ($this->formItems as $formItem) {
            $address->{$formItem['name']} = $formItem['value'];
        }

        if (empty($address->alias)) {
            $address->alias = $this->translator->trans('My Address', [], 'Address');
        }

        if (empty($address->id_customer)) {
            $address->id_customer = $this->context->customer->id;
        }

        return $address->save();
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
        return [
            'action'    => $this->action,
            'back'      => $this->back,
            'formItems' => $this->formItems
        ];
    }
}
