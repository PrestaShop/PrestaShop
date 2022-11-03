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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    public function initContent()
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

    public function getBreadcrumbLinks()
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
