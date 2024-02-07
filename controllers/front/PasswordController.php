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
use PrestaShop\PrestaShop\Core\Util\InternationalizedDomainNameConverter;

class PasswordControllerCore extends FrontController
{
    /** @var string */
    public $php_self = 'password';
    /** @var bool */
    public $auth = false;
    /** @var bool */
    public $ssl = true;

    /**
     * @var InternationalizedDomainNameConverter
     */
    private $IDNConverter;

    public function __construct()
    {
        parent::__construct();
        $this->IDNConverter = new InternationalizedDomainNameConverter();
    }

    /**
     * Start forms process.
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $this->setTemplate('customer/password-email');

        if (Tools::isSubmit('email')) {
            $this->sendRenewPasswordLink();
        } elseif (Tools::getValue('token') && ($id_customer = (int) Tools::getValue('id_customer'))) {
            $this->changePassword();
        } elseif (Tools::getValue('token') || Tools::getValue('id_customer')) {
            $this->errors[] = $this->trans('We cannot regenerate your password with the data you\'ve submitted', [], 'Shop.Notifications.Error');
        }
    }

    protected function sendRenewPasswordLink()
    {
        if (!($email = $this->IDNConverter->emailToUtf8(trim(Tools::getValue('email')))) || !Validate::isEmail($email)) {
            $this->errors[] = $this->trans('Invalid email address.', [], 'Shop.Notifications.Error');
        } else {
            $customer = new Customer();
            $customer->getByEmail($email);
            if (null === $customer->email) {
                $customer->email = Tools::getValue('email');
            }

            if (!Validate::isLoadedObject($customer)) {
                $this->success[] = $this->trans(
                    'If this email address has been registered in our store, you will receive a link to reset your password at %email%.',
                    ['%email%' => $customer->email],
                    'Shop.Notifications.Success'
                );
                $this->setTemplate('customer/password-infos');
            } elseif (!$customer->active) {
                $this->errors[] = $this->trans('You cannot regenerate the password for this account.', [], 'Shop.Notifications.Error');
            } elseif ((strtotime($customer->last_passwd_gen . '+' . ($minTime = (int) Configuration::get('PS_PASSWD_TIME_FRONT')) . ' minutes') - time()) > 0) {
                $this->errors[] = $this->trans('You can regenerate your password only every %d minute(s)', [(int) $minTime], 'Shop.Notifications.Error');
            } else {
                if (!$customer->hasRecentResetPasswordToken()) {
                    $customer->stampResetPasswordToken();
                    $customer->update();
                }

                $mailParams = [
                    '{email}' => $customer->email,
                    '{lastname}' => $customer->lastname,
                    '{firstname}' => $customer->firstname,
                    '{url}' => $this->context->link->getPageLink('password', null, null, 'token=' . $customer->secure_key . '&id_customer=' . (int) $customer->id . '&reset_token=' . $customer->reset_password_token),
                ];

                if (
                    Mail::Send(
                        $this->context->language->id,
                        'password_query',
                        $this->trans(
                            'Password query confirmation',
                            [],
                            'Emails.Subject'
                        ),
                        $mailParams,
                        $customer->email,
                        $customer->firstname . ' ' . $customer->lastname
                    )
                ) {
                    $this->success[] = $this->trans('If this email address has been registered in our store, you will receive a link to reset your password at %email%.', ['%email%' => $customer->email], 'Shop.Notifications.Success');
                    $this->setTemplate('customer/password-infos');
                } else {
                    $this->errors[] = $this->trans('An error occurred while sending the email.', [], 'Shop.Notifications.Error');
                }
            }
        }
    }

    protected function changePassword()
    {
        $token = Tools::getValue('token');
        $id_customer = (int) Tools::getValue('id_customer');
        $reset_token = Tools::getValue('reset_token');
        $email = Db::getInstance()->getValue(
            'SELECT `email` FROM ' . _DB_PREFIX_ . 'customer c WHERE c.`secure_key` = \'' . pSQL($token) . '\' AND c.id_customer = ' . $id_customer
        );
        if ($email) {
            $customer = new Customer();
            $customer->getByEmail($email);

            if (!Validate::isLoadedObject($customer)) {
                $this->errors[] = $this->trans('Customer account not found', [], 'Shop.Notifications.Error');
            } elseif (!$customer->active) {
                $this->errors[] = $this->trans('You cannot regenerate the password for this account.', [], 'Shop.Notifications.Error');
            } elseif ($customer->getValidResetPasswordToken() !== $reset_token) {
                $this->errors[] = $this->trans('The password change request expired. You should ask for a new one.', [], 'Shop.Notifications.Error');
            }

            if ($this->errors) {
                return;
            }

            if ($isSubmit = Tools::isSubmit('passwd')) {
                // If password is submitted validate pass and confirmation
                if (!$passwd = Tools::getValue('passwd')) {
                    $this->errors[] = $this->trans('The password is missing: please enter your new password.', [], 'Shop.Notifications.Error');
                }

                if (!$confirmation = Tools::getValue('confirmation')) {
                    $this->errors[] = $this->trans('The confirmation is empty: please fill in the password confirmation as well', [], 'Shop.Notifications.Error');
                }

                if ($passwd && $confirmation) {
                    if ($passwd !== $confirmation) {
                        $this->errors[] = $this->trans('The confirmation password doesn\'t match.', [], 'Shop.Notifications.Error');
                    }

                    if (!Validate::isAcceptablePasswordLength($passwd)) {
                        $this->errors[] = $this->trans('The password is not in a valid format.', [], 'Shop.Notifications.Error');
                    }
                }
            }

            if (!$isSubmit || $this->errors) {
                // If password is NOT submitted OR there are errors, shows the form (and errors)
                $this->context->smarty->assign([
                    'customer_email' => $customer->email,
                    'customer_token' => $token,
                    'id_customer' => $id_customer,
                    'reset_token' => $reset_token,
                ]);

                $this->setTemplate('customer/password-new');
            } else {
                // Both password fields posted. Check if all is right and store new password properly.
                if (!$reset_token || (strtotime($customer->last_passwd_gen . '+' . (int) Configuration::get('PS_PASSWD_TIME_FRONT') . ' minutes') - time()) > 0) {
                    Tools::redirect($this->context->link->getPageLink(
                        'authentication',
                        null,
                        null,
                        ['error_regen_pwd' => 1]
                    ));
                } else {
                    $customer->passwd = $this->get('hashing')->hash($password = Tools::getValue('passwd'), _COOKIE_KEY_);
                    $customer->last_passwd_gen = date('Y-m-d H:i:s', time());

                    if ($customer->update()) {
                        Hook::exec('actionPasswordRenew', ['customer' => $customer, 'password' => $password]);
                        $customer->removeResetPasswordToken();
                        $customer->update();

                        $mail_params = [
                            '{email}' => $customer->email,
                            '{lastname}' => $customer->lastname,
                            '{firstname}' => $customer->firstname,
                        ];

                        if (
                            Mail::Send(
                                $this->context->language->id,
                                'password',
                                $this->trans(
                                    'Your new password',
                                    [],
                                    'Emails.Subject'
                                ),
                                $mail_params,
                                $customer->email,
                                $customer->firstname . ' ' . $customer->lastname
                            )
                        ) {
                            $this->context->smarty->assign([
                                'customer_email' => $customer->email,
                            ]);
                            $this->success[] = $this->trans('Your password has been successfully reset and a confirmation has been sent to your email address: %s', [$customer->email], 'Shop.Notifications.Success');
                            $this->context->updateCustomer($customer);
                            $this->redirectWithNotifications($this->context->link->getPageLink('my-account'));
                        } else {
                            $this->errors[] = $this->trans('An error occurred while sending the email.', [], 'Shop.Notifications.Error');
                        }
                    } else {
                        $this->errors[] = $this->trans('An error occurred with your account, which prevents us from updating the new password. Please report this issue using the contact form.', [], 'Shop.Notifications.Error');
                    }
                }
            }
        } else {
            $this->errors[] = $this->trans('We cannot regenerate your password with the data you\'ve submitted', [], 'Shop.Notifications.Error');
        }
    }

    /**
     * @return bool
     */
    public function display()
    {
        $this->context->smarty->assign(
            [
                'layout' => $this->getLayout(),
                'stylesheets' => $this->getStylesheets(),
                'javascript' => $this->getJavascript(),
                'js_custom_vars' => Media::getJsDef(),
                'errors' => $this->getErrors(),
                'successes' => $this->getSuccesses(),
            ]
        );

        $this->smartyOutputContent($this->template);

        return true;
    }

    /**
     * @return array
     */
    protected function getErrors()
    {
        $notifications = $this->prepareNotifications();

        $errors = [];
        if (array_key_exists('error', $notifications)) {
            $errors = $notifications['error'];
        }

        return $errors;
    }

    /**
     * @return array
     */
    protected function getSuccesses()
    {
        $notifications = $this->prepareNotifications();

        $successes = [];

        if (array_key_exists('success', $notifications)) {
            $successes = $notifications['success'];
        }

        return $successes;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Reset your password', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('password'),
        ];

        return $breadcrumb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalURL()
    {
        return $this->context->link->getPageLink('password');
    }
}
