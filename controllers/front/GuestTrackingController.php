<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderPresenter;

class GuestTrackingControllerCore extends FrontController
{
    public $ssl = true;
    public $auth = false;
    public $php_self = 'guest-tracking';
    protected $order;

    /**
     * Initialize guest tracking controller.
     *
     * @see FrontController::init()
     */
    public function init()
    {
        if ($this->context->customer->isLogged()) {
            Tools::redirect('history.php');
        }

        parent::init();
    }

    /**
     * Start forms process.
     *
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        $order_reference = current(explode('#', Tools::getValue('order_reference')));
        $email = Tools::getValue('email');

        if (!$email && !$order_reference) {
            return;
        } elseif (!$email || !$order_reference) {
            $this->errors[] = $this->getTranslator()->trans(
                'Please provide the required information',
                array(),
                'Shop.Notifications.Error'
            );

            return;
        }

        $isCustomer = Customer::customerExists($email, false, true);
        if ($isCustomer) {
            $this->info[] = $this->trans(
                'Please log in to your customer account to view the order',
                array(),
                'Shop.Notifications.Info'
            );
            $this->redirectWithNotifications($this->context->link->getPageLink('history'));
        } else {
            $this->order = Order::getByReferenceAndEmail($order_reference, $email);
            if (!Validate::isLoadedObject($this->order)) {
                $this->errors[] = $this->getTranslator()->trans(
                    'We couldn\'t find your order with the information provided, please try again',
                    array(),
                    'Shop.Notifications.Error'
                );
            }
        }

        if (Tools::isSubmit('submitTransformGuestToCustomer') && Tools::getValue('password')) {
            $customer = new Customer((int) $this->order->id_customer);
            $password = Tools::getValue('password');

            if (strlen($password) < Validate::PASSWORD_LENGTH) {
                $this->errors[] = $this->trans(
                    'Your password must be at least %min% characters long.',
                    array('%min%' => Validate::PASSWORD_LENGTH),
                    'Shop.Forms.Help'
                );
            } elseif ($customer->transformToCustomer($this->context->language->id, $password)) {
                $this->success[] = $this->trans(
                    'Your guest account has been successfully transformed into a customer account. You can now log in as a registered shopper.',
                    array(),
                    'Shop.Notifications.Success'
                );
            } else {
                $this->success[] = $this->trans(
                    'An unexpected error occurred while creating your account.',
                    array(),
                    'Shop.Notifications.Error'
                );
            }
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (!Validate::isLoadedObject($this->order)) {
            return $this->setTemplate('customer/guest-login');
        }

        if ((int) $this->order->isReturnable()) {
            $this->info[] = $this->trans(
                'You cannot return merchandise with a guest account.',
                array(),
                'Shop.Notifications.Warning'
            );
        }

        $presented_order = (new OrderPresenter())->present($this->order);

        $this->context->smarty->assign(array(
            'order' => $presented_order,
            'guest_email' => Tools::getValue('email'),
            'HOOK_DISPLAYORDERDETAIL' => Hook::exec('displayOrderDetail', array('order' => $this->order)),
        ));

        return $this->setTemplate('customer/guest-tracking');
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumbLinks = parent::getBreadcrumbLinks();

        $breadcrumbLinks['links'][] = array(
            'title' => $this->getTranslator()->trans('Guest order tracking', array(), 'Shop.Theme.Checkout'),
            'url' => '#',
        );

        return $breadcrumbLinks;
    }
}
