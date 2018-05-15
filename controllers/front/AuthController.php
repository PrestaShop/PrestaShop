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

class AuthControllerCore extends FrontController
{
    public $ssl = true;
    public $php_self = 'authentication';
    public $auth = false;

    public function checkAccess()
    {
        if ($this->context->customer->isLogged() && !$this->ajax) {
            $this->redirect_after = ($this->authRedirection) ? urlencode($this->authRedirection) : 'my-account';
            $this->redirect();
        }

        return parent::checkAccess();
    }

    public function initContent()
    {
        $should_redirect = false;

        if (Tools::isSubmit('submitCreate') || Tools::isSubmit('create_account')) {
            $register_form = $this
                ->makeCustomerForm()
                ->setGuestAllowed(false)
                ->fillWith(Tools::getAllValues())
            ;

            if (Tools::isSubmit('submitCreate')) {
                $hookResult = array_reduce(
                    Hook::exec('actionSubmitAccountBefore', array(), null, true),
                    function ($carry, $item) {
                        return $carry && $item;
                    },
                    true
                );
                if ($hookResult && $register_form->submit()) {
                    $should_redirect = true;
                }
            }

            $this->context->smarty->assign([
                'register_form'  => $register_form->getProxy(),
                'hook_create_account_top' => Hook::exec('displayCustomerAccountFormTop')
            ]);
            $this->setTemplate('customer/registration');
        } else {
            $login_form = $this->makeLoginForm()->fillWith(
                Tools::getAllValues()
            );

            if (Tools::isSubmit('submitLogin')) {
                if ($login_form->submit()) {
                    $should_redirect = true;
                }
            }

            $this->context->smarty->assign([
                'login_form' => $login_form->getProxy()
            ]);
            $this->setTemplate('customer/authentication');
        }

        parent::initContent();

        if ($should_redirect && !$this->ajax) {
            $back = urldecode(Tools::getValue('back'));

            if (Tools::urlBelongsToShop($back)) {
                // Checks to see if "back" is a fully qualified
                // URL that is on OUR domain, with the right protocol
                return $this->redirectWithNotifications($back);
            }

            // Well we're not redirecting to a URL,
            // so...
            if ($this->authRedirection) {
                // We may need to go there if defined
                return $this->redirectWithNotifications($this->authRedirection);
            }

            // go home
            return $this->redirectWithNotifications(__PS_BASE_URI__);
        }
    }
}
