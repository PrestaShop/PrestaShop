<?php

use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Core\Foundation\Crypto\Hashing as Crypto;

/**
 * StarterTheme TODO: B2B fields, Genders, CSRF
 */

class CustomerFormCore extends AbstractForm
{
    private $crypto;

    protected $template = 'customer/_partials/customer-form.tpl';

    private $context;
    private $translator;
    private $constraintTranslator;
    private $urls;

    private $customerFormatter;

    private $formFields = [];

    private $guest_allowed;

    protected $errors = ['' => []];

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        Crypto $crypto,
        CustomerFormatter $customerFormatter,
        array $urls
    ) {
        parent::__construct($smarty);

        $this->context = $context;
        $this->translator = $translator;
        $this->crypto = $crypto;
        $this->customerFormatter = $customerFormatter;
        $this->urls = $urls;
        $this->constraintTranslator = new ValidateConstraintTranslator(
            $this->translator
        );
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

        return $this->fillWith([
            'firstname'     => $customer->firstname,
            'lastname'      => $customer->lastname,
            'email'         => $customer->email,
            'birthday'      => $birthday,
            'newsletter'    => $customer->newsletter,
            'optin'         => $customer->optin
        ]);
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

    public function submit()
    {
        foreach ($this->formFields as $field) {
            if (isset(Customer::$definition['fields'][$field->getName()]['validate'])) {
                $constraint = Customer::$definition['fields'][$field->getName()]['validate'];
                if (!Validate::$constraint($field->getValue())) {
                    $field->addError(
                        $this->constraintTranslator->translate($constraint)
                    );
                }
            }
        }

        $create_guest = false;
        if ($this->guest_allowed && !$this->formFields['password']->getValue()) {
            $create_guest = true;
        }

        if (!$this->hasErrors()) {
            $emailField     = $this->formFields['email'];
            $passwordField  = $this->formFields['password'];

            // Preparing customer
            $customer = new Customer();
            $passwordOK = $customer->getByEmail(
                $emailField->getValue(),
                $passwordField->getValue() ? $passwordField->getValue() : null,
                false
            );

            if (!$passwordOK) {
                if (Customer::customerExists($emailField->getValue())) {
                    $emailField->addError(
                        $this->translator->trans(
                            'An account using this email address has already been registered.',
                            [],
                            'Customer'
                        )
                    );
                    return false;
                }
            }

            /**
             * FIXME TODO SECURITY
             * We need to make sure that when updating an account without
             * using a password (guest) we are not updating somebody
             * else's account.
             */
            if ($customer->id && $customer->id != $this->context->cookie->id_customer) {
                $emailField->addError(
                    $this->translator->trans(
                        'An account using this email address has already been registered.',
                        [],
                        'Customer'
                    )
                );
                return false;
            }

            $customer->is_guest = $create_guest;

            if ($create_guest) {
                /**
                 * FIXME TODO SECURITY
                 * This is NOT cryptographically safe.
                 * It should not cause a security breach if a
                 * customer that is a guest is not allowed to login.
                 * This needs to be checked though.
                 */
                 $clearPasswd = md5(microtime()._COOKIE_KEY_);
            } else {
                $clearPasswd = $this->formFields['password']->getValue();
            }

            $customer->passwd = $this->crypto->encrypt($clearPasswd, _COOKIE_KEY_);

            foreach ($this->formFields as $field) {
                if (property_exists($customer, $field->getName())) {
                    $customer->{$field->getName()} = $field->getValue();
                }
            }

            if ($customer->optin || $customer->newsletter) {
                $customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
                $customer->newsletter_date_add = pSQL(date('Y-m-d H:i:s'));
            }

            if (!$this->hasErrors()) {
                $is_update = $customer->id > 0;

                if ($customer->save()) {
                    $this->context->updateCustomer($customer);
                    $this->context->cart->update();

                    if ($is_update) {
                        Hook::exec('actionCustomerAccountAdd', [
                            'newCustomer' => $customer
                        ]);
                    } else {
                        $this->sendConfirmationMail($customer);
                        Hook::exec('actionCustomerAccountUpdate', array(
                            'customer' => $customer
                        ));
                    }
                    return true;
                } else {
                    $this->errors[''] = $this->translator->trans(
                        'An error occurred while creating your account.', [], 'Customer'
                    );
                }
            }
        }

        return false;
    }

    private function sendConfirmationMail(Customer $customer)
    {
        if ($customer->is_guest || !Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            return true;
        }

        return Mail::Send(
            $this->context->language->id,
            'account',
            Mail::l('Welcome!'),
            [
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
            ],
            $customer->email,
            $customer->firstname.' '.$customer->lastname
        );
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
