<?php

use PrestaShop\PrestaShop\Core\Foundation\Crypto\Hashing as Crypto;
use Symfony\Component\Translation\TranslatorInterface;

class CustomerPersisterCore
{
    private $errors = [];
    private $context;
    private $crypto;
    private $translator;
    private $guest_allowed;

    public function __construct(
        Context $context,
        Crypto $crypto,
        TranslatorInterface $translator,
        $guest_allowed
    ) {
        $this->context = $context;
        $this->crypto  = $crypto;
        $this->translator = $translator;
        $this->guest_allowed = $guest_allowed;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function save(Customer $customer, $clearTextPassword, $newPassword='')
    {
        if ($customer->id) {
            return $this->update($customer, $clearTextPassword, $newPassword);
        } else {
            return $this->create($customer, $clearTextPassword);
        }
    }

    private function update(Customer $customer, $clearTextPassword, $newPassword)
    {
        if (!$customer->is_guest && !$this->crypto->checkHash(
            $clearTextPassword,
            $customer->passwd,
            _COOKIE_KEY_
        )) {
            $msg = $this->translator->trans(
                'Invalid email/password combination',
                [],
                'Customer'
            );
            $this->errors['email'][]    = $msg;
            $this->errors['password'][] = $msg;
            return false;
        }

        if (!$customer->is_guest) {
            $customer->passwd = $this->crypto->encrypt(
                $newPassword ? $newPassword : $clearTextPassword,
                _COOKIE_KEY_
            );
        }

        if ($customer->is_guest) {
            // TODO SECURITY: Audit requested
            if ($customer->id != $this->context->customer->id) {

                // Since we're updating a customer without
                // checking the password, we need to check that
                // the customer being updated is the one from the
                // current session.

                // The error message is not great,
                // but it should only be displayed to hackers
                // so it should not be an issue :)

                $this->errors['email'][] = $this->translator->trans(
                    'There seems to be an issue with your account, please contact support',
                    [],
                    'Customer'
                );
                return false;
            }
        }

        $guest_to_customer = false;

        if ($clearTextPassword && $customer->is_guest) {
            $guest_to_customer = true;
            $customer->is_guest = false;
            $customer->passwd = $this->crypto->encrypt(
                $clearTextPassword,
                _COOKIE_KEY_
            );
        }

        if ($customer->is_guest || $guest_to_customer) {
            // guest cannot update their email to that of an existing real customer
            if (Customer::customerExists($customer->email, false, true)) {
                $this->errors['email'][] = $this->translator->trans(
                    'An account was already registed with this email address',
                    [],
                    'Customer'
                );
                return false;
            }
        }

        $ok = $customer->save();

        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            Hook::exec('actionCustomerAccountAdd', [
                'newCustomer' => $customer
            ]);
            if ($guest_to_customer) {
                $this->sendConfirmationMail($customer);
            }
        }

        return $ok;
    }

    private function create(Customer $customer, $clearTextPassword)
    {
        if (!$clearTextPassword) {
            if (!$this->guest_allowed) {
                $this->errors['password'][] = $this->translator->trans(
                    'Password is required',
                    [],
                    'Customer'
                );
                return false;
            }

            /**
             * Warning: this is only safe provided
             * that guests cannot log in even with the generated
             * password. That's the case at least at the time of writing.
             */
            $clearTextPassword = $this->crypto->encrypt(
                microtime(),
                _COOKIE_KEY_
            );

            $customer->is_guest = true;
        }

        $customer->passwd = $this->crypto->encrypt(
            $clearTextPassword,
            _COOKIE_KEY_
        );

        if (Customer::customerExists($customer->email, false, $customer->is_guest)) {
            $this->errors['email'][] = $this->translator->trans(
                'An account was already registed with this email address',
                [],
                'Customer'
            );
            return false;
        }

        $ok = $customer->save();

        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            $this->sendConfirmationMail($customer);
            Hook::exec('actionCustomerAccountUpdate', array(
                'customer' => $customer
            ));
        }

        return $ok;
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
}
