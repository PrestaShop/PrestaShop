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

class IdentityControllerCore extends FrontController
{
    public $auth = true;
    public $php_self = 'identity';
    public $authRedirection = 'identity';
    public $ssl = true;

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $should_redirect = false;
        parent::initContent();

        $customer_form = $this->makeCustomerForm();

        $customer_form->getFormatter()
            ->setAskForNewPassword(true)
            ->setPasswordRequired(true)
        ;

        if (Tools::isSubmit('submitCreate')) {
            $customer_form->fillWith(Tools::getAllValues());
            if ($customer_form->submit()) {
                $this->success[] = $this->l('Information successfully updated.');
                $should_redirect = true;
            } else {
                $this->errors[] = $this->l('Could not update your information, please check your data.');
            }
        } else {
            $customer_form->fillFromCustomer(
                $this->context->customer
            );
        }

        $this->context->smarty->assign([
            'customer_form' => $customer_form->getProxy()
        ]);

        if ($should_redirect) {
            $this->redirectWithNotifications($this->getCurrentURL());
        }

        $this->setTemplate('customer/identity.tpl');
    }

    public function getBreadcrumb()
    {
        $breadcrumb = parent::getBreadcrumb();

        $breadcrumb[] = $this->addMyAccountToBreadcrumb();

        return $breadcrumb;
    }
}
