<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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

    public function save(Customer $customer, $clearTextPassword, $newPassword = '')
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
                'Shop.Notifications.Error'
            );
            $this->errors['email'][]    = $msg;
            $this->errors['password'][] = $msg;
            return false;
        }

        if (!$customer->is_guest) {
            $customer->passwd = $this->crypto->hash(
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
                    'Shop.Notifications.Error'
                );
                return false;
            }
        }

        $guest_to_customer = false;

        if ($clearTextPassword && $customer->is_guest) {
            $guest_to_customer = true;
            $customer->is_guest = false;
            $customer->passwd = $this->crypto->hash(
                $clearTextPassword,
                _COOKIE_KEY_
            );
        }

        if ($customer->is_guest || $guest_to_customer) {
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

        $ok = $customer->save();

        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            Hook::exec('actionCustomerAccountUpdate', [
                'customer' => $customer,
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
                    'Shop.Notifications.Error'
                );
                return false;
            }

            /**
             * Warning: this is only safe provided
             * that guests cannot log in even with the generated
             * password. That's the case at least at the time of writing.
             */
            $clearTextPassword = $this->crypto->hash(
                microtime(),
                _COOKIE_KEY_
            );

            $customer->is_guest = true;
        }

        $customer->passwd = $this->crypto->hash(
            $clearTextPassword,
            _COOKIE_KEY_
        );

        if (Customer::customerExists($customer->email, false, true)) {
            $this->errors['email'][] = $this->translator->trans(
                'An account was already registered with this email address',
                [],
                'Shop.Notifications.Error'
            );
            return false;
        }

        $ok = $customer->save();

        if ($ok) {
            $this->context->updateCustomer($customer);
            $this->context->cart->update();
            $this->sendConfirmationMail($customer);
            Hook::exec('actionCustomerAccountAdd', array(
                'newCustomer' => $customer,
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
            $this->translator->trans(
                'Welcome!',
                array(),
                'Emails.Subject'
            ),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
            ),
            $customer->email,
            $customer->firstname.' '.$customer->lastname
        );
    }
}
