<?php

use Symfony\Component\Translation\TranslatorInterface;

class CustomerRegisterFormCore extends AbstractForm
{
    private $context;
    private $translator;
    private $urls;

    private $back;

    private $submitted;
    protected $errors = [
        null            => [],
        'firstname'     => [],
        'lastname'      => [],
        'email'         => [],
        'password'      => [],
        'birthdate'     => []
    ];

    private $firstname;
    private $lastname;
    private $email;
    private $password;
    private $birthdate;
    private $newsletter;
    private $partner_optin;

    private $ask_for_birthdate      = true;
    private $ask_for_newsletter     = true;
    private $ask_for_partner_optin  = true;
    private $guest_allowed          = true;

    public function __construct(
        Smarty $smarty,
        Context $context,
        TranslatorInterface $translator,
        array $urls
    ) {
        parent::__construct($smarty);

        $this->context = $context;
        $this->translator = $translator;
        $this->urls = $urls;
    }

    public function setAskForBirthdate($ask_for_birthdate)
    {
        $this->ask_for_birthdate = $ask_for_birthdate;
        return $this;
    }

    public function getAskForBirthdate()
    {
        return $this->ask_for_birthdate;
    }

    public function setAskForNewsletter($ask_for_newletter)
    {
        $this->ask_for_newletter = $ask_for_newletter;
        return $this;
    }

    public function getAskForNewsletter()
    {
        return $this->ask_for_newsletter;
    }

    public function setAskForParterOptin($ask_for_partner_optin)
    {
        $this->ask_for_partner_optin = $ask_for_partner_optin;
        return $this;
    }

    public function getAskForPartnerOptin()
    {
        return $this->ask_for_partner_optin;
    }

    public function setGuestAllowed($guest_allowed)
    {
        $this->guest_allowed = $guest_allowed;
        return $this;
    }

    public function getGuestAllowed()
    {
        return $this->guest_allowed;
    }

    public function fillWith(array $params = [])
    {
        $fields = [
            'firstname',
            'lastname',
            'email',
            'password',
            'birthdate',
            'newsletter',
            'partner_optin',
            'back'
        ];

        foreach ($fields as $field) {
            if (array_key_exists($field, $params)) {
                $this->$field = $params[$field];
            }
        }

        return $this;
    }

    public function handleRequest(array $params = [])
    {
        $this->fillWith($params);

        if (array_key_exists('submitCreate', $params)) {
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
        if (!Validate::isEmail($this->email)) {
            $this->errors['email'][] = $this->translator->trans('Invalid email address.', [], 'Customer');
        } elseif (Customer::customerExists($this->email)) {
            $this->errors['email'][] = $this->translator->trans('An account using this email address has already been registered.', [], 'Customer');
        }

        $create_guest = false;
        if ($this->guest_allowed) {
            if ($this->password && !Validate::isPasswd($this->password)) {
                $this->errors['password'][] = $this->translator->trans('Invalid password.', [], 'Customer');
            } elseif (!$this->password) {
                $create_guest = true;
            }
        } else {
            if (empty($this->password)) {
                $this->errors['password'][] = $this->translator->trans('Password is required.', [], 'Customer');
            } elseif (!Validate::isPasswd($this->password)) {
                $this->errors['password'][] = $this->translator->trans('Invalid password.', [], 'Customer');
            }
        }

        if (!$this->hasErrors()) {
            // Preparing customer
            $customer = new Customer();
            $customer->getByEmail($this->email, null, false);

            /**
             * FIXME TODO SECURITY
             * We need to make sure that when updating an account without
             * using a password (guest) we are not updating somebody
             * else's account.
             */
            if ($customer->id && $customer->id != $this->context->cookie->id_customer) {
                $this->errors['email'][] = $this->translator->trans('An account using this email address has already been registered.', [], 'Customer');
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
                 $customer->passwd = md5(microtime()._COOKIE_KEY_);
            } else {
                $customer->passwd = $this->password;
            }

            $customer->firstname = $this->firstname;
            $customer->lastname  = $this->lastname;
            $customer->email     = $this->email;

            $customer->optin      = (bool)$this->partner_optin;
            $customer->newsletter = (bool)$this->newsletter;
            if ($this->partner_optin || $this->newsletter) {
                $customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
                $customer->newsletter_date_add = pSQL(date('Y-m-d H:i:s'));
            }

            if ($this->birthdate) {
                if (!Validate::isBirthDate($this->birthdate)) {
                    $this->errors['birthdate'][] = $this->translator->trans(
                        'Invalid date of birth.', [], 'Customer'
                    );
                }
                $customer->birthday = $this->birthdate;
            }

            if (!$this->hasErrors()) {
                if ($customer->save()) {
                    $this->sendConfirmationMail($customer);
                    $this->context->updateCustomer($customer);
                    $this->context->cart->update();
                    Hook::exec('actionCustomerAccountAdd', [
                        'newCustomer' => $customer
                    ]);
                    return true;
                } else {
                    $this->errors[null] = $this->translator->trans(
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

    public function getTemplatePath()
    {
        return 'customer/_partials/register-form.tpl';
    }

    public function getTemplateVariables()
    {
        return [
            'action'        => $this->action,
            'errors'        => $this->getErrors(),
            'urls'          => $this->urls,
            'back'          => $this->back,
            'firstname'     => $this->firstname,
            'lastname'      => $this->lastname,
            'email'         => $this->email,
            'password'      => $this->password,
            'birthdate'     => $this->birthdate,
            'newsletter'    => $this->newsletter,
            'partner_optin' => $this->partner_optin,
            'ask_for_birthdate'     => $this->ask_for_birthdate,
            'ask_for_newsletter'    => $this->ask_for_newsletter,
            'ask_for_partner_optin' => $this->ask_for_partner_optin,
            'guest_allowed'         => $this->guest_allowed
        ];
    }
}
