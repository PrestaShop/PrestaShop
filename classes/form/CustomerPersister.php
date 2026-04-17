<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        // If we are converting to a registered customer, we must check if a customer
        // with this email doesn't already exist.
        if ($guestToCustomerConversion && Customer::customerExists($customer->email)) {
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

                // Send him a welcome email, if enabled
                if (Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
                    $customer->sendWelcomeEmail($this->context->language->id);
                }
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
         * If a password was entered in the forn, check that there is not
         * a customer registered with this email, we can't have two
         * registered customers with the same email.
         */
        if (!$customer->isGuest() && Customer::customerExists($customer->email)) {
            $this->errors['email'][] = $this->translator->trans(
                'The email is already used, please choose another one or sign in',
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

            // Send a welcome information email, only for registered customers and if enabled
            if (!$customer->is_guest && Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
                $customer->sendWelcomeEmail($this->context->language->id);
            }

            Hook::exec('actionCustomerAccountAdd', [
                'newCustomer' => $customer,
            ]);
        }

        return $ok;
    }
}
