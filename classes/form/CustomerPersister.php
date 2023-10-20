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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Core\Crypto\Hashing as Crypto;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        $this->crypto = $crypto;
        $this->translator = $translator;
        $this->guest_allowed = $guest_allowed;
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
    public function save(Customer $customer, $plainTextPassword, $newPlainTextPassword = '', $passwordRequired = true)
    {
        // If customer already exists in context, we will keep the ID and only update him
        if ($customer->id) {
            return $this->update($customer, $plainTextPassword, $newPlainTextPassword, $passwordRequired);
        }

        return $this->create($customer, $plainTextPassword);
    }

    private function update(Customer $customer, $plainTextPassword, $newPlainTextPassword, $passwordRequired = true)
    {
        if (!$customer->is_guest && $passwordRequired && !$this->crypto->checkHash(
            $plainTextPassword,
            $customer->passwd,
            _COOKIE_KEY_
        )) {
            $msg = $this->translator->trans(
                'Invalid email/password combination',
                [],
                'Shop.Notifications.Error'
            );
            $this->errors['email'][] = $msg;
            $this->errors['password'][] = $msg;

            return false;
        }

        if (!$customer->is_guest) {
            $customer->passwd = $this->crypto->hash(
                $newPlainTextPassword ? $newPlainTextPassword : $plainTextPassword,
                _COOKIE_KEY_
            );
        }

        if ($customer->is_guest || !$passwordRequired) {
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
        }

        $guestToCustomerConversion = false;

        /*
         * If context customer is a guest and a new password was provided in the form,
         * we start the customer conversion.
         *
         * This consists of setting is_guest property to false, setting proper password
         * assigning him to proper group and changing his default group.
         */
        if ($plainTextPassword && $customer->is_guest) {
            $guestToCustomerConversion = true;
            $customer->is_guest = false;
            $customer->passwd = $this->crypto->hash(
                $plainTextPassword,
                _COOKIE_KEY_
            );
            $customer->id_default_group = (int) Configuration::get('PS_CUSTOMER_GROUP');
        }

        if ($customer->is_guest || $guestToCustomerConversion) {
            // guest cannot update their email to that of an existing real customer
            if (Customer::customerExists($customer->email, false, true)) {
                $this->errors['email'][] = $this->translator->trans(
                    'An account was already registered with this email address',
                    [],
                    'Shop.Notifications.Error'
                );

                return false;
            }
        }

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

            // If converting from guest to customer, we need to assign proper group
            // and inform him if needed. This is intentionally done after saving the customer,
            // so we don't mess up his groups if the saving failed.
            if ($guestToCustomerConversion) {
                $customer->cleanGroups();
                $customer->addGroups([Configuration::get('PS_CUSTOMER_GROUP')]);
                $this->sendConfirmationMail($customer);
            }
        }

        return $ok;
    }

    private function create(Customer $customer, $plainTextPassword)
    {
        /*
         * If there is no password provided, we are registering a guest
         */
        if (!$plainTextPassword) {
            // If ordering without registration is not enabled, we need to force it
            if (!$this->guest_allowed) {
                $this->errors['password'][] = $this->translator->trans(
                    'Password is required',
                    [],
                    'Shop.Notifications.Error'
                );

                return false;
            }

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

        /*
         * Check that there is not a customer registered with this email,
         * we can't have two registered customers with the same email.
         *
         * Currently, it also checks for guests, because we don't allow guest checkout
         * if there is a registered customer already, will be changed.
         */
        if (Customer::customerExists($customer->email, false, true)) {
            $this->errors['email'][] = $this->translator->trans(
                'An account was already registered with this email address',
                [],
                'Shop.Notifications.Error'
            );

            return false;
        }

        /*
         * Create a password hash and assign it to the customer
         */
        $customer->passwd = $this->crypto->hash(
            $plainTextPassword,
            _COOKIE_KEY_
        );

        $ok = $customer->save();

        // If the customer himself was saved properly, we need to update the global context and the cookie
        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            // Send a welcome information email, only for registered customers
            $this->sendConfirmationMail($customer);
            Hook::exec('actionCustomerAccountAdd', [
                'newCustomer' => $customer,
            ]);
        }

        return $ok;
    }

    /**
     * Send a welcome email after converting the customer, if configured.
     *
     * @param Customer $customer
     *
     * @return bool Indicates if mail was sent OK
     */
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
}
