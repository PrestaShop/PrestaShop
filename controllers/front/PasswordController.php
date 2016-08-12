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

use PrestaShop\PrestaShop\Core\Crypto\Hashing;

class PasswordControllerCore extends FrontController
{
    public $php_self = 'password';
    public $auth = false;

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $this->setTemplate('customer/password-email');

        if (Tools::isSubmit('email')) {
            $this->sendRenewPasswordLink();
        } elseif (($token = Tools::getValue('token')) && ($id_customer = (int)Tools::getValue('id_customer'))) {
            $this->changePassword();
        } elseif (Tools::getValue('token') || Tools::getValue('id_customer')) {
            $this->errors[] = $this->trans('We cannot regenerate your password with the data you\'ve submitted', array(), 'Shop.Notifications.Error');
        }
    }

    protected function sendRenewPasswordLink()
    {
        if (!($email = trim(Tools::getValue('email'))) || !Validate::isEmail($email)) {
            $this->errors[] = $this->trans('Invalid email address.', array(), 'Shop.Notifications.Error');
        } else {
            $customer = new Customer();
            $customer->getByEmail($email);
            if (!Validate::isLoadedObject($customer)) {
                $this->errors[] = $this->trans('There is no account registered for this email address.', array(), 'Shop.Notifications.Error');
            } elseif (!$customer->active) {
                $this->errors[] = $this->trans('You cannot regenerate the password for this account.', array(), 'Shop.Notifications.Error');
            } elseif ((strtotime($customer->last_passwd_gen.'+'.($min_time = (int)Configuration::get('PS_PASSWD_TIME_FRONT')).' minutes') - time()) > 0) {
                $this->errors[] = $this->trans('You can regenerate your password only every %d minute(s)', array((int) $min_time), 'Shop.Notifications.Error');
            } else {
                if (!$customer->hasRecentResetPasswordToken()) {
                    $customer->stampResetPasswordToken();
                    $customer->update();
                }

                $mail_params = [
                    '{email}' => $customer->email,
                    '{lastname}' => $customer->lastname,
                    '{firstname}' => $customer->firstname,
                    '{url}' => $this->context->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id.'&reset_token='.$customer->reset_password_token)
                ];

                if (Mail::Send($this->context->language->id, 'password_query', Mail::l('Password query confirmation'), $mail_params, $customer->email, $customer->firstname.' '.$customer->lastname)) {
                    $this->success[] = $this->trans('A link to reset your password has been sent to your address: %s', array($customer->email), 'Shop.Notifications.Success');
                    $this->setTemplate('customer/password-infos');
                } else {
                    $this->errors[] = $this->trans('An error occurred while sending the email.', array(), 'Shop.Notifications.Error');
                }
            }
        }
    }

    protected function changePassword()
    {
        $token = Tools::getValue('token');
        $id_customer = (int)Tools::getValue('id_customer');
        if ($email = Db::getInstance()->getValue('SELECT `email` FROM '._DB_PREFIX_.'customer c WHERE c.`secure_key` = \''.pSQL($token).'\' AND c.id_customer = '.$id_customer)) {
            $customer = new Customer();
            $customer->getByEmail($email);

            if (!Validate::isLoadedObject($customer)) {
                $this->errors[] = $this->trans('Customer account not found', array(), 'Shop.Notifications.Error');
            } elseif (!$customer->active) {
                $this->errors[] = $this->trans('You cannot regenerate the password for this account.', array(), 'Shop.Notifications.Error');
            }

            // Case if both password params not posted or different, then "change password" form is not POSTED, show it.
            if (!(Tools::isSubmit('passwd'))
                || !(Tools::isSubmit('confirmation'))
                || ($passwd = Tools::getValue('passwd')) !== ($confirmation = Tools::getValue('confirmation'))
                || !Validate::isPasswd($passwd) || !Validate::isPasswd($confirmation)) {
                // Check if passwords are here anyway, BUT does not match the password validation format
                if (Tools::isSubmit('passwd') || Tools::isSubmit('confirmation')) {
                    $this->errors[] = $this->trans('The password and its confirmation do not match.', array(), 'Shop.Notifications.Error');
                }

                $this->context->smarty->assign([
                    'customer_email' => $customer->email,
                    'customer_token' => $token,
                    'id_customer' => $id_customer,
                    'reset_token' => Tools::getValue('reset_token'),
                ]);

                $this->setTemplate('customer/password-new');
            } else {
                // Both password fields posted. Check if all is right and store new password properly.
                if (!Tools::getValue('reset_token') || (strtotime($customer->last_passwd_gen.'+'.(int)Configuration::get('PS_PASSWD_TIME_FRONT').' minutes') - time()) > 0) {
                    Tools::redirect('index.php?controller=authentication&error_regen_pwd');
                } else {
                    // To update password, we must have the temporary reset token that matches.
                    if ($customer->getValidResetPasswordToken() !== Tools::getValue('reset_token')) {
                        $this->errors[] = $this->trans('The password change request expired. You should ask for a new one.', array(), 'Shop.Notifications.Error');
                    } else {
                        try {
                            $crypto = new Hashing();
                        } catch (\PrestaShop\PrestaShop\Adapter\CoreException $e) {
                            $this->errors[] = $this->trans('An error occurred with your account, which prevents us from updating the new password. Please report this issue using the contact form.', array(), 'Shop.Notifications.Error');
                            return false;
                        }

                        $customer->passwd = $crypto->encrypt($password = Tools::getValue('passwd'), _COOKIE_KEY_);
                        $customer->last_passwd_gen = date('Y-m-d H:i:s', time());

                        if ($customer->update()) {
                            Hook::exec('actionPasswordRenew', array('customer' => $customer, 'password' => $password));
                            $customer->removeResetPasswordToken();
                            $customer->update();

                            $mail_params = [
                                '{email}' => $customer->email,
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname
                            ];

                            if (Mail::Send($this->context->language->id, 'password', Mail::l('Your new password'), $mail_params, $customer->email, $customer->firstname.' '.$customer->lastname)) {
                                $this->context->smarty->assign([
                                    'customer_email' => $customer->email
                                ]);
                                $this->success[] = $this->trans('Your password has been successfully reset and a confirmation has been sent to your email address: %s', array($customer->email), 'Shop.Notifications.Success');
                                $this->context->updateCustomer($customer);
                                $this->redirectWithNotifications('index.php?controller=my-account');
                            } else {
                                $this->errors[] = $this->trans('An error occurred while sending the email.', array(), 'Shop.Notifications.Error');
                            }
                        } else {
                            $this->errors[] = $this->trans('An error occurred with your account, which prevents us from updating the new password. Please report this issue using the contact form.', array(), 'Shop.Notifications.Error');
                        }
                    }
                }
            }
        } else {
            $this->errors[] = $this->trans('We cannot regenerate your password with the data you\'ve submitted', array(), 'Shop.Notifications.Error');
        }
    }
}
