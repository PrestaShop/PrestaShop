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
class AuthControllerCore extends FrontController
{
    /** @var bool */
    public $ssl = true;
    /** @var string */
    public $php_self = 'authentication';
    /** @var bool */
    public $auth = false;

    public function checkAccess()
    {
        if ($this->context->customer->isLogged() && !$this->ajax) {
            $this->redirect_after = $this->authRedirection ? urlencode($this->authRedirection) : 'my-account';
            $this->redirect();
        }

        return parent::checkAccess();
    }

    public function initContent()
    {
        if (Tools::isSubmit('create_account')) {
            return $this->redirectWithNotifications('registration');
        }

        $should_redirect = false;

        $login_form = $this->makeLoginForm()->fillWith(
            Tools::getAllValues()
        );

        if (Tools::isSubmit('submitLogin')) {
            if ($login_form->submit()) {
                $should_redirect = true;
            }
        }

        $this->context->smarty->assign([
            'login_form' => $login_form->getProxy(),
        ]);
        $this->setTemplate('customer/authentication');

        parent::initContent();

        if ($should_redirect && !$this->ajax) {
            $back = rawurldecode(Tools::getValue('back'));

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

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Log in to your account', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('authentication'),
        ];

        return $breadcrumb;
    }
}
