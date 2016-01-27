<?php

use Symfony\Component\Translation\TranslatorInterface;

/**
 * StarterTheme TODO: B2B fields, Genders, CSRF
 */

class CustomerFormCore extends AbstractForm
{
    protected $template = 'customer/_partials/customer-form.tpl';

    private $context;
    private $urls;

    private $customerPersister;
    private $guest_allowed;

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        CustomerFormatter $formatter,
        CustomerPersister $customerPersister,
        array $urls
    ) {
        parent::__construct(
            $smarty,
            $translator,
            $formatter
        );

        $this->context = $context;
        $this->urls = $urls;
        $this->customerPersister = $customerPersister;
    }

    public function setGuestAllowed($guest_allowed = true)
    {
        if ($guest_allowed) {
            $this->formatter->setPasswordRequired(false);
        }
        $this->guest_allowed = $guest_allowed;
        return $this;
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

    public function getCustomer()
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
