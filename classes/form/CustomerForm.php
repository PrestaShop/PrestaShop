<?php

use Symfony\Component\Translation\TranslatorInterface;

/**
 * StarterTheme TODO: B2B fields, Genders, CSRF
 */

class CustomerFormCore extends AbstractForm
{
    protected $template = 'customer/_partials/customer-form.tpl';

    private $context;
    private $translator;
    private $constraintTranslator;
    private $urls;

    private $customerFormatter;
    private $customerPersister;

    private $formFields = [];

    private $guest_allowed;

    protected $errors = ['' => []];

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        CustomerFormatter $customerFormatter,
        CustomerPersister $customerPersister,
        array $urls
    ) {
        parent::__construct($smarty);

        $this->context = $context;
        $this->translator = $translator;
        $this->customerFormatter = $customerFormatter;
        $this->urls = $urls;
        $this->constraintTranslator = new ValidateConstraintTranslator(
            $this->translator
        );
        $this->customerPersister = $customerPersister;
    }

    public function getFormatter()
    {
        return $this->customerFormatter;
    }

    public function setGuestAllowed($guest_allowed = true)
    {
        if ($guest_allowed) {
            $this->customerFormatter->setPasswordRequired(false);
        }
        $this->guest_allowed = $guest_allowed;
        return $this;
    }

    public function getErrors()
    {
        foreach ($this->formFields as $field) {
            $this->errors[$field->getName()] = $field->getErrors();
        }
        return $this->errors;
    }

    public function fillFromCustomer(Customer $customer)
    {
        $birthday = $customer->birthday;
        if ($birthday === '0000-00-00') {
            // this is just because '0000-00-00' is not a valid
            // value for an <input type="date">
            $birthday = null;
        }

        $params = get_object_vars($customer);
        $params['id_customer'] = $customer->id;

        return $this->fillWith($params);
    }

    public function fillWith(array $params = [])
    {
        $newFields = $this->customerFormatter->getFormat();

        foreach ($newFields as $field) {
            if (isset($this->formFields[$field->getName()])) {
                // keep current value if set
                if ($this->formFields[$field->getName()]->getValue()) {
                    $field->setValue($this->formFields[$field->getName()]->getValue());
                }
            }

            if (array_key_exists($field->getName(), $params)) {
                // overwrite it if necessary
                $field->setValue($params[$field->getName()]);
            } elseif ($field->getType() === 'checkbox') {
                $field->setValue(false);
            }
        }

        $this->formFields = $newFields;

        return $this;
    }

    private function getCustomer()
    {
        if (isset($this->formFields['id_customer'])) {
            $id_customer = $this->formFields['id_customer']->getValue();
        } else {
            $id_customer = null;
        }

        $customer = new Customer($id_customer);

        foreach ($this->formFields as $field) {
            $customerField = $field->getName();
            if ($customerField === 'id_customer') {
                $customerField = 'id';
            }
            if (property_exists($customer, $customerField)) {
                $customer->$customerField = $field->getValue();
            }
        }

        return $customer;
    }

    private function validate()
    {
        foreach ($this->formFields as $field) {
            if ($field->isRequired() && !$field->getValue()) {
                $field->addError(
                    $this->constraintTranslator->translate('required')
                );
                continue;
            } elseif (!$field->isRequired() && !$field->getValue()) {
                continue;
            }

            foreach ($field->getConstraints() as $constraint) {
                if (!Validate::$constraint($field->getValue())) {
                    $field->addError(
                        $this->constraintTranslator->translate($constraint)
                    );
                }
            }
        }

        return !$this->hasErrors();
    }

    public function submit()
    {
        if ($this->validate()) {
            $clearTextPassword = '';
            $newPassword = '';
            if (isset($this->formFields['password'])) {
                $clearTextPassword = (string)$this->formFields['password']->getValue();
            }

            if (isset($this->formFields['new_password'])) {
                $newPassword = $this->formFields['new_password']->getValue();
            }

            $ok = $this->customerPersister->save(
                $this->getCustomer(),
                $clearTextPassword,
                $newPassword
            );

            if (!$ok) {
                foreach ($this->customerPersister->getErrors() as $field => $errors) {
                    $this->formFields[$field]->setErrors($errors);
                }
            }

            return $ok;
        }

        return false;
    }

    public function getTemplateVariables()
    {
        return [
            'action'        => $this->action,
            'urls'          => $this->urls,
            'errors'        => $this->getErrors(),
            'hook_create_account_form'  => Hook::exec('displayCustomerAccountForm'),
            'formFields' => array_map(
                function (FormField $field) {
                    return $field->toArray();
                },
                $this->formFields
            )
        ];
    }
}
