<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class AuthControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'authentication';
    public $auth = false;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (Tools::getValue('create_account')) {
            $this->setTemplate('customer/_partials/register-form.tpl');
        } else {
            $this->setTemplate('customer/authentication.tpl');
        }

        $genders = [];
        $collec = Gender::getGenders();
        foreach ($collec as $g) {
            $genders[] = $this->objectSerializer->toArray($g);
        }

        $back = Tools::getValue('back');
        $key = Tools::safeOutput(Tools::getValue('key'));

        if (!empty($key)) {
            $back .= (strpos($back, '?') !== false ? '&' : '?').'key='.$key;
        }

        if ($back == Tools::secureReferrer(Tools::getValue('back'))) {
            $this->context->smarty->assign('back', html_entity_decode($back));
        } else {
            $this->context->smarty->assign('back', Tools::safeOutput($back));
        }

        $this->assignDate();

        $this->context->smarty->assign([
            'hook_create_account_form' => Hook::exec('displayCustomerAccountForm'),
            'hook_create_account_top' => Hook::exec('displayCustomerAccountFormTop'),
            'genders' => $genders,
        ]);
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitCreate')) {
            $this->processSubmitCreate();
        }

        if (Tools::isSubmit('SubmitLogin')) {
            $this->processSubmitLogin();
        }
    }

    /**
     * Process login
     */
    protected function processSubmitLogin()
    {
        Hook::exec('actionAuthenticationBefore');

        $email = trim(Tools::getValue('email'));
        $passwd = trim(Tools::getValue('passwd'));
        $_POST['passwd'] = null;

        if (empty($email)) {
            $this->errors[] = $this->l('An email address required.');
        } elseif (!Validate::isEmail($email)) {
            $this->errors[] = $this->l('Invalid email address.');
        } elseif (empty($passwd)) {
            $this->errors[] = $this->l('Password is required.');
        } elseif (!Validate::isPasswd($passwd)) {
            $this->errors[] = $this->l('Invalid password.');
        } else {
            $customer = new Customer();
            $authentication = $customer->getByEmail($email, $passwd);
            if (isset($authentication->active) && !$authentication->active) {
                $this->errors[] = $this->l('Your account isn\'t available at this time, please contact us');
            } elseif (!$authentication || !$customer->id) {
                $this->errors[] = $this->l('Authentication failed.');
            } else {
                $this->updateContext($customer);

                if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
                    $this->context->cart = new Cart($id_cart);
                } else {
                    $id_carrier = (int)$this->context->cart->id_carrier;
                    $this->context->cart->id_carrier = 0;
                    $this->context->cart->setDeliveryOption(null);
                    $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                    $this->context->cart->id_address_invoice = (int)Address::getFirstCustomerAddressId((int)($customer->id));
                }
                $this->context->cart->id_customer = (int)$customer->id;

                if ($this->ajax && isset($id_carrier) && $id_carrier) {
                    $delivery_option = [$this->context->cart->id_address_delivery => $id_carrier.','];
                    $this->context->cart->setDeliveryOption($delivery_option);
                }

                $this->context->cart->save();
                $this->context->cookie->id_cart = (int)$this->context->cart->id;
                $this->context->cookie->write();
                $this->context->cart->autosetProductAddress();

                Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);

                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);
            }
        }

        if (!$this->ajax) {
            $back = Tools::getValue('back', 'my-account');

            if ($back == Tools::secureReferrer($back)) {
                $this->redirectWithNotifications(html_entity_decode($back));
            }

            $this->redirectWithNotifications('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : $back));
        }
    }

    /**
    * Process submit on a creation
    */
    protected function processSubmitCreate()
    {
        $create_guest = false;

        if (Tools::getValue('create_from') == 'order' && (bool)Configuration::get('PS_GUEST_CHECKOUT_ENABLED') && !Tools::getValue('passwd')) {
            $create_guest = true;
            $_POST['passwd'] = md5(time()._COOKIE_KEY_);
        }

        Hook::exec('actionSubmitAccountBefore');

        $email = trim(Tools::getValue('email'));

        if (!Validate::isEmail($email) || empty($email)) {
            $this->errors[] = $this->l('Invalid email address.');
        } elseif (Customer::customerExists($email)) {
            $this->errors[] = $this->l('An account using this email address has already been registered.');
        }

        if (!count($this->errors)) {
            // Preparing customer
            $customer = new Customer();
            $customer->getByEmail($email, null, false);

            $customer->is_guest = $create_guest;

            $this->errors = array_unique(array_merge($this->errors, $customer->validateController(), $customer->validateFieldsRequiredDatabase()));

            if (!count($this->errors)) {
                $this->processCustomerNewsletter($customer);

                if ($create_guest && (int)Tools::getValue('years') != 0 && (int)Tools::getValue('months') != 0 && (int)Tools::getValue('days') != 0) {
                    $customer->birthday = (int)Tools::getValue('years').'-'.(int)Tools::getValue('months').'-'.(int)Tools::getValue('days');
                }

                if (!Validate::isBirthDate($customer->birthday)) {
                    $this->errors[] = $this->l('Invalid date of birth.');
                }

                if (!count($this->errors)) {
                    if ($customer->save()) {
                        if (!$this->sendConfirmationMail($customer)) {
                            $this->errors[] = $this->l('The email cannot be sent.');
                        }

                        $this->updateContext($customer);
                        $this->context->cart->update();

                        Hook::exec('actionCustomerAccountAdd', [
                            '_POST' => $_POST,
                            'newCustomer' => $customer
                        ]);
                    } else {
                        $this->errors[] = $this->l('An error occurred while creating your account.');
                    }
                }
            }
        }

        if (($back = Tools::getValue('back')) && $back == Tools::secureReferrer($back)) {
            $this->redirectWithNotifications(html_entity_decode($back));
        } else {
            $this->redirectWithNotifications('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
        }
    }

    /**
     * Process the newsletter settings and set the customer infos.
     *
     * @param Customer $customer Reference on the customer Object.
     *
     * @note At this point, the email has been validated.
     */
    protected function processCustomerNewsletter(&$customer)
    {
        $blocknewsletter = Module::isInstalled('blocknewsletter') && $module_newsletter = Module::getInstanceByName('blocknewsletter');
        if ($blocknewsletter && $module_newsletter->active && !Tools::getValue('newsletter')) {
            if (is_callable([$module_newsletter, 'isNewsletterRegistered']) && $module_newsletter->isNewsletterRegistered(Tools::getValue('email')) == $module_newsletter::GUEST_REGISTERED) {
                /* Force newsletter registration as customer as already registred as guest */
                $_POST['newsletter'] = true;
            }
        }

        if (Tools::getValue('newsletter')) {
            $customer->newsletter = true;
            $customer->ip_registration_newsletter = pSQL(Tools::getRemoteAddr());
            $customer->newsletter_date_add = pSQL(date('Y-m-d H:i:s'));
            /** @var Blocknewsletter $module_newsletter */
            if ($blocknewsletter && $module_newsletter->active) {
                $module_newsletter->confirmSubscription(Tools::getValue('email'));
            }
        }
    }

    /**
     * Assign date var to smarty
     */
    protected function assignDate()
    {
        $selectedYears = (int)(Tools::getValue('years', 0));
        $years = Tools::dateYears();
        $selectedMonths = (int)(Tools::getValue('months', 0));
        $months = Tools::dateMonths();
        $selectedDays = (int)(Tools::getValue('days', 0));
        $days = Tools::dateDays();

        $this->context->smarty->assign([
            'birthday_dates' => [
                'years' => $years,
                'sl_year' => $selectedYears,
                'months' => $months,
                'sl_month' => $selectedMonths,
                'days' => $days,
                'sl_day' => $selectedDays
            ]]);
    }

    /**
     * Update context after customer creation
     * @param Customer $customer Created customer
     */
    protected function updateContext(Customer $customer)
    {
        $this->context->customer = $customer;
        $this->context->cookie->id_customer = (int)$customer->id;
        $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        $customer->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest =  $customer->isGuest();
        $this->context->cart->secure_key = $customer->secure_key;
    }

    /**
     * sendConfirmationMail
     * @param Customer $customer
     * @return bool
     */
    protected function sendConfirmationMail(Customer $customer)
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
