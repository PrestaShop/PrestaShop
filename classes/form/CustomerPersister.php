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
use PrestaShop\PrestaShop\Core\Crypto\Hashing as Crypto;
use Symfony\Component\Translation\TranslatorInterface;

class CustomerPersisterCore
{
    private $errors = [];
    private $context;
    private $crypto;
    private $translator;
    private $guest_allowed;
    private $customer_email_exists;
    private $ignore_passwords_if_customer_exists;

    public function __construct(
        Context $context,
        Crypto $crypto,
        TranslatorInterface $translator,
        $guest_allowed,
        $ignore_passwords_if_customer_exists = false
    ) {
        $this->context = $context;
        $this->crypto = $crypto;
        $this->translator = $translator;
        $this->guest_allowed = $guest_allowed;
        $this->ignore_passwords_if_customer_exists = $ignore_passwords_if_customer_exists;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * READ ME - This class deals with many different use cases, make sure to check them when modifying anything!
     * - Creating a customer with no pasword from checkout (guest checkout enabled/disabled)
     * - Creating a customer with password from checkout
     * - Creating a customer from register form
     * - Converting guest to customer either by filling password in checkout or using the register form
     * - Editing customer details in my-account section
     */
    public function save(Customer $customer, $plainTextPassword, $newPassword = '', $passwordRequired = true)
    {
        // Check if the customer with same email is registed, we will need it to adjust checks
        $this->customer_email_exists = Customer::customerExists($customer->email, false, true);

        // If customer already exists in context, we will keep the ID and only update him
        if ($customer->id) {
            // If updating customer without a password needed, we need to check if its not a hack attempt
            if (!$passwordRequired && !$this->checkCustomerMatchesContext($customer)) {
                return false;
            }

            if ($customer->is_guest) {
                return $this->updateGuest($customer, $plainTextPassword);
            } else {
                return $this->updateRegisteredCustomer($customer, $plainTextPassword, $newPassword, $passwordRequired);
            }
        }

        return $this->create($customer, $plainTextPassword);
    }

    private function updateGuest(Customer $customer, $plainTextPassword)
    {
        // We ignore any entered password, if there is a registered customer with the same email and we are in checkout
        // The password should never be submitted, the only way the password can be filled is by a hack
        if ($this->ignore_passwords_if_customer_exists && $this->customer_email_exists) {
            $plainTextPassword = '';
        }

        // Are we going to convert the guest to customer account?
        $guest_to_customer_conversion = false;
        if ($plainTextPassword) {
            $guest_to_customer_conversion = true;
            $customer->is_guest = false;
            $customer->passwd = $this->crypto->hash(
                $plainTextPassword,
                _COOKIE_KEY_
            );
            $customer->id_default_group = (int) Configuration::get('PS_CUSTOMER_GROUP');
        }

        // If we are converting a customer, we need to check if this email doesn't already exist
        // Guest cannot update their email to that of an existing registered customer
        if ($guest_to_customer_conversion && $this->customer_email_exists) {
            $this->errors['email'][] = $this->translator->trans(
                'The email is already used, please choose another one or sign in',
                [],
                'Shop.Notifications.Error'
            );

            return false;
        }

        if ($customer->email != $this->context->customer->email) {
            $customer->removeResetPasswordToken();
        }

        if ($customer->save()) {
            if ($guest_to_customer_conversion) {
                $customer->cleanGroups();
                $customer->addGroups([Configuration::get('PS_CUSTOMER_GROUP')]);
                $this->sendConfirmationMail($customer);
            }
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            Hook::exec('actionCustomerAccountUpdate', [
                'customer' => $customer,
            ]);

            return true;
        }

        return false;
    }

    private function updateRegisteredCustomer(Customer $customer, $plainTextPassword, $newPassword, $passwordRequired = true)
    {
        // If password is required, we need to check if the current password matches, before doing anything
        if ($passwordRequired && !$this->crypto->checkHash($plainTextPassword, $customer->passwd, _COOKIE_KEY_)) {
            $msg = $this->translator->trans('Invalid email/password combination', [], 'Shop.Notifications.Error');
            $this->errors['email'][] = $msg;
            $this->errors['password'][] = $msg;

            return false;
        }

        $customer->passwd = $this->crypto->hash(
            $newPassword ? $newPassword : $plainTextPassword,
            _COOKIE_KEY_
        );

        if ($customer->email != $this->context->customer->email) {
            $customer->removeResetPasswordToken();
        }

        $ok = $customer->save();

        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            Hook::exec('actionCustomerAccountUpdate', [
                'customer' => $customer,
            ]);
        }

        return $ok;
    }

    private function create(Customer $customer, $plainTextPassword)
    {
        // We ignore any entered password, if there is a registered customer with the same email and we are in checkout
        // The password should never be submitted, the only way the password can be filled is by a hack
        if ($this->ignore_passwords_if_customer_exists && $this->customer_email_exists) {
            $plainTextPassword = '';
        }

        /* If guest checkout is disabled, we need the password from the customer */
        if (!$plainTextPassword && !$this->guest_allowed) {
            $this->errors['password'][] = $this->translator->trans(
                'Password is required',
                [],
                'Shop.Notifications.Error'
            );

            return false;
        }

        /* If guest checkout is disabled, we can't allow the checkout */
        if ($this->customer_email_exists && !$this->guest_allowed) {
            $this->errors['email'][] = $this->translator->trans(
                'The email is already used, please choose another one or sign in',
                [],
                'Shop.Notifications.Error'
            );

            return false;
        }

        if (!$plainTextPassword) {
            /**
             * Warning: this is only safe provided
             * that guests cannot log in even with the generated
             * password. That's the case at least at the time of writing.
             */
            $plainTextPassword = $this->crypto->hash(
                microtime(),
                _COOKIE_KEY_
            );
            $customer->is_guest = true;
        }

        $customer->passwd = $this->crypto->hash(
            $plainTextPassword,
            _COOKIE_KEY_
        );

        $ok = $customer->save();

        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            $this->sendConfirmationMail($customer);
            Hook::exec('actionCustomerAccountAdd', [
                'newCustomer' => $customer,
            ]);
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
            $this->translator->trans(
                'Welcome!',
                [],
                'Emails.Subject'
            ),
            [
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
            ],
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname
        );
    }

    private function checkCustomerMatchesContext(Customer $customer)
    {
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
                'Shop.Notifications.Error'
            );

            return false;
        }

        return true;
    }
}
