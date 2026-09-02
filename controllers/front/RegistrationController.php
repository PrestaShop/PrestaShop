<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class RegistrationControllerCore extends FrontController
{
    /** @var bool */
    public $ssl = true;
    /** @var string */
    public $php_self = 'registration';
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
        // If the customer is already logged and he got here by 'accident', we will redirect him away
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
        $register_form = $this
            ->makeCustomerForm()
            ->setGuestAllowed(false)
            ->fillWith(Tools::getAllValues());

        // If registration form was submitted
        if (Tools::isSubmit('submitCreate')) {
            $hookResult = array_reduce(
                Hook::exec('actionSubmitAccountBefore', [], null, true),
                function ($carry, $item) {
                    return $carry && $item;
                },
                true
            );

            // If no problem occured in the hook, let's get the user redirected
            if ($hookResult && $register_form->submit() && !$this->ajax) {
                // First option - redirect the customer to desired URL specified in 'back' parameter
                // Before that, we need to check if 'back' is legit URL that is on OUR domain, with the right protocol
                $back = rawurldecode(Tools::getValue('back'));
                if (Tools::urlBelongsToShop($back)) {
                    $this->redirectWithNotifications($back);
                }

                // Second option - we will redirect him to authRedirection if set
                if ($this->authRedirection) {
                    $this->redirectWithNotifications($this->authRedirection);
                }

                // Third option - we will redirect him to home URL
                $this->redirectWithNotifications(__PS_BASE_URI__);
            }
        }

        $this->context->smarty->assign([
            'register_form' => $register_form->getProxy(),
            'hook_create_account_top' => Hook::exec('displayCustomerAccountFormTop'),
        ]);
        $this->setTemplate('customer/registration');

        parent::initContent();
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Create an account', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('registration'),
        ];

        return $breadcrumb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalURL(): string
    {
        return $this->context->link->getPageLink('registration');
    }
}
