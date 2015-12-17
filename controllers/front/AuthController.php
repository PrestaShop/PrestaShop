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

    private $loginForm;
    private $registerForm;

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::getValue('create_account') || Tools::isSubmit('submitCreate')) {
            $this->registerForm = $this
                ->getRegisterForm()
                ->setGuestAllowed(false)
                ->fillWith(Tools::getAllValues())
            ;
        } else {
            $this->loginForm = $this
                ->getLoginForm()
                ->fillWith(Tools::getAllValues())
            ;
        }

        if (Tools::getValue('create_account') || Tools::isSubmit('submitCreate')) {
            $this->context->smarty->assign([
                'rendered_register_form'  => $this->registerForm->render(),
                'hook_create_account_top' => Hook::exec('displayCustomerAccountFormTop')
            ]);
            $this->setTemplate('customer/registration.tpl');
            if (Tools::isSubmit('submitCreate')) {
                $this->processSubmitCreate();
            }
        } else {
            if (Tools::isSubmit('SubmitLogin')) {
                $this->processSubmitLogin();
            }
            $this->context->smarty->assign([
                'rendered_login_form' => $this->loginForm->render()
            ]);
            $this->setTemplate('customer/authentication.tpl');
        }
    }

    /**
     * Process login
     */
    protected function processSubmitLogin()
    {
        $this->loginForm->submit();

        if (!$this->ajax && !$this->loginForm->hasErrors()) {
            $back = Tools::getValue('back', 'my-account');

            if ($back == Tools::secureReferrer($back)) {
                $this->redirectWithNotifications(html_entity_decode($back));
            } else {
                $this->redirectWithNotifications('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : $back));
            }
        }
    }

    /**
    * Process submit on a creation
    */
    protected function processSubmitCreate()
    {
        $this->registerForm->submit();

        if (!$this->ajax && !$this->registerForm->hasErrors()) {
            if (($back = Tools::getValue('back')) && $back == Tools::secureReferrer($back)) {
                $this->redirectWithNotifications(html_entity_decode($back));
            } else {
                $this->redirectWithNotifications('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
            }
        }
    }
}
