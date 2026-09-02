<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class IdentityControllerCore extends FrontController
{
    /** @var bool */
    public $auth = true;
    /** @var string */
    public $php_self = 'identity';
    /** @var string */
    public $authRedirection = 'identity';
    /** @var bool */
    public $ssl = true;

    public $passwordRequired = true;

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent(): void
    {
        $should_redirect = false;

        $customer_form = $this->makeCustomerForm()->setPasswordRequired($this->passwordRequired);
        $customer = new Customer();

        $customer_form->getFormatter()
            ->setAskForNewPassword(true)
            ->setAskForPassword($this->passwordRequired)
            ->setPasswordRequired($this->passwordRequired)
            ->setPartnerOptinRequired($customer->isFieldRequired('optin'));

        if (Tools::isSubmit('submitCreate')) {
            $customer_form->fillWith(Tools::getAllValues());
            if ($customer_form->submit()) {
                $this->success[] = $this->trans('Information successfully updated.', [], 'Shop.Notifications.Success');
                $should_redirect = true;
            } else {
                $this->errors[] = $this->trans('Could not update your information, please check your data.', [], 'Shop.Notifications.Error');
            }
        } else {
            $customer_form->fillFromCustomer(
                $this->context->customer
            );
        }

        $this->context->smarty->assign([
            'customer_form' => $customer_form->getProxy(),
        ]);

        if ($should_redirect) {
            $this->redirectWithNotifications($this->getCurrentURL());
        }

        parent::initContent();
        $this->setTemplate('customer/identity');
    }

    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();

        $breadcrumb['links'][] = [
            'title' => $this->trans('Your personal information', [], 'Shop.Theme.Customeraccount'),
            'url' => $this->context->link->getPageLink('identity'),
        ];

        return $breadcrumb;
    }
}
