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

    protected $template = 'customer/_partials/address-form.tpl';

    private $address;

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
        $address = new Address($id_address, $this->language->id);

        $params = get_object_vars($address);
        $params['id_address'] = $address->id;

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
        if (!parent::validate()) {
            return false;
        }

        if (($postcode = $this->getField('postcode'))) {
            if ($postcode->isRequired()) {
                $country    = $this->formatter->getCountry();
                if (!$country->checkZipCode($postcode->getValue())) {
                    // FIXME: the translator adapter is crap at the moment,
                    // but once it is not, the sprintf needs to go away.
                    $postcode->addError(sprintf(
                        $this->translator->trans(
                            'invalid postcode - should look like "%1$s"', [], 'Address'
                        ),
                        $country->zip_code_format
                    ));
                    return false;
                }
            }
        }

        return true;
    }

    public function submit()
    {
        if (!$this->validate()) {
            return false;
        }

        $address = new Address(
            $this->getValue('id_address'),
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
            $this->getValue('token')
        );
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getTemplateVariables()
    {
        if (!$this->formFields) {
            // This is usually done by fillWith but the form may be
            // rendered before fillWith is called.
            // I don't want to assign formFields in the constructor
            // because it accesses the DB and a constructor should not
            // have side effects.
            $this->formFields = $this->formatter->getFormat();
        }

        $this->setValue('token', $this->persister->getToken());

        return [
            'action'    => $this->action,
            'errors'    => $this->getErrors(),
            'formFields' => array_map(
                function (FormField $item) {
                    return $item->toArray();
                },
                $this->formFields
            )
        ];
    }
}
