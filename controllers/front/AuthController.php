<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class AuthControllerCore extends FrontController
{
    /** @var bool */
    public $ssl = true;
    /** @var string */
    public $php_self = 'authentication';
    /** @var bool */
    public $auth = false;

    /**
     * Check if the controller is available for the current user/visitor.
     *
     * @see Controller::checkAccess()
     *
     * @return bool
     */
    public function checkAccess(): bool
    {
        if ($this->context->customer->isLogged() && !$this->ajax) {
            $this->redirect_after = $this->authRedirection ? urlencode($this->authRedirection) : 'my-account';
            $this->redirect();
        }

        return parent::checkAccess();
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        if (Tools::isSubmit('create_account')) {
            $this->redirectWithNotifications('registration');
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
                $this->redirectWithNotifications($back);
            }

            // Well we're not redirecting to a URL,
            // so...
            if ($this->authRedirection) {
                // We may need to go there if defined
                $this->redirectWithNotifications($this->authRedirection);
            }

            // go home
            $this->redirectWithNotifications(__PS_BASE_URI__);
        }
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Log in to your account', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('authentication'),
        ];

        return $breadcrumb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalURL(): string
    {
        return $this->context->link->getPageLink('authentication');
    }
}
