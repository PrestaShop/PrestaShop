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
        if (Tools::isSubmit('email')) {
            if (!($email = trim(Tools::getValue('email'))) || !Validate::isEmail($email)) {
                $this->errors[] = Tools::displayError('Invalid email address.');
            } else {
                $customer = new Customer();
                $customer->getByemail($email);
                if (!Validate::isLoadedObject($customer)) {
                    $this->errors[] = Tools::displayError('There is no account registered for this email address.');
                } elseif (!$customer->active) {
                    $this->errors[] = Tools::displayError('You cannot regenerate the password for this account.');
                } elseif ((strtotime($customer->last_passwd_gen.'+'.($min_time = (int)Configuration::get('PS_PASSWD_TIME_FRONT')).' minutes') - time()) > 0) {
                    $this->errors[] = sprintf(Tools::displayError('You can regenerate your password only every %d minute(s)'), (int)$min_time);
                } else {
                    $resetToken = new ResetTokenCore();

                    // Checks if a 'too young' ResetToken exists for this customer
                    $resetToken->getMostRecentTokenForCustomer($customer->id, strtotime('-'.($min_time = (int)Configuration::get('PS_PASSWD_TIME_FRONT')).' minutes'));
                    if (Validate::isLoadedObject($resetToken)) {
                        $this->errors[] = sprintf(Tools::displayError('You can regenerate your password only every %d minute(s)'), (int)$min_time);
                        // TODO or maybe keep existing token and send it by mail again?
                    } else {
                        // Create a new resetToken with unique link to change password
                        $resetToken->id_customer = (int)$customer->id;
                        $resetToken->secure_key = $customer->secure_key;
                        $resetToken->stampToken(); // fill unique sha1 token and validity date

                        $mail_params = array(
                            '{email}' => $customer->email,
                            '{lastname}' => $customer->lastname,
                            '{firstname}' => $customer->firstname,
                            '{url}' => $this->context->link->getPageLink('password', true, null, 'token='.$customer->secure_key.'&id_customer='.(int)$customer->id.'&reset_token='.$resetToken->unique_token)
                        );
                        if (Mail::Send($this->context->language->id, 'password_query', Mail::l('Password query confirmation'), $mail_params, $customer->email, $customer->firstname.' '.$customer->lastname)
                                && $resetToken->add()) {
                            $this->context->smarty->assign(array('confirmation' => 2, 'customer_email' => $customer->email));
                        } else {
                            $this->errors[] = Tools::displayError('An error occurred while sending the email.');
                        }
                    }
                }
            }
        } elseif (($token = Tools::getValue('token')) && ($id_customer = (int)Tools::getValue('id_customer'))) {
            $email = Db::getInstance()->getValue('SELECT `email` FROM '._DB_PREFIX_.'customer c WHERE c.`secure_key` = \''.pSQL($token).'\' AND c.id_customer = '.(int)$id_customer);
            if ($email) {
                $customer = new Customer();
                $customer->getByemail($email);
                if (!Validate::isLoadedObject($customer)) {
                    $this->errors[] = Tools::displayError('Customer account not found');
                } elseif (!$customer->active) {
                    $this->errors[] = Tools::displayError('You cannot regenerate the password for this account.');
                }

                // Case if both password params not posted, then "change password" form is not POSTED, show it.
                if (!(Tools::isSubmit('passwd')) || !(Tools::isSubmit('confirmation')) || Tools::getValue('passwd') != Tools::getValue('confirmation')) {
                    $passwd = Tools::getValue('passwd');
                    $confirmation = Tools::getValue('confirmation');

                    // Check equality between both passwords fields
                    if ((Tools::isSubmit($passwd) || Tools::isSubmit($confirmation)) &&
                        (!Validate::isPasswd($passwd) || !Validate::isPasswd($confirmation) || $passwd !== $confirmation)) {
                        $this->errors[] = Tools::displayError('The password and confirmation do not match.');
                    }

                    $this->addJS(_PS_JS_DIR_.'validate.js');
                    $this->context->smarty->assign(array(
                        'confirmation' => 1,
                        'customer_email' => $customer->email,
                        'customer_token' => $token,
                        'id_customer' => $id_customer,
                        'reset_token' => Tools::getValue('reset_token'),
                    ));
                } else {
                    // Both password fields posted. Check if all is right and store new password properly.
                    if ((strtotime($customer->last_passwd_gen.'+'.(int)Configuration::get('PS_PASSWD_TIME_FRONT').' minutes') - time()) > 0) {
                        Tools::redirect('index.php?controller=authentication&error_regen_pwd');
                    } else {
                        // To update password, we must have the temporary reset token that matches.
                        if (!($reset_token = ResetToken::getValidByIdCustomerAndUniqueToken($id_customer, Tools::getValue('reset_token')))) {
                            $this->errors[] = Tools::displayError('The password change request expired. You should ask for a new one.');
                        } else {
                            $customer->passwd = Tools::encrypt($password = Tools::getValue('passwd'));
                            $customer->last_passwd_gen = date('Y-m-d H:i:s', time());
                            if ($customer->update()) {
                                Hook::exec('actionPasswordRenew', array('customer' => $customer, 'password' => $password));
                                $reset_token->delete(); // Delete temporary reset token
                                $mail_params = array(
                                    '{email}' => $customer->email,
                                    '{lastname}' => $customer->lastname,
                                    '{firstname}' => $customer->firstname
                                );
                                if (Mail::Send($this->context->language->id, 'password', Mail::l('Your new password'), $mail_params, $customer->email, $customer->firstname.' '.$customer->lastname)) {
                                    $this->context->smarty->assign(array('confirmation' => 3, 'customer_email' => $customer->email));
                                } else {
                                    $this->errors[] = Tools::displayError('An error occurred while sending the email.');
                                }
                            } else {
                                $this->errors[] = Tools::displayError('An error occurred with your account, which prevents us from updating the new password. Please report this issue using the contact form.');
                            }
                        }
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('We cannot regenerate your password with the data you\'ve submitted.');
            }
        } elseif (Tools::getValue('token') || Tools::getValue('id_customer')) {
            $this->errors[] = Tools::displayError('We cannot regenerate your password with the data you\'ve submitted.');
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate(_PS_THEME_DIR_.'password.tpl');
    }
}
